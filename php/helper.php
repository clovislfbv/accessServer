<?php
    if (!isset($_SESSION)){
        session_start();
    }
    
    if (isset($_POST["action"])){
        switch ($_POST["action"]) {
            case "cd":
                cd();
                break;
            case "reset_session":
                reset_session();
                break;
            case "mkdir":
                make_dir();
                break;
            case "rm":
                remove();
                break;
            case "receive_file":
                receive_file();
                break;
            case "empty_downloaded_files":
                empty_downloaded_files();
                break;
            case "set_pubkey":
                set_pubkey();
                break;
            case "set_privkey":
                set_privkey();
                break;
            case "git_pull":
                git_pull();
                break;
            case "empty_keys_files":
                empty_keys_files();
                break;
            case "ls_extensions":
                ls_extensions();
                break;
            case "set_files_details":
                $_SESSION['files-details'] = $_POST['status'];
                break;
        }
    }

    if (isset($_FILES['file0'])){
        send_files();
    }

    if (isset($_FILES['keyfile'])){
        dl_key_file();
    }

    function connect(){
        $host = $_SESSION['host'];
        $user = $_SESSION['user'];
        $port = $_SESSION['port'];

        $connection = ssh2_connect($host, $port);

        if (isset($_SESSION["password"])){
            $password = $_SESSION['password'];
            ssh2_auth_password($connection, $user, $password);
        } else {
            $pubfile = $_SESSION["pubfile"];
            $privfile = $_SESSION["privfile"];

            if (isset($_SESSION['password_key'])){
                $password_key = $_SESSION['password_key'];
                ssh2_auth_pubkey_file($connection, $user, $pubfile, $privfile, $password_key);
            } else {
                ssh2_auth_pubkey_file($connection, $user, $pubfile, $privfile);
            }
        }

        return $connection;
    }

    function cd(){
        $folder = $_POST["folder"];
        $folder = str_replace(" ", "\ ", $folder);
        $current = $_SESSION['current'];
        $_SESSION['current'] = $current . $folder . "/";
    }

    function reset_session(){
        if (isset($_SESSION['current'])){
            unset($_SESSION['current']);
            unset($_SESSION['host']);
            unset($_SESSION['user']);
            unset($_SESSION['password']);
            unset($_SESSION['port']);
            unset($_SESSION['pubfile']);
            unset($_SESSION['privfile']);
            unset($_SESSION['password_key']);
        }
    }

    function make_dir(){
        $folder = $_POST["folder"];
        $current = $_SESSION['current'];
        $command = 'cd ' . $current . ' && mkdir "' . $folder . '"';
        $connection = connect();
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);
    }

    function remove(){
        $folder = $_POST["folder"];
        $current = $_SESSION['current'];
        $user = $_SESSION['user'];
        $connection = connect();
        $command = 'cd ' . $current . ' && pwd';
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);

        $output = str_replace("/home/" . $user, ".", $output);
        $output[strlen($output) - 1] = '/';
        $output = str_replace(' ', '\ ', $output);
        $_SESSION['current'] = $output;

        $current = $_SESSION['current'];
        $command2 = "cd " . $current . " && rm -rf '" . $folder . "'";
        $stream2 = ssh2_exec($connection, $command2);
        stream_set_blocking($stream2, true);
        $stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO);
        $output2 = stream_get_contents($stream_out2);
    }

    function send_files(){
        exec("rm ../Downloads/*");
        $response = array('success' => false, 'error' => '');

        for ($i = 0; isset($_FILES['file' . $i]); $i++) {
            $file = $_FILES['file' . $i];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $response['error'] = 'Upload error: ' . $file['error'];
                continue;
            }

            // Move the uploaded file to a directory on your server
            $dir = '../Downloads/';
            if (!is_dir($dir)) {
                exec("mkdir " . $dir);
            }

            $fileWithoutSpaces = str_replace(' ', '_', $file['name']);
            $fileWithoutDashes = str_replace('-', '_', $fileWithoutSpaces);
            $destination = $dir . $fileWithoutDashes;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $files = scandir($dir);

                foreach ($files as $file) {
                    $connection = connect();
                    if ($file === '.' || $file === '..') {
                        continue;  // Skip current directory and parent directory
                    }
                    $_SESSION['current'] = str_replace('\ ', ' ', $_SESSION['current']);
                    $remoteFilePath = $_SESSION['current'] . $file;
                    if (ssh2_scp_send($connection, $dir . $file, $remoteFilePath, 0644)) {
                        $response['success'] = true;
                    } else {
                        $response['error'] = 'Failed to send file via SCP';
                        error_log($response['error']);
                    }
                    ssh2_exec($connection, 'exit');
                    ssh2_disconnect($connection);
                    $_SESSION['current'] = str_replace(' ', '\ ', $_SESSION['current']);
                }
            } else {
                $response['error'] = 'Failed to move uploaded file';
                error_log($response['error']);
            }

            unset($_FILES['file' . $i]);
        }

        echo json_encode($response);
    }

    function dl_key_file(){
        $keyfile = $_FILES['keyfile'];

        // Check for upload errors
        if ($keyfile['error'] !== UPLOAD_ERR_OK) {
            echo 'Upload error: ' . $keyfile['error'];
        }

        $dir = '../Keys/';
        if (!is_dir($dir)) {
            exec("mkdir " . $dir);
        }

        move_uploaded_file($keyfile['tmp_name'], $dir . $keyfile['name']);

        echo $dir . $keyfile['name'];
    }

    function set_pubkey(){
        $pubfile = $_POST['file'];
        $dir = '../Keys/';
        //exec("chmod 600 " . $dir . $pubfile);
        $_SESSION['pubfile'] = $dir . $pubfile;
    }

    function set_privkey(){
        $privfile = $_POST['file'];
        $dir = '../Keys/';
        //exec("chmod 600 " . $dir . $privfile);
        $_SESSION['privfile'] = $dir . $privfile;
    }

    function receive_file(){
        $connection = connect();

        $file = $_POST["file"];
        $_SESSION['current'] = str_replace('\ ', ' ', $_SESSION['current']);
        $remoteFile = $_SESSION['current'] . $file;
        $_SESSION['current'] = str_replace(' ', '\ ', $_SESSION['current']);
        $dir = "../remoteFiles/";
        $localFile = $dir . $file;

        if (!is_dir($dir)) {
            exec("mkdir " . $dir);
        }

        if (!file_exists($localFile)) {
            $stream = ssh2_exec($connection, '[ -d "' . $remoteFile . '" ] && echo "directory" || echo "file"');
            stream_set_blocking($stream, true);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $output = stream_get_contents($stream_out);

            if (trim($output) === 'file') {
                ssh2_scp_recv($connection, $remoteFile, $localFile);
            }
        }
    }

    function ls_extensions(){
        $extensions = $_POST["extensions"];
        $current = $_SESSION['current'];
        $value = "";
        for ($i = 0; $i < count($extensions); $i++){
            $value .= "*." . $extensions[$i] . " ";
        }
        $command = 'cd ' . $current . ' && ls ' . $value;
        $stream = ssh2_exec(connect(), $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);
        echo json_encode($output);
    }

    function git_pull(){
        $connection = connect();
        $folder = $_POST["folder"];
        $current = $_SESSION['current'];
        $command = 'cd ' . $current . $folder . ' && git config --get remote.origin.url';
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);

        $output = str_replace("git@", "https://", $output);
        $output = str_replace(".com:", ".com/", $output);
        
        $command = 'cd ' . $current . $folder . ' && git pull';
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);

        if ($output)
        {
            echo $output;
        }

        $stream_in = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
        $output = stream_get_contents($stream_in);

        if ($output)
        {
            echo $output;
        }
    }

    function empty_downloaded_files(){
        exec("rm -rf ../remoteFiles/");
    }

    function empty_keys_files(){
        exec("rm ../Keys/*");
    }

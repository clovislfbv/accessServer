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
            case "receive_directory":
                receive_folder();
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
            case "set_hidden_files":
                $_SESSION['hidden-files'] = $_POST['status'];
                break;
            case "get_end_time_from_path":
                get_end_time_from_path();
                break;
            case "remove_local_file":
                remove_local_files();
                break;
            case "folder_to_file":
                folder_to_file();
                break;
            case "update_end_time":
                update_end_time();
                break;
            case "set_python_status":
                $_SESSION['python-status'] = $_POST['status'];
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
        exec("rm ../Downloads/.* ../Downloads/*");
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
        include_once("conn.php");

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
    
        // Get the size of the remote file
        $stream = ssh2_exec($connection, 'stat -c%s "' . $remoteFile . '"');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $remoteFileSize = trim(stream_get_contents($stream_out));
    
        // Check if the local file exists and get its size
        $localFileSize = file_exists($localFile) ? filesize($localFile) : -1;
    
        // Compare the sizes and proceed with the download if they are different
        if ($localFileSize != $remoteFileSize) {
            $stream = ssh2_exec($connection, '[ -d "' . $remoteFile . '" ] && echo "directory" || echo "file"');
            stream_set_blocking($stream, true);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $output = stream_get_contents($stream_out);
    
            if (trim($output) === 'file') {
                ssh2_scp_recv($connection, $remoteFile, $localFile);

                $pwd = ssh2_exec($connection, 'cd ' . $_SESSION['current'] . ' && pwd');
                stream_set_blocking($pwd, true);
                $pwd_out = ssh2_fetch_stream($pwd, SSH2_STREAM_STDIO);
                $output = stream_get_contents($pwd_out);
                $output = str_replace("\n", "", $output);

                $query = "INSERT INTO files (user, server_ip, path, pwd, end_time, url) VALUES ('" . $_SESSION["user"] . "', '" . $_SESSION["host"] . "', '" . $localFile . "', '" . $output . "/" . $file . "', NOW() + INTERVAL 1 HOUR, '" . "https://access-server.ddns.net/remoteFiles/" . $file . "')";
                $conn->query($query);
                if ($conn->error) {
                    echo "Error: " . $conn->error;
                }

                if ($_SESSION["python-status"] == "false") {
                    $_SESSION["python-status"] = "true";
                    exec("python3 ../python/index.py " . $_SESSION["user"] . " " . $_SESSION["host"] . " > /dev/null 2>&1 &");
                }
            }
        }

        echo filesize($localFile);
    }

    function receive_folder(){
        include_once("conn.php");

        $connection = connect();
        $folder = $_POST["folder"];
        $current = $_SESSION['current'];
        $remoteDir = $current . $folder;
        $dir = "../remoteFiles/";
        $localDir = $dir . $folder;

        if (!is_dir($dir)) {
            exec("mkdir " . $dir);
        }
    
        // Create a temporary archive of the remote directory
        $remoteArchive = "/tmp/" . basename($remoteDir) . ".tar.gz";
        $command = "tar -czf $remoteArchive -C " . escapeshellarg(dirname($remoteDir)) . " " . escapeshellarg(basename($remoteDir));
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        stream_get_contents($stream); // Wait for the command to complete
    
        // Transfer the archive file
        $localArchive = $dir . "remote_dir.tar.gz";
        if (!ssh2_scp_recv($connection, $remoteArchive, $localArchive)) {
            echo "Failed to receive the archive file.";
            return;
        }

        $pwd = ssh2_exec($connection, 'cd ' . $_SESSION['current'] . ' && pwd');
        stream_set_blocking($pwd, true);
        $pwd_out = ssh2_fetch_stream($pwd, SSH2_STREAM_STDIO);
        $output = stream_get_contents($pwd_out);
        $output = str_replace("\n", "", $output);

        echo $output . "/" . $folder;

        $query = "INSERT INTO files (user, server_ip, path, pwd, end_time, url) VALUES ('" . $_SESSION["user"] . "', '" . $_SESSION["host"] . "', '" . $localDir . "', '" . $output . "/" . $folder . "', NOW() + INTERVAL 1 HOUR, " . "'https://access-server.ddns.net/remoteFiles/" . $folder . "/')";
        $conn->query($query);
        if ($conn->error) {
            echo "Error: " . $conn->error;
        }
    
        // Extract the archive locally
        try {
            $phar = new PharData($localArchive);
            $phar->decompress(); // Decompress the tar.gz file
            $phar->extractTo($dir); // Extract the contents
        } catch (Exception $e) {
            echo "Failed to extract the archive: ", $e->getMessage();
            return;
        }

        exec("chmod -R 755 " . escapeshellarg($localDir));
        exec("chown -R www-data:www-data " . escapeshellarg($localDir)); // Adjust 'www-data' to your web server user and group
    
        // Clean up
        unlink($dir . "remote_dir.tar"); // Remove the local archive file
        unlink($localArchive); // Remove the local archive file
        ssh2_exec($connection, "rm $remoteArchive"); // Remove the remote archive file

        if ($_SESSION["python-status"] == "false") {
            $_SESSION["python-status"] = "true";
            exec("python3 ../python/index.py " . $_SESSION["user"] . " " . $_SESSION["host"] . " > /dev/null 2>&1 &");
        }

        //exec("python3 ../python/index.py " . $_SESSION["user"] . " " . $_SESSION["host"]);
    
        echo "Directory received successfully";
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

    function remove_local_files() {
        include("conn.php");

        $query = "SELECT path FROM files WHERE end_time < NOW()";
        $result = $conn->query($query);

        // Check if the query executed successfully
        if (!$result) {
            echo "Query error: " . $conn->error . "<br>";
            return;
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $filePath = $row['path'];

                if (file_exists($filePath)) {
                    if (is_dir($filePath)) {
                        exec("rm -rf " . escapeshellarg($filePath));
                        $_SESSION['python-status'] = "false";
                    } else {
                        unlink($filePath);
                    }
                }
            }
        }

        $conn->query("DELETE FROM files WHERE end_time < NOW()");
    }

    function get_all_temp_files_from_user_and_server($user, $server_ip){
        include("conn.php");

        $query = "SELECT pwd, end_time, url FROM files WHERE user = '" . $user . "' AND server_ip = '" . $server_ip . "'";
        $result = $conn->query($query);

        // Check if the query executed successfully
        if (!$result) {
            echo "Query error: " . $conn->error . "<br>";
            return;
        }
        $files = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $filePath = $row['pwd'];
                $end_time = $row['end_time'];
                $url = $row['url'];

                $files[] = array('pwd' => $filePath, 'end_time' => $end_time, 'url' => $url);
            }
        }
        return $files;
    }   

    function empty_downloaded_files(){
        exec("rm -rf ../remoteFiles/");
    }

    function empty_keys_files(){
        exec("rm ../Keys/*");
    }

    function get_end_time_from_path(){
        include("conn.php");

        $path = $_POST['path'];
        $user = $_SESSION['user'];
        $server_ip = $_SESSION['host'];

        $query = "SELECT end_time FROM files WHERE user = '" . $user . "' AND server_ip = '" . $server_ip . "' AND pwd = '" . $path . "'";
        $result = $conn->query($query);

        // Check if the query executed successfully
        if (!$result) {
            echo "Query error: " . $conn->error . "<br>";
            return;
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo $row['end_time'];
            }
        }
    }

    function remove_local_file(){
        $path = $_POST['path'];
        $user = $_SESSION['user'];
        $server_ip = $_SESSION['host'];

        exec("rm -rf " . escapeshellarg($path . $file));
        $conn->query("DELETE FROM files WHERE user = '" . $user . "' AND server_ip = '" . $server_ip . "' AND pwd = '" . $path . "'");
    }

    function folder_to_file(){
        $folder = $_POST['folder'];
        $path = "../remoteFiles/" . $folder;

        // Change to the parent directory and tar only the folder
        $command = "cd " . escapeshellarg(dirname($path)) . " && tar -czf " . escapeshellarg($folder . ".tar.gz") . " " . escapeshellarg(basename($path));
        exec($command);

        // Remove the original folder
        exec("rm -rf " . escapeshellarg($path));

        // Return the tar file name
        echo $folder . ".tar.gz";
    }

    function update_end_time(){
        include("conn.php");

        $path = $_POST['path'];
        $end_time = $_POST['end_time'];
        $user = $_SESSION['user'];
        $server_ip = $_SESSION['host'];

        $query = "UPDATE files SET end_time = '" . $end_time . "' WHERE user = '" . $user . "' AND server_ip = '" . $server_ip . "' AND pwd = '" . $path . "'";
        $result = $conn->query($query);

        // Check if the query executed successfully
        if (!$result) {
            echo "Query error: " . $conn->error . "<br>";
            return;
        }

        echo "End time updated successfully";
    }
    
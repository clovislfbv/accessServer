<?php
    session_start();

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
        }
    }

    function cd(){
        $folder = $_POST["folder"];
        $current = $_SESSION['current'];
        $_SESSION['current'] = $current . $folder . "/";
    }

    function reset_session(){
        if (isset($_SESSION['current'])){
            unset($_SESSION['current']);
        }
    }

    function make_dir(){
        $host = $_SESSION['host'];
        $user = $_SESSION['user'];
        $password = $_SESSION['password'];
        $port = $_SESSION['port'];

        global $connection;

        $connection = ssh2_connect($host, $port);

        ssh2_auth_password($connection, $user, $password);

        $folder = $_POST["folder"];
        $current = $_SESSION['current'];
        $command = 'cd ' . $current . ' && mkdir ' . $folder;
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);
    }
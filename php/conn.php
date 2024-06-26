<?php
    $host = $_SESSION['host'];
    $user = $_SESSION['user'];
    $port = $_SESSION['port'];

    global $connection;

    $connection = ssh2_connect($host, $port);

    if (isset($_SESSION['password'])){
        $password = $_SESSION['password'];
        ssh2_auth_password($connection, $user, $password);
    } else {
        $pubfile = $_SESSION['pubfile'];
        $privfile = $_SESSION['privfile'];
        
        if (isset($_SESSION['password_key'])){
            $password_key = $_SESSION['password_key'];
            ssh2_auth_pubkey_file($connection, $user, $pubfile, $privfile, $password_key);
        } else {
            ssh2_auth_pubkey_file($connection, $user, $pubfile, $privfile);
        }
    }
?>
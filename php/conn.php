<?php
    include_once("env.php");

    if (!isset($_SESSION)) {
        session_start();
    }

    $host = $_SESSION['host'];
    $user = $_SESSION['user'];
    $port = $_SESSION['port'];

    $servername = "db";
    $username = getenv('username');
    $password = getenv('pswd');
    $dbname = getenv('db_name');

    global $connection, $conn;

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error){
        die("Error : " . $conn->connect_error);
    }

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
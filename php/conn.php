<?php
    $host = $_SESSION['host'];
    $user = $_SESSION['user'];
    $password = $_SESSION['password'];
    $port = $_SESSION['port'];

    global $connection;

    $connection = ssh2_connect($host, $port);

    ssh2_auth_password($connection, $user, $password);
?>
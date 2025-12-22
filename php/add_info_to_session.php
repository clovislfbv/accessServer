<?php
    session_start();
    $host = $_POST['host'];
    $user = $_POST['user'];
    if ($_POST['port'] == "") {
        $port = 22;
    } else {
        $port = $_POST['port'];
    }
    $id_choice = $_POST['id_choice'];
    if ($id_choice == 1) {
        $password = $_POST['password'];
        $_SESSION['password'] = $password;
    } else if (isset($_POST["password_key"])){
        $password_key = $_POST["password_key"];
        $_SESSION['password_key'] = $password_key;
    }

    $_SESSION['host'] = $host;
    $_SESSION['user'] = $user;  
    $_SESSION['port'] = $port;

    $_SESSION['files-details'] = "unchecked";
    $_SESSION['hidden-files'] = "unchecked";

    include_once("conn.php");
    include_once("helper.php");

    if ($conn->connect_error){
        die("Error : " . $conn->connect_error);
    }

    remove_local_files();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1; result.php">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <title>accessServer</title>
</head>
<body>
</body>
</html>

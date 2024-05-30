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
    }

    $_SESSION['host'] = $host;
    $_SESSION['user'] = $user;  
    $_SESSION['port'] = $port;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1; result.php">
    <title>Document</title>
</head>
<body>
    
</body>
</html>
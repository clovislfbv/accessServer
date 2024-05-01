<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        $host = $_POST['host'];
        $user = $_POST['user'];
        $password = $_POST['password'];
        $port = $_POST['port'];

        $output = null;
        $output_2 = null;
        $retval = null;

        $connection = ssh2_connect($host, $port);

        if (ssh2_auth_password($connection, $user, $password)) {
            $stream = ssh2_exec($connection, 'ls -l');
            stream_set_blocking($stream, true);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $output = stream_get_contents($stream_out);

            // Split the output into an array of lines
            $outputArray = explode("\n", $output);

            for ($i = 0; $i < count($outputArray); $i++) {
                echo "<h3>" . $outputArray[$i] . "<br></h3>";
            }
        } else {
            die('Authentication Failed...');
        }
    ?>
</body>
</html>
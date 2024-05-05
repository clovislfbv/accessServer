<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../css/result.css">
    <script src="../js/jquery.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="../js/script_website.js" type="module"></script>
    <title>Document</title>
</head>
<body>
    <?php

        function get_start_index($small, $big){
            $index = 0;
            for ($i = 0; $i < strlen($big); $i++) {
                if ($big[$i] == $small[$index]) {
                    $index++;
                } else {
                    $index = 0;
                }
                if ($index == strlen($small) && $i == strlen($big) - 1) {
                    return $i - strlen($small) + 1;
                }
            }
            return -1;
        }

        include 'conn.php';

        if (!isset($_SESSION['current'])) {
            $_SESSION['current'] = "./";
        }

        $command = 'cd ' . $_SESSION['current'] . ' && ls -a';
        $stream = ssh2_exec($connection, $command);
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);

        $folders = explode("\n", $output);

        $stream = ssh2_exec($connection, 'cd ' . $_SESSION['current'] . ' && ls -la');
        stream_set_blocking($stream, true);
        $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $output = stream_get_contents($stream_out);

        // Split the output into an array of lines
        $outputArray = explode("\n", $output);

        for ($i = 0; $i < count($outputArray); $i++) {
            echo "<div class='container float-left'>";
            if ($i == 0) {
                echo "<h3>" . $outputArray[$i] . "</h3>";
            } else if ($i > 0 && $folders[$i-1] == "..") {
                $index = get_start_index($folders[$i-1], $outputArray[$i]);
                $newString = substr_replace($outputArray[$i], "<a id='" . $i-1 . "' class='folder' href='result.php'>", $index, 0);
                echo "<img class='img' src='../assets/back.gif' alt='back'><h3>" . $newString . "<br></h3></a>";
            } else if (strlen($outputArray[$i]) > 0 && $outputArray[$i][0] == 'd') {
                $index = get_start_index($folders[$i-1], $outputArray[$i]);
                $newString = substr_replace($outputArray[$i], "<a id='" . $i-1 . "' class='folder' href='result.php'>", $index, 0);
                echo "<img class='img' src='../assets/folder.gif' alt='folder'><h3>" . $newString . "<br></h3></a>";
            } else if (count($outputArray) - 1 != $i){
                $index = get_start_index($folders[$i-1], $outputArray[$i]);
                $newString = substr_replace($outputArray[$i], "<a id='" . $i-1 . "' class='file' href='result.php'>", $index, 0);
                echo "<img class='img' src='../assets/text.gif' alt='file'><h3>" . $newString . "<br></h3></a>";
            }

            if ($i > 2 && count($outputArray) - 1 != $i){
                echo "<img class='poubelle' id='del_" . $i-1 . "' src='../assets/bin.png' alt='bin-logo'>";
            }
            echo "</div>";
        }
    ?>
    <div class="new_folder float-left">
        <a class="create_folder" href="result.php"><h3>Créer un nouveau dossier</h3></a>
    </div>
    <div class="modal" id="confirmModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Are you sure about that ?</h5>
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-primary" id="modal-body">
                <p>Are you sure to delete that file or folder ?</p>
            </div>
            <div class="modal-footer text-primary">
                <button type="submit" class="btn btn-primary" id="confirm">Confirm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
</body>
</html>
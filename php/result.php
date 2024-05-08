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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="../css/result.css">
    <script src="../js/jquery.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
                    echo "<button type='button' id='preview_" . $i-1 . "' class='btn btn-primary preview'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-eye' viewBox='0 0 16 16'><path d='M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z'></path><path d='M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0'></path></svg></button>";
                }

                if ($i > 2 && count($outputArray) - 1 != $i){
                    echo "<img class='poubelle' id='del_" . $i-1 . "' src='../assets/bin.png' alt='bin-logo'>";
                }
                echo "</div>";
            }
        ?>
    <div class="new_folder float-left">
        <a class="create_folder" href="result.php"><h3>Créer un nouveau dossier</h3></a>
        <input type="file" id="myFile" name="filename" multiple><button class="send_files d-none" type="submit" class="btn btn-primary">Send files</button><button class="cancel_files d-none" type="submit" class="btn btn-primary">Cancel</button>
        <div id="drop_zone">Drop your files here</div>
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
    <div class="modal" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="previewTitle"></h5>
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-primary previewBody" id="modal-body"></div>
            </div>
        </div>
    </div>
</body>
</html>
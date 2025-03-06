<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="../css/result.css">
    <script src="../js/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="../js/script_website.js" type="module"></script>
    <title>Document</title>
</head>
<body>
        <div class="files_options">
            <div class="form-check form-switch custom-switch">
                <div>
                    <?php
                        if ($_SESSION['files-details'] == "checked") {
                            echo "<input class='form-check-input files-details' type='checkbox' role='switch' id='flexSwitchCheckDefault' checked>";
                        } else {
                            echo "<input class='form-check-input files-details' type='checkbox' role='switch' id='flexSwitchCheckDefault'>";
                        }
                    ?>
                </div>
                <label class="form-check-label" for="flexSwitchCheckDefault"><h3>Show files details</h3></label>
            </div>
            <div class="form-check form-switch custom-switch">
                <div>
                    <?php
                        if ($_SESSION['hidden-files'] == "checked") {
                            echo "<input class='form-check-input hidden-files' type='checkbox' role='switch' id='flexSwitchCheckDefault' checked>";
                        } else {
                            echo "<input class='form-check-input hidden-files' type='checkbox' role='switch' id='flexSwitchCheckDefault'>";
                        }
                    ?>
                </div>
                <label class="form-check-label" for="flexSwitchCheckDefault"><h3>Show hidden files</h3></label>
            </div>
        </div>   
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

            if ($_SESSION['hidden-files'] == 'checked'){
                $hidden_files = " -a";
            } else {
                $hidden_files = "";
            }
            
            $command = 'cd ' . $_SESSION['current'] . ' && ls' . $hidden_files;
            $stream = ssh2_exec($connection, $command);
            stream_set_blocking($stream, true);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $output = stream_get_contents($stream_out);
            $folders = explode("\n", $output);

            if ($_SESSION['hidden-files'] == 'unchecked'){
                $new_folders = array();
                array_push($new_folders, ".");
                array_push($new_folders, "..");
                for ($i = 0; $i < count($folders); $i++) {
                    array_push($new_folders, $folders[$i]);
                }
                $folders = $new_folders;
            }


            $stream = ssh2_exec($connection, 'cd ' . $_SESSION['current'] . ' && ls -l' . $hidden_files);
            stream_set_blocking($stream, true);
            $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
            $output = stream_get_contents($stream_out);
            $outputArray = explode("\n", $output);

            if ($_SESSION['hidden-files'] == 'unchecked'){
                $stream = ssh2_exec($connection, 'cd ' . $_SESSION['current'] . ' && ls -la');
                stream_set_blocking($stream, true);
                $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                $output = stream_get_contents($stream_out);
                $newOutputArray = explode("\n", $output);

                $new_output = array();
                array_push($new_output, $outputArray[0]);
                array_push($new_output, $newOutputArray[1]);
                array_push($new_output, $newOutputArray[2]);
                for ($i = 1; $i < count($outputArray); $i++) {
                    array_push($new_output, $outputArray[$i]);
                }
                $outputArray = $new_output;
            }


            for ($i = 0; $i < count($outputArray); $i++) {
                echo "<div class='line'>";
                if ($i == 0) {
                    if ($_SESSION['files-details'] == "checked") {
                        echo "<h3>" . $outputArray[$i] . "</h3>";
                    }
                } else if ($i > 0 && $folders[$i-1] == "..") {
                    if ($_SESSION['files-details'] == "checked") {
                        $big = $outputArray[$i];
                    } else {
                        $big = $folders[$i-1];
                    }
                    $index = get_start_index($folders[$i-1], $big);
                    $newString = substr_replace($big, "<a id='" . $i-1 . "' class='folder' href='result.php'>", $index, 0);
                    echo "<img class='img' src='../assets/back.gif' alt='back'><h3>" . $newString . "<br></h3></a>";
                } else if (strlen($outputArray[$i]) > 0 && $outputArray[$i][0] == 'd') {
                    if ($_SESSION['files-details'] == "checked") {
                        $big = $outputArray[$i];
                    } else {
                        $big = $folders[$i-1];
                    }
                    $git = 0;
                    $index = get_start_index($folders[$i-1], $big);
                    echo "<div class='container_icon_folder'>";
                    $newString = substr_replace($big, "<a id='" . $i-1 . "' class='folder' href='result.php'>", $index, 0);
                    echo "<img class='img' src='../assets/folder.gif' alt='folder'><h3>" . $newString . "<br></h3></a></div>";
                    
                    $stream = ssh2_exec($connection, 'cd ' . $_SESSION['current'] . $folders[$i-1] . '&& ls -a');
                    stream_set_blocking($stream, true);
                    $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                    $new_output = stream_get_contents($stream_out);

                    $newOutputArray = explode("\n", $new_output);

                    for ($j = 0; $j < count($newOutputArray); $j++) {
                        if ($newOutputArray[$j] == ".git")
                        {
                            $git = 1;
                        }
                    }

                    echo "<div class='special_icons'>";

                    if ($git == 1 && $folders[$i-1] != ".")
                    {
                        echo "<img class='icon git_pull' id='pull_" . $i-1 . "' src='../assets/git-pull-request-icon.png' alt='git-pull-request-icon'>";
                        //print_r("git repo found");
                    }
                    
                } else if (count($outputArray) - 1 != $i){
                    if ($_SESSION['files-details'] == "checked") {
                        $big = $outputArray[$i];
                    } else {
                        $big = $folders[$i-1];
                    }
                    $index = get_start_index($folders[$i-1], $big);
                    echo "<div class='container_icon_folder'>";
                    $newString = substr_replace($big, "<a id='" . $i-1 . "' class='file' href='result.php'>", $index, 0);
                    echo "<img class='img' src='../assets/text.gif' alt='file'><h3>" . $newString . "<br></h3></a>";
                    echo "<div class='container_button'><button type='button' id='preview_" . $i-1 . "' class='btn btn-primary preview'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-eye' viewBox='0 0 16 16'><path d='M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z'></path><path d='M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0'></path></svg></button></div></div>";
                }

                if ($i > 2 && count($outputArray) - 1 != $i){
                    echo "<img class='icon poubelle' id='del_" . $i-1 . "' src='../assets/bin.png' alt='bin-logo'>";
                    echo "<div class='icon share_icon' id='share_" . $i-1 . "'>";
                    echo "<svg xmlns='http://www.w3.org/2000/svg' height='24' viewBox='0 0 24 24' width='24' focusable='false' aria-hidden='true' style='pointer-events: none; display: inherit; width: 100%; height: 100%;'><path d='M15 5.63 20.66 12 15 18.37V14h-1c-3.96 0-7.14 1-9.75 3.09 1.84-4.07 5.11-6.4 9.89-7.1l.86-.13V5.63M14 3v6C6.22 10.13 3.11 15.33 2 21c2.78-3.97 6.44-6 12-6v6l8-9-8-9z'></path></svg>";
                    echo "</div></div>";
                }
                echo "</div></div>";
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
    <div class="modal" id="outputGitModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Git pull output</h5>
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-primary git-modal-body" id="modal-body"></div>
            <div class="modal-footer text-primary">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
    <div class="modal" id="shareModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Share your file or folder</h5>
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-primary share-modal-body" id="modal-body">
            <p>You can share your file or folder via this link:\n</p><input type='text' id='url_to_copy' name='url_to_copy' readonly><button class="blabloubli" id='copy_url'>Copier</button>
            </div>
            <div class="modal-footer text-primary">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
    <i class="bi bi-arrow-left-circle"></i>
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
    <i class="bi bi-arrow-right-circle"></i>
</body>
</html>

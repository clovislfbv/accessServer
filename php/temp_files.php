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
        <header>
            <div class="header">
                <h1 class="main_title">
                    accessServer
                </h1>
                <nav class="main_nav">
                    <a href="result.php">Parcourir vos fichiers sur votre serveur</a>
                    <a href="temp_files.php">Vos fichiers temporaires sur ce site web</a>
                </nav>
            </div>
        </header>
        <?php
            include_once "helper.php";

            $files = get_all_temp_files_from_user_and_server($_SESSION['user'], $_SESSION['host']);
            
            if (!empty($files)) {
                echo '<table class="table table-striped table-bordered">';
                echo '<thead class="table-dark">';
                echo '<tr>';
                echo '<th>File Path</th>';
                echo '<th>Time Remaining</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($files as $file) {
                    // Set the timezone explicitly for both DateTime objects
                    $timezone = new DateTimeZone('Europe/Paris');
                    $endTime = new DateTime($file['end_time'], $timezone);
                    $currentTime = new DateTime('now', $timezone);

                    // Compute the difference
                    $interval = $currentTime->diff($endTime);

                    // Format the time difference
                    $timeRemaining = $interval->format('%d days, %h hours, %i minutes, %s seconds');
                    
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($file['pwd']) . '</td>';
                    echo '<td>' . htmlspecialchars($timeRemaining) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<div class="alert alert-info text-center">No temporary files found.</div>';
            }
        ?>
    </body>
</html>
<?php
    session_start();

    if (isset($_POST["action"])){
        switch ($_POST["action"]) {
            case "cd":
                cd();
                break;
            case "reset_session":
                reset_session();
                break;
        }
    }

    function cd(){
        $folder = $_POST["folder"];
        $current = $_SESSION['current'];
        $_SESSION['current'] = $current . $folder . "/";
    }

    function reset_session(){
        if (isset($_SESSION['current'])){
            unset($_SESSION['current']);
        }
    }
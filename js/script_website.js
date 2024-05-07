import { cd, resetSession, mkdir, rm, send_files, receive_file, empty_downloaded_files } from './helper.js';

var $j = jQuery.noConflict();

$j(document).ready(function () {
    if ($j("#confirmModal").length) {
        console.log('confirmModal');
        $j("#confirmModal").modal("hide");
    }

    $j('.folder').click(function (e) {
        console.log('click');
        e.preventDefault();
        var folder = $j(this).text();
        cd(folder);
        empty_downloaded_files();
        window.location.href = '../php/result.php';
    });

    $j(".file").click(function (e) {
        e.preventDefault();
        let filename = $j(this).text();
        receive_file(filename);
        let link = document.createElement('a');
        link.href = "../remoteFiles/" + filename;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.location.href = '../php/result.php';
    });

    $j("#form").submit(function (e) {
        e.preventDefault();
        resetSession();
        $j("#form")[0].submit();
    });

    $j(".create_folder").click(function (e) {
        console.log("create folder")
        e.preventDefault();
        $j(".new_folder").html('<input type="text" name="folder" id="folder" placeholder="Folder name" required><button class="valid_new_folder" type="submit" class="btn btn-primary">Create</button><button class="cancel" type="submit" class="btn btn-primary">Cancel</button>');
        $j(".valid_new_folder").click(function (e) {
            mkdir($j("#folder").val());
            window.location.href = '../php/result.php';
        });
        $j(".cancel").click(function (e) {
            window.location.href = '../php/result.php';
        });
    });

    var del_id
    $j(".poubelle").click(function (e) {
        e.preventDefault();
        $j("#confirmModal").modal("show");
        del_id = $j(this).attr('id');
    });

    $j("#confirm").click(function (e) {
        var folder_id = del_id.replace('del_', '');
        var folder = $j("#" + folder_id).text();
        rm(folder);
        window.location.href = '../php/result.php';
    });

    $j("#drop_zone").on("dragover", function (e) {
        e.preventDefault();
        $j(this).addClass("blue");
        console.log("dragover");
    })

    $j("#drop_zone").on("dragleave", function (e) {
        e.preventDefault();
        $j(this).removeClass("blue");
        console.log("dragleave");
    })

    $j("#drop_zone").on("drop", function (e) {
        e.preventDefault();
        $j(this).removeClass("blue");
        var files = e.originalEvent.dataTransfer.files;
        $j("#myFile").prop("files", files);
        $j(".send_files").removeClass("d-none");
        $j(".cancel_files").removeClass("d-none");
    })

    $j("#myFile").on('change', function (e) {
        if (e.target.files.length > 0) {
            $j(".send_files").removeClass("d-none");
            $j(".cancel_files").removeClass("d-none");
        } else {
            $j(".send_files").addClass("d-none");
            $j(".cancel_files").addClass("d-none");
        }
    });

    $j(".send_files").click(function (e) {
        var files = $j("#myFile").prop("files");
        send_files(files);
        window.location.href = '../php/result.php';
    });

    $j(".cancel_files").click(function (e) {
        window.location.href = '../php/result.php';
    });

    // window.onbeforeunload = function (event) {
    //     console.log("onbeforeunload");
    //     empty_downloaded_files();
    // };
});
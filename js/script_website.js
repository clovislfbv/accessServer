import { cd, resetSession, mkdir, rm, send_files, receive_file, empty_downloaded_files, dl_key_file, setPubKey, setPrivKey, empty_keys_files } from './helper.js';

var $j = jQuery.noConflict();

$j(document).ready(function () {
    if ($j("#confirmModal").length) {
        console.log('confirmModal');
        $j("#confirmModal").modal("hide");
    }

    if ($j("#previewModal").length) {
        console.log('previewModal');
        $j("#previewModal").modal("hide");
        $j(".bi-arrow-left-circle").addClass("d-none");
        $j(".bi-arrow-right-circle").addClass("d-none");
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
        var id_choice = $j(".id_choice").val();
        if (id_choice === "2") {
            var file = $j("#pubfile").prop("files");
            console.log(file[0].name);
            dl_key_file(file[0]);
            setPubKey(file[0].name);
            file = $j("#privfile").prop("files");
            dl_key_file(file[0]);
            setPrivKey(file[0].name);
        }
        this.submit();
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
        console.log(folder);
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
        console.log(files);
        send_files(files);
        window.location.href = '../php/result.php';
    });

    $j(".cancel_files").click(function (e) {
        window.location.href = '../php/result.php';
    });

    $j(".preview").click(function (e) {
        $j(".bi-arrow-left-circle").removeClass("d-none");
        $j(".bi-arrow-right-circle").removeClass("d-none");
        var file_id = $j(this).attr('id').replace('preview_', '');
        var filename = $j("#" + file_id).text();
        console.log(filename);
        receive_file(filename);
        console.log("received file");
        var directory = "../remoteFiles/";
        var filePath = directory + filename;

        var fileExtension = filename.split('.').pop().toLowerCase();
        var imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];

        if (imageExtensions.includes(fileExtension)) {
            $j("#previewModal").find(".modal-content").resizable({
                handles: 'n, e, s, w, ne, sw, se, nw',
            });
            $j("#previewModal").modal("show");
            $j("#previewTitle").text(filename);
            $j(".previewBody").html("<img src='" + filePath + "' class='img-fluid' alt='" + filename + "'>");
            $j(".modal-backdrop").removeClass("show");
            $j(".modal-backdrop").addClass("d-none");
        } else {
            window.location.href = filePath;
        };

        // $j("#previewModal").find(".modal-content").css({ "height": "70vh" })
        // $j(".previewBody").css({ "height": "65vh" })

        // if (imageExtensions.includes(fileExtension)) {
        //     $j(".previewBody").html("<object data='" + filePath + "' type='image/" + fileExtension + "' allowfullscreen></object>");
        // } else if (fileExtension === 'pdf') {
        //     $j(".previewBody").html("<object data='" + filePath + "' type='application/pdf' width='100%' height='100%' allowfullscreen></object>");
        // } else {
        //     $j(".previewBody").html("<object data='" + filePath + "' width='100%' height='100%'><param name='allowFullScreen' value='true'></param></object>");
        // }
    });

    $j("#previewModal").on('hidden.bs.modal', function () {
        $j(".bi-arrow-left-circle").addClass("d-none");
        $j(".bi-arrow-right-circle").addClass("d-none");
        $j(".previewBody").html("");
    });

    window.onbeforeunload = function (event) {
        console.log("onbeforeunload");
        //empty_downloaded_files();
        //empty_keys_files();
    };
});
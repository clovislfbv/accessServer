import { cd, resetSession, mkdir, rm, send_files, receive_file, receive_file_async, empty_downloaded_files, dl_key_file, setPubKey, setPrivKey, empty_keys_files, ls, ls_extensions, git_pull, set_files_details, set_hidden_files } from './helper.js';

var $j = jQuery.noConflict();

async function checkFileExists(filePath) {
    try {
        const response = await fetch(filePath, { method: 'HEAD' });
        if (response.ok) {
            //console.log('File exists:', filePath);
            return true;
        } else {
            //console.log('File does not exist:', filePath);
            return false;
        }
    } catch (error) {
        console.error('Error checking file:', error);
        return false;
    }
    return false;
}

function downloadNeighbourFile(files, index) {
    if (index > 0) {
        checkFileExists("../remoteFiles/" + files[index - 1]).then((exists) => {
            if (!exists) {
                receive_file_async(files[index - 1]);
            }
        });
    }

    if (index < files.length - 1) {
        checkFileExists("../remoteFiles/" + files[index + 1]).then((exists) => {
            if (!exists) {
                receive_file_async(files[index + 1]);
            }
        });
    }
}


$j(document).ready(function () {
    // if (window.location.pathname.endsWith('result.php')) {
    //     var current_files = ls();
    //     current_files = current_files.split('\n');
    //     current_files.pop();
    //     for (var i = 0; i < current_files.length / 2; i++) {
    //         if ($j("#" + i).hasClass("file")) {
    //             console.log(current_files[i]);
    //             receive_file_async(current_files[i]);
    //         }
    //     }
    // }

    var images;
    if ($j("#confirmModal").length) {
        console.log('confirmModal');
        $j("#confirmModal").modal("hide");
    }

    if ($j("#outputGitModal").length) {
        console.log('outputGitModal');
        $j("#outputGitModal").modal("hide");
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

    var git_id
    $j(".git_pull").click(function (e) {
        git_id = $j(this).attr('id');
        console.log(git_id);
        var git_pull_id = git_id.replace('pull_', '');
        var folder = $j("#" + git_pull_id).text();
        console.log(folder);
        var output = git_pull(folder);
        $j(".git-modal-body").html("<p>" + output + "</p>");
        $j("#outputGitModal").modal("show");
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
        e.preventDefault();
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
        var file_id_int = parseInt(file_id, 10);
        var filename = $j("#" + file_id).text();
        var directory = "../remoteFiles/";
        //console.log(filename);

        var filePath = directory + filename;
        receive_file(filename);
        //console.log("received file");
        processFile(filePath, filename, directory);
    });

    function processFile(filePath, filename, directory) {
        // for (var i = current_files.length / 2; i < current_files.length; i++) {
        //     if ($j("#" + i).hasClass("file")) {
        //         console.log(current_files[i]);
        //         receive_file_async(current_files[i]);
        //     }
        // }
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

            images = ls_extensions(imageExtensions);
            images = images.split('\n');
            images.pop();

            var index = images.indexOf(filename);
            downloadNeighbourFile(images, index);

            $j("#previewModal").keydown(function (e) {
                if (e.keyCode === 37) {
                    //console.log("left");
                    var index = images.indexOf(filename);
                    if (index === 0) {
                        filename = images[0];
                    } else {
                        downloadNeighbourFile(images, index - 1);

                        filename = images[index - 1];
                        filePath = directory + filename;
                        // checkFileExists(filePath).then((exists) => {
                        //     if (!exists) {
                        //         receive_file(filename);
                        //     }
                        // });
                    }
                    $j("#previewTitle").text(filename);
                    $j(".previewBody").html("<img src='" + filePath + "' class='img-fluid' alt='" + filename + "'>");
                } else if (e.keyCode === 39) {
                    //console.log("right");
                    var index = images.indexOf(filename);
                    if (index === images.length - 1) {
                        filename = images[images.length - 1];
                    } else {
                        filename = images[index + 1];
                        downloadNeighbourFile(images, index + 1);

                        filePath = directory + filename;
                        // checkFileExists(filePath).then((exists) => {
                        //     if (!exists) {
                        //         receive_file(filename);
                        //     }
                        // });
                        $j("#previewTitle").text(filename);
                        $j(".previewBody").html("<img src='" + filePath + "' class='img-fluid' alt='" + filename + "'>");
                    }
                }
            });
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
    };

    $j("#previewModal").on('hidden.bs.modal', function () {
        $j(".bi-arrow-left-circle").addClass("d-none");
        $j(".bi-arrow-right-circle").addClass("d-none");
        $j(".previewBody").html("");
    });

    $j(".files-details").change(function () {
        var status;
        if ($j(this).prop("checked")) {
            status = "checked";
        } else {
            status = "unchecked";
        }
        set_files_details(status);
        window.location.href = '../php/result.php';
    });

    $j(".hidden-files").change(function (e) {
        var status;
        if ($j(this).prop("checked")) {
            status = "checked";
        } else {
            status = "unchecked";
        }
        set_hidden_files(status);
        window.location.href = '../php/result.php';
    });

    window.onbeforeunload = function (event) {
        console.log("onbeforeunload");
        //empty_downloaded_files();
        //empty_keys_files();
    };

});
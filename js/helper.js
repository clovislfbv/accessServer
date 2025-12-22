var $j = jQuery.noConflict();

export function cd(folder) {
    /*** 
     * commande pour changer de dossier
     * ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'cd',
            folder: folder
        },
        async: false,
        success: function (data) {
            console.log(data);
        }
    });
}

export function resetSession() {
    /*** 
     * commande pour reset la session en cours
     * ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        async: false,
        data: {
            action: 'reset_session'
        },
    });
}

export function mkdir(folder) {
    /*** 
     * commande pour créer un dossier
     * ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        async: false,
        data: {
            action: 'mkdir',
            folder: folder
        },
    });
}

export function rm(folder) {
    /*** 
     * commande pour supprimer un fichier
     * ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'rm',
            folder: folder
        },
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

export function send_files(files) {
    /***
     * Commande pour envoyer des fichiers au serveur distant depuis le client
     ***/
    var formData = new FormData();

    for (var i = 0; i < files.length; i++) {
        formData.append('file' + i, files[i]);
    }

    console.log(files);
    console.log(formData);

    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: formData,
        async: false,
        processData: false,  // tell jQuery not to process the data
        contentType: false,  // tell jQuery not to set contentType
        success: function (data) {
            data = JSON.parse(data);
            console.log(data);
            // Check if the file was uploaded successfully
            if (data["success"]) {
                console.log("File uploaded successfully");
            } else {
                console.error("File upload failed:", data["error"]);
            }
        },
        error: function (err) {
            console.log(err);
        }
    });
}

export function dl_key_file(file) {
    /***
     * Commande pour télécharger les clés ssh depuis le client vers le serveur web pour l'authentification par clé ssh 
     ***/
    var formData = new FormData();
    formData.append('keyfile', file);
    console.log(file);
    console.log(formData);

    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: formData,
        async: false,
        processData: false,  // tell jQuery not to process the data
        contentType: false,  // tell jQuery not to set contentType
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });

}

export function setPubKey(file) {
    /***
     * commande pour stocker le path vers la clé publique téléchargée en session
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'set_pubkey',
            file: file
        },
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

export function setPrivKey(file) {
    /***
     * commande pour stocker le path vers la clé privée téléchargée en session
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'set_privkey',
            file: file
        },
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

export function receive_file(file) {
    /***
     * commande pour télécharger un fichier depuis le serveur distant vers le client
     * Précision : obligé d'attendre la fin du téléchargement du fichier pour afficher le fichier
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'receive_file',
            file: file
        },
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });

}

export function receive_file_async(file) {
    /***
     * commande pour télécharger un fichier depuis le serveur distant vers le client
     * Précision : pas besoin d'attendre la fin du téléchargement pour faire autre chose
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'receive_file',
            file: file
        },
        success: function (data) {
            console.log(data);
        },
    });
}

export function receive_folder(folder) {
    /***
     * Commande pour télécharger un dossier depuis le serveur distant vers le client
     * Précision : obligé d'attendre la fin du téléchargement pour afficher le dossier
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'receive_directory',
            folder: folder
        },
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

export function ls() {
    /***
     * commande pour lister les fichiers du dossier courant
     ***/
    let output;
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'ls'
        },
        async: false,
        success: function (data) {
            output = JSON.parse(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
    return output;
}

export function ls_extensions(extensions) {
    /***
     * commande pour lister les extensions des fichiers provenant de la liste d'extensions
     ***/
    let output;
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'ls_extensions',
            extensions: extensions
        },
        async: false,
        success: function (data) {
            output = JSON.parse(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
    return output;
}

export function git_pull(folder) {
    /***
     * commande pour faire un git pull dans un repo git
     ***/
    var output;
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'git_pull',
            folder: folder,
        },
        async: false,
        success: function (data) {
            output = data;
        },
        error: function (err) {
            output = err;
        }
    });

    return output;
}

export function empty_downloaded_files() {
    /***
     * commande pour vider le dossier des fichiers téléchargés
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'empty_downloaded_files'
        },
        success: function (data) {
            console.log(data);
        },
    });
}

export function empty_keys_files() {
    /***
     * commande pour vider le dossier des clés ssh
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'empty_keys_files'
        },
        success: function (data) {
            console.log(data);
        },
    });
}

export function set_files_details(status){
    /***
     * commande pour savoir s'il faut afficher les détails sur les fichiers ou non
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'set_files_details',
            status: status
        },
        async: false,
    });
}

export function set_hidden_files(status){
    /***
     * commande pour savoir s'il faut afficher les fichiers cachés ou non
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'set_hidden_files',
            status: status
        },
        async: false,
    });
}

export function set_user_timezone(timezone) {
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'set_user_timezone',
            timezone: timezone,
        },
        async: false,
    });
}

export function get_end_time_from_path(path) {
    /***
     * commande pour récupérer le temps restant avant la fin du téléchargement
     ***/
    var end_time = 0;
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'get_end_time_from_path',
            path: path,
        },
        async: false,
        success: function (data) {
            end_time = data;
        },
        error: function (err) {
            console.log(err);
        }
    });
    return end_time;
}

export function remove_local_file(path) {
    /***
     * commande pour supprimer un fichier local
     ***/
    let result = null;
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'remove_local_file',
            path: path
        },
        async: false,
        success: function (data) {
            try {
                result = typeof data === 'string' ? JSON.parse(data) : data;
            } catch (e) {
                result = { success: false, error: 'invalid_json', raw: data };
            }
        },
        error: function (err) {
            result = { success: false, error: 'ajax_error', details: err };
        }
    });

    return result;
}

export function folder_to_file(folder) {
    /***
     * commande pour créer un fichier à partir d'un dossier
     ***/
    let output;
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'folder_to_file',
            folder: folder
        },
        async: false,
        success: function (data) {
            output = data;
        },
        error: function (err) {
            console.log(err);
        }
    });
    return output;
}

export function update_end_time(path, end_time) {
    /***
     * commande pour mettre à jour le temps restant avant la fin du téléchargement
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'update_end_time',
            path: path,
            end_time: end_time
        },
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

export function git_clone(repo) {
    /***
     * commande pour cloner un repo git
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'git_clone',
            url: repo
        },
        async: false,
        error: function (err) {
            console.log(err);
        }
    });
}

export function dl_file_from_url(file) {
    /***
     * commande pour télécharger un fichier depuis une URL
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'dl_file_from_url',
            url: file
        },
        async: false,
        error: function (err) {
            console.log(err);
        }
    });
}
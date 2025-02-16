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
        error: function (err) {
            console.log(err);
        }
    });

}

export function receive_file_async(file) {
    /***
     * commande pour télécharger un fichier depuis le serveur distant vers le client
     * Précision : pas besoin d'attendre la fin du télécargement pour faire autre chose
     ***/
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'receive_file',
            file: file
        },
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
var $j = jQuery.noConflict();

export function cd(folder) {
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
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'reset_session'
        },
    });
}

export function mkdir(folder) {
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
            console.log(data);
        },
        error: function (err) {
            console.log(err);
        }
    });
}

export function dl_key_file(file) {
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
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'set-privkey',
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

export function empty_downloaded_files() {
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

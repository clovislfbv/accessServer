var $j = jQuery.noConflict();

export function cd(folder){
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'cd',
            folder: folder
        },
        async: false,
        success: function(data){
            console.log(data);
        }
    });
}

export function resetSession(){
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'reset_session'
        },
    });
}

export function mkdir(folder){
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'mkdir',
            folder: folder
        },
    });
}

export function rm(folder){
    $j.ajax({
        url: '../php/helper.php',
        type: 'POST',
        data: {
            action: 'rm',
            folder: folder
        },
        success: function(data){
            console.log(data);
        },
        error: function(err){
            console.log(err);
        }
    });
}
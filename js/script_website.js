import { cd, resetSession, mkdir } from './helper.js';

var $j = jQuery.noConflict();

$j(document).ready(function(){
    $j('.folder').click(function(e){
        console.log('click');
        e.preventDefault();
        var folder = $j(this).text();
        cd(folder);
        window.location.href = '../php/result.php';
    });

    $j("#form").submit(function(e){
        e.preventDefault();
        resetSession();
        $j("#form")[0].submit();
    });

    $j(".create_folder").click(function(e){
        console.log("create folder")
        e.preventDefault();
        $j(".new_folder").html('<input type="text" name="folder" id="folder" placeholder="Folder name" required><button class="valid_new_folder" type="submit" class="btn btn-primary">Create</button><button class="cancel" type="submit" class="btn btn-primary">Cancel</button>');
        $j(".valid_new_folder").click(function(e){
            mkdir($j("#folder").val());
            window.location.href = '../php/result.php';
        });
        $j(".cancel").click(function(e){
            window.location.href = '../php/result.php';
        });
    });
});
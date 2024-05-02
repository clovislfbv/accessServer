import { cd, resetSession } from './helper.js';

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
});
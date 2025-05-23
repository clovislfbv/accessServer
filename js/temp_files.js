import { get_end_time_from_path, remove_local_file, update_end_time } from './helper.js';

var $j = jQuery.noConflict();

function show_time_remaining(path, index) {
    console.log("show_time_remaining");
    var end_time = get_end_time_from_path(path);
    end_time = Date.parse(end_time);
    console.log("test");
    console.log("End time: " + end_time);
    var current_time = new Date().getTime();
    var time_remaining = end_time - current_time;
    setInterval(function() {
        var days = Math.floor(time_remaining / (1000 * 60 * 60 * 24));
        var hours = Math.floor((time_remaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((time_remaining % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((time_remaining % (1000 * 60)) / 1000);
        
        $j(".row_" + index + " .time_remaining").text(days + " days " + hours + " hours " + minutes + " minutes " + seconds + " seconds");
        if (time_remaining < 0 || (isNaN(days) && isNaN(hours) && isNaN(minutes) && isNaN(seconds))) {
            clearInterval(this);
            remove_local_file(path);
            window.location.href = '../php/temp_files.php';
        }

        time_remaining = end_time - current_time;
        current_time = new Date().getTime();
    }, 1000);
}

$j(document).ready(function () {
    $j('.table tbody tr').each((index, row) => {
        // Get the text of the `.path` cell in the current row
        var path = $j(row).find('.path').text().trim();

        // Call your function with the path
        show_time_remaining(path, index);
    });
});

$j(document).on('input', '.input_edit_end_time', function() {
    $j(this).siblings('button').prop('disabled', false);
});

$j('.edit_end_time').click(function() {
    var value = $j(this).siblings('input').val();
    var index = $j(this).attr('id').split('_')[2];
    var path = $j(".row_" + index).find('.path').text().trim();
    console.log("Path: " + path);
    console.log("Value: " + value);

    update_end_time(path, value);
    window.location.href = '../php/temp_files.php';
});
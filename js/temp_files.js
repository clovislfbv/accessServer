import { get_end_time_from_path, remove_local_file, update_end_time } from './helper.js';

var $j = jQuery.noConflict();

function show_time_remaining(path, index) {
    console.log("show_time_remaining");
    var end_time = get_end_time_from_path(path);
    end_time = Date.parse(end_time);
    console.log("End time: " + end_time);

    // Si déjà expiré au chargement, supprimer tout de suite.
    var initial_time_remaining = end_time - new Date().getTime();
    if (!Number.isFinite(initial_time_remaining) || initial_time_remaining <= 0) {
        var res = remove_local_file(path);
        if (res && res.success) {
            window.location.href = '../php/temp_files.php';
        } else {
            console.error('remove_local_file failed', res);
            $j(".row_" + index + " .time_remaining").text('Expired (delete failed)');
        }
        return;
    }

    var intervalId = setInterval(function() {
        var current_time = new Date().getTime();
        var time_remaining = end_time - current_time;

        if (!Number.isFinite(time_remaining) || time_remaining <= 0) {
            clearInterval(intervalId);
            var res = remove_local_file(path);
            if (res && res.success) {
                window.location.href = '../php/temp_files.php';
            } else {
                console.error('remove_local_file failed', res);
                $j(".row_" + index + " .time_remaining").text('Expired (delete failed)');
            }
            return;
        }

        var days = Math.floor(time_remaining / (1000 * 60 * 60 * 24));
        var hours = Math.floor((time_remaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((time_remaining % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((time_remaining % (1000 * 60)) / 1000);

        $j(".row_" + index + " .time_remaining").text(days + " days " + hours + " hours " + minutes + " minutes " + seconds + " seconds");
        console.log("Time remaining: " + time_remaining);
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
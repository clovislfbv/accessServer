var $j = jQuery.noConflict();

$j(document).ready(function () {
    $j('#password').prop('disabled', false);
    $j('#password').prop('required', true);

    $j("#pubfile").prop('disabled', true);
    $j("#pubfile").prop('required', false);

    $j("#privfile").prop('disabled', true);
    $j("#privfile").prop('required', false);

    window.matchMedia("(orientation: portrait)").addEventListener("change", e => {
        const portrait = e.matches;
        if (portrait) {
            $j(".right").css({
                "height": "0",
                "width": "0"
            });
            $j(".center").css({
                "height": "100%",
                "width": "100%",
                "display": "flex",
                "flex-direction": "column",
                "justify-content": "space-evenly",
                "align-items": "center",
            });
            $j(".left").css({
                "height": "0",
                "width": "0"
            });
        } else {
            $j(".right").css({
                "height": "100%",
                "width": "25%"
            });
            $j(".center").css({
                "height": "100%",
                "width": "50%",
                "display": "flex",
                "flex-direction": "column",
                "justify-content": "space-evenly",
                "align-items": "center",
            });
            $j(".left").css({
                "height": "100%",
                "width": "25%"
            });
        }
    });

    $j('.with_password').change(function () {
        if ($j(this).is(":checked")) {
            $j('#password').prop('disabled', false);
            $j('#password').prop('required', true);

            $j("#pubfile").prop('disabled', true);
            $j("#pubfile").prop('required', false);

            $j("#privfile").prop('disabled', true);
            $j("#privfile").prop('required', false);

            $j(".with_pubfile").prop('checked', false);
            $j(".id_choice").val(1);
        } else {
            $j('#password').prop('disabled', true);
            $j('#password').prop('required', false);

            $j("#pubfile").prop('disabled', false);
            $j("#pubfile").prop('required', true);

            $j("#privfile").prop('disabled', false);
            $j("#privfile").prop('required', true);

            $j(".with_pubfile").prop('checked', true);
            $j(".id_choice").val(2);
        }
    });

    $j('.with_pubfile').change(function () {
        if ($j(this).is(":checked")) {
            $j('#password').prop('disabled', true);
            $j('#password').prop('required', false);

            $j("#pubfile").prop('disabled', false);
            $j("#pubfile").prop('required', true);

            $j("#privfile").prop('disabled', false);
            $j("#privfile").prop('required', true);

            $j(".with_password").prop('checked', false);
            $j(".id_choice").val(2);
        } else {
            $j('#password').prop('disabled', false);
            $j('#password').prop('required', true);

            $j("#pubfile").prop('disabled', true);
            $j("#pubfile").prop('required', false);

            $j("#privfile").prop('disabled', true);
            $j("#privfile").prop('required', false);

            $j(".with_password").prop('checked', true);
            $j(".id_choice").val(1);
        }
    });
});
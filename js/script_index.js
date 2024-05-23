var $j = jQuery.noConflict();

$j(document).ready(function () {
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
});
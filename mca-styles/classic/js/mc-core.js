jQuery(document).ready(function ($) {
    $j = jQuery;
    if ($j("#mc_core_log textarea").length != 0) {

    }
    if ($j("#uninstall").length != 0) {

    }



    $j("#mca .message").css({
        height: $j(document).height()
    });
    $j("#mca .message .nein").click(function () {
        $j("#mca .message").fadeOut();

        $j("#wpwrap").css({
            overflow: "auto"
        });
    });
    $j("#mc_info_content a.remove").click(function () {
        $j("#wpwrap").css({
            overflow: "hidden"
        });
        $j("#mca .message .ja").attr({href: $j(this).attr("href")});
        $j("#mca .message").fadeIn();
        return false;
    });
});
MCA.Install = function () {
    var _this = this;
    var SETTINGS = [];
    this.init = function () {

    };
    this.createDialog = function (title, text, options) {
        return jQuery("<div class='dialog' title='" + title + "'><p>" + text + "</p></div>").dialog(options);
    };
    this.parse = function (inhalt) {
        if (inhalt.substr(inhalt.length - 1) == "0") {
            response = inhalt.slice(0, -1);
        } else {
            response = inhalt;
        }
        return response;
    };
    this.load = function (PARAMTERS, FUNCTION) {
        var paras = {
            nav: "install",
        };
        return jQuery.when(jQuery.post(ajaxurl, _this.merge_options(paras, PARAMTERS), FUNCTION)).done(function (a1) {
            return _this.parse(a1);
        });
    };
    this.merge_options = function (obj1, obj2) {
        var obj3 = {};
        for (var attrname in obj1) {
            obj3[attrname] = obj1[attrname];
        }
        for (var attrname in obj2) {
            obj3[attrname] = obj2[attrname];
        }
        return obj3;
    };
    this.loadStart = function () {
        _this.load({
            action: "startuploading"
        }, function (response) {
            SETTINGS = response;
        });
    };
    this.lizenz = function () {
        jQuery.colorbox({
            href: jQuery("#lizenz a").attr("href"),
            title: "Lizenz",
            iframe: true,
            width: "80%",
            height: "80%",
            onClosed: function () {

                jQuery('#lizenz').append('<div id="dialog-confirm" title="Empty the recycle bin?">    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Nimmst du die Lizenz an?</p> </div>').children('#dialog-confirm').dialog({
                    resizable: false,
                    modal: true,
                    buttons: {
                        "Yes": function () {
                            window.location = "admin.php?page=minecraft-admin&mca_install=3";
                            jQuery(this).dialog("close");

                        },
                        "No": function () {
                            jQuery('#lizenz').append('<div id="dialog-confirm2" title="Empty the recycle bin?">    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Du darfst Minecraft Admin nicht benutzen...</p> </div>').children('#dialog-confirm2').dialog({
                                resizable: false,
                                buttons: {
                                    "Ok": function () {
                                        jQuery(this).dialog("close");
                                    }
                                },
                                close: function () {
                                    window.location = "plugins.php?action=deactivate&plugin=minecraft-admin%2Floader.php&plugin_status=all&paged=1";

                                }
                            });
                        }
                    }
                });


                return;
            }

        });
        return false;
    }
    this.install = function () {
        setInterval(function () {
            if (jQuery("#mca .loading").length != 0) {
                _this.load({
                    action: "install",
                }, function (response) {
                    if (response == "ja0") {
                        jQuery("#mca .loading").fadeOut();
                        jQuery("#mca .logundbutton .fertig_install").fadeIn();
                        jQuery("#mca #log").fadeOut();
                    }
                });

            }
        }, 1000);

        setInterval(function () {
            _this.load({
                action: "installlog",
            }, function (response) {
                jQuery("#install #log").html(response);
            });
        }, 1000);


        setInterval(function () {
            _this.load({
                action: "installstart",
            }, function (response) {
                if (response == "installing0") {

                    jQuery("#mca .logundbutton").fadeIn();
                    jQuery("#mca .befehle").fadeOut();
                }
            });

        }, 1000);
        return false;
    }
};
jQuery(document).ready(function ($) {
    var $MCA = new MCA.Install();
    $MCA.init();
    if (jQuery("#installbox").length != 0) {
        $MCA.install();
    }
    $("#lizenz a").click(function () {
        $MCA.lizenz();
        return false;
    });
    $("#lizenzbutton").click(function () {
        $("#lizenz a").click();
        return false;
    });
    // if(( $(document).getUrlParam("schritt") == 1 && $(document).getUrlParam("schritt") != null) || ( $(document).getUrlParam("schritt") != 1 && $(document).getUrlParam("schritt") == null ))
    //   $("#lizenz a").click();
});
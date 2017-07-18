MCA.Settings = function () {
    var _this = this;
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
            nav: "settings",
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
    this.changeDesign = function (name) {
        _this.createDialog("Change design", "Do you really want to change the design to " + name + "?", {
            resizable: false,
            height: 250,
            modal: true,
            buttons: {
                "Yes": function () {
                    _this.load({
                        action: "changedesign",
                        name: name,
                    }, function (response) {
                        jQuery.notify(response, "success");
                    });
                    jQuery(this).dialog("close");
                },
                Cancel: function () {
                    jQuery(this).dialog("close");
                }
            }
        });
    }
    this.uninstall = function () {
        _this.createDialog("Uninstall", jQuery("#removeQuestion").text(), {
            resizable: false,
            height: 250,
            modal: true,
            buttons: {
                "Yes": function () {
                    _this.createDialog("Uninstall", jQuery("#removeCode").html(), {
                        resizable: false,
                        height: 250,
                        modal: true,
                        buttons: {
                            "Ok, done": function () {
                                var dia = _this.createDialog("Uninstall", jQuery("#removeLoading").html(), {
                                    resizable: false,
                                    height: 250,
                                    modal: true,
                                    buttons: {
                                        "wainting": function () {
                                        }
                                    }
                                });
                                var int = setInterval(function () {
                                    _this.load({
                                        action: "mca_uninstall"
                                    }, function (response) {
                                        if (response == "ja0") {
                                            _this.load({
                                                action: "uninstall",
                                            }, function (response) {
                                                jQuery.notify(response, "success");
                                            });
                                            _this.createDialog("Uninstall", jQuery("#removeMessage").html(), {
                                                resizable: false,
                                                height: 250,
                                                modal: true,
                                                buttons: {
                                                    "Finish": function () {
                                                        jQuery(this).dialog("close");
                                                    }
                                                }
                                            });
                                            clearInterval(int);
                                            dia.dialog("close");
                                        }
                                    });
                                }, 1000);
                                jQuery(this).dialog("close");
                            },
                            Cancel: function () {
                                jQuery(this).dialog("close");
                            }
                        }
                    });
                    jQuery(this).dialog("close");
                },
                Cancel: function () {
                    jQuery(this).dialog("close");
                }
            }
        });
        return false;
    }
};
jQuery(document).ready(function ($) {
    var $MCA = new MCA.Settings();
    $MCA.init();
    $("#uninstall .box").click(function () {
        $MCA.changeDesign($(this).data("name"));
    });
    $("#uninstalllbutton").click(function () {
        $MCA.uninstall();
    });
});
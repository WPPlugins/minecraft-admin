MCA.Filebrowser = function () {
    var _this = this;

    this.path = "/";
    this.page = "";
    this.file = "";
    this.SERVERID = 1;
    this.BLOGID = 1;
    this.paras = {
        action: 'mca_filebrowser',
        path: _this.path,
        nav: "servers",
        subnav: "filebrowser",
        server: jQuery("#serverid").text()
    }
    jQuery("#editor textarea").autosize();
    this.getpath = function (akfile) {
        return "../wp-content/minecraft.dir/servers/" + _this.SERVERID + "_" + _this.BLOGID + "/" + _this.path + "/" + akfile;
    }
    this.merge_options = function (obj1, obj2) {
        var obj3 = {};
        for (var attrname in obj1) {
            obj3[attrname] = obj1[attrname];
        }
        for (var attrname in obj2) {
            obj3[attrname] = obj2[attrname];
        }
        return obj3;
    }
    this.setTmp = function (TMP) {
        this.TMP = TMP;
    }
    this.getTmp = function () {
        return this.TMP;
    }
    this.load = function (PARAMTERS, FUNCTION) {

        var paras = {
            action: 'mca_filebrowser',
            path: _this.path,
            nav: "servers",
            subnav: "filebrowser",
            server: jQuery("#serverid").text()
        };
        return jQuery.post(ajaxurl, _this.merge_options(paras, PARAMTERS), FUNCTION);
    }
    this.parse = function (inhalt) {
        if (inhalt.substr(inhalt.length - 1) == "0") {
            response = inhalt.slice(0, -1);
        } else {
            response = inhalt;
        }
        return response;
    }
    this.breadcrumbs = function () {
        this.load({
            mca_get: "breadcrumbs"
        }, function (re) {
            jQuery("#breadcrumbs").html(_this.parse(re));
        });
    }
    this.loaddir = function (dir) {
        _this.path = dir;
        _this.load({
            mca_get: "dir",
            path: dir
        }, function (response) {
            jQuery("#fileview .content").html(_this.parse(response));
            _this.breadcrumbs();
            _this.load({
                mca_get: "fileinfos"
            }, function (response) {
                jQuery("#infos").html(_this.parse(response));
            });
        });
    }
    this.edit = function ($file) {
        file = $file;
        _this.load({
            mca_get: "file",
            file: file
        }, function (response) {
            jQuery("#editor").children("textarea").html(_this.parse(response)).trigger('autosize.resize');
            jQuery("#editor h2 span").html(window.page + " -");
        });
        jQuery("#extras").animate({
            width: "0"
        }).fadeOut();
        jQuery("#editor").animate({
            width: "100%"
        }).fadeIn();
        jQuery("#fileview").animate({
            width: "0"
        }).fadeOut();
        jQuery("#middle").animate({
            flexBasis: "100%",
            WebkitFlexBasis: "100%",
        });
    }
    this.createDialog = function (title, text, options) {
        return jQuery("<div class='dialog' title='" + title + "'></div>").html(text).dialog(options);
    }
    this.contextmenunormal = function () {
        jQuery.contextMenu({
            selector: '#fileview ul li.file',
            callback: function (key, options) {
                window.page = jQuery(this).html();
                if (key == "edit") {
                    _this.edit(jQuery(this).data("path"));
                } else if (key == "rename") {
                    var $path = jQuery(this).data("path");
                    _this.createDialog("Rename", 'Rename this file.<form><input name="rename" id="rename" placeholder="' + jQuery(this).text() + '"></form>', {
                        resizable: false,
                        height: 250,
                        modal: true,
                        buttons: {
                            "Rename": function () {
                                _this.load({
                                    mca_get: "rename",
                                    file: $path,
                                    name: jQuery(this).find("#rename").val(),
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
                } else if (key == "delete") {
                    var $path = jQuery(this).data("path");
                    _this.createDialog("Delete all items", "Delete this file, it cannot be recoverd.", {
                        resizable: false,
                        height: 250,
                        modal: true,
                        buttons: {
                            "Delete": function () {
                                _this.load({
                                    mca_get: "deletefile",
                                    file: $path
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

                } else if (key == "settings") {
                    var $path = jQuery(this).data("path");


                    if (jQuery.isNumeric($path)) {
                        $path = $path.toString();
                    }
                    if (($path.indexOf(".") >= 0  ) || (  $path.indexOf(",") >= 0  ) || (  $path.indexOf("-") >= 0 ) || ( $path.indexOf("_") >= 0)) {
                        $path = $path.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ' ');
                    }
                    _this.createDialog("About this file", jQuery("." + $path).html(), {
                        resizable: false,
                        height: 250,
                        modal: true,
                        buttons: {
                            Cancel: function () {
                                jQuery(this).dialog("close");
                            }
                        }
                    });

                }
            },
            items: {
                "edit": {
                    name: "Edit",
                    icon: "edit"
                },
                /*"cut": {
                 name: "Cut",
                 icon: "cut"
                 },
                 "copy": {
                 name: "Copy",
                 icon: "copy"
                 },
                 "paste": {
                 name: "Paste",
                 icon: "paste"
                 },*/
                "rename": {
                    name: "Rename",
                    icon: "rename"
                },
                "delete": {
                    name: "Delete",
                    icon: "delete"
                },
                "sep1": "---------",
                "settings": {
                    name: "Settings",
                    icon: "settings"
                }
                /*,
                 "sep1": "---------",
                 "quit": {
                 name: "Quit",
                 icon: "quit"
                 }*/
            }
        });
    }
    this.toolbar = function () {
    }
    this.toolbar.reload = function () {
        _this.load({
            mca_get: "file",
        }, function (response) {
            jQuery("#editor").children("textarea").html(response).trigger('autosize.resize');
        });
    }
    this.toolbar.help = function () {
        jQuery("#toolbar .help").colorbox({
            title: "Wiki - Editor",
            iframe: true,
            width: "80%",
            height: "80%",
            href: "http://profenter.de/en/projekte/minecraft-admin/wiki/filebrowser"
        });

    }
    this.toolbar.save = function () {
        _this.load({
            mca_get: "savefile",
            inhalt: jQuery("#editor").children("textarea").html(),
        }, function (response) {
            jQuery.notify(response, "success");
        });
    }
    this.toolbar.delete = function () {
        _this.$delete.dialog("open");
    }
    this.Editor = function () {
    }
    this.Editor.back = function () {
        jQuery("#editor").animate({
            width: "0"
        }).fadeOut();
        jQuery("#fileview").animate({
            width: "100%"
        }).fadeIn();
        jQuery("#extras").animate({
            width: "320px"
        }).fadeIn();
        jQuery("#middle").animate({
            flexBasis: "80%",
            WebkitFlexBasis: "80%",
        });
    }
    this.infos = function () {
    }
    this.infos.click = function ($OBJ) {
        window.page = $OBJ.html();
        if ($OBJ.data("type") != "directory") {
            if ($OBJ.data("type") == "jpg" || $OBJ.data("type") == "jpeg" || $OBJ.data("type") == "png" || $OBJ.data("type") == "gif") {
                jQuery.when(_this.getpath($OBJ.data("path"))).done(function (a1) {
                    jQuery.colorbox({
                        title: window.page,
                        iframe: true,
                        width: "80%",
                        height: "80%",
                        href: _this.parse(a1)
                    });
                });
            } else if ($OBJ.data("type") == "pdf") {
                jQuery.when(_this.getpath($OBJ.data("path"))).done(function (a1) {
                    jQuery.colorbox({
                        title: window.page,
                        iframe: true,
                        width: "80%",
                        height: "80%",
                        href: _this.parse(a1)
                    });
                });
            } else {
                _this.edit($OBJ.data("path"));
            }
        } else {
            _this.loaddir(_this.path + $OBJ.data("path") + "/");
        }
    }
    this.contextmenudir = function ($OBJ) {
        jQuery.contextMenu({
            selector: '#fileview ul li.dir',
            callback: function (key, options) {
                window.page = $OBJ.html();
                if (key == "rename") {
                    var $path = $OBJ.data("path");
                    _this.createDialog("Rename", 'Rename this dir.<form><input name="rename" id="rename" placeholder="' + $OBJ.text() + '"></form>', {
                        resizable: false,
                        height: 250,
                        modal: true,
                        buttons: {
                            "Rename": function () {
                                _this.load({
                                    mca_get: "rename",
                                    file: $path,
                                    name: $OBJ.find("#rename").val(),
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
                } else if (key == "delete") {
                    var $path = $OBJ.data("path");
                    _this.createDialog("Delete this dir", "Removes this dir an it's content.", {
                        resizable: false,
                        height: 250,
                        modal: true,
                        buttons: {
                            "Delete all items": function () {
                                _this.load({
                                    mca_get: "deletedir",
                                    file: $path,
                                    name: $OBJ.find("#rename").val(),
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
            },
            items: {
                "rename": {
                    name: "Rename",
                    icon: "rename"
                },
                "delete": {
                    name: "Delete",
                    icon: "delete"
                }
            }
        });
    }
    this.setdialog = function () {
        _this.$download = jQuery("#dialog-form-download").dialog({
            autoOpen: false,
            width: 350,
            modal: true,
            buttons: {
                Cancel: function () {
                    jQuery(this).dialog("close");
                },
                "Send": function () {
                    jQuery.post(ajaxurl, {
                        action: 'mca_filebrowser',
                        nav: "servers",
                        subnav: "filebrowser",
                        mca_get: "download",
                        path: _this.path,
                        url: jQuery("#downloadurl").val(),
                        server: jQuery("#serverid").text()
                    }, function (response) {
                        jQuery.notify(response, "success");
                        jQuery(".toolbar .reload").click();
                    });
                    jQuery(this).dialog("close");
                }
            },
            close: function () {
                jQuery(this).dialog("close");
            }
        });
        _this.$new = jQuery("#dialog-form-new").dialog({
            autoOpen: false,
            width: 350,
            modal: true,
            buttons: {
                Cancel: function () {
                    jQuery(this).dialog("close");
                },
                "Send": function () {
                    jQuery.post(ajaxurl, {
                        action: 'mca_filebrowser',
                        nav: "servers",
                        subnav: "filebrowser",
                        mca_get: "createdir",
                        path: _this.path,
                        name: jQuery("#name").val(),
                        server: jQuery("#serverid").text()
                    }, function (response) {
                        jQuery.notify(response, "warn");
                        jQuery(".toolbar .reload").click();
                    });
                    jQuery(this).dialog("close");
                }
            },
            close: function () {
                jQuery(this).dialog("close");
            }
        });
        _this.$delete = jQuery("#dialog-confirm").dialog({
            autoOpen: false,
            resizable: false,
            height: 200,
            modal: true,
            buttons: {
                "Delete": function () {
                    jQuery.post(ajaxurl, {
                        action: 'mca_filebrowser',
                        nav: "servers",
                        subnav: "filebrowser",
                        mca_get: "deletefile",
                        path: _this.path,
                        file: _this.file,
                        server: jQuery("#serverid").text()
                    }, function (response) {
                        jQuery.notify(_this.parse(response), "warn");

                    });
                    jQuery("#filebrowser .back").click();
                    jQuery("#toolbar .reload").click();
                    jQuery(this).dialog("close");
                },
                Cancel: function () {
                    jQuery(this).dialog("close");
                }
            }
        });
        _this.$newfile = jQuery("#dialog-form-file-new").dialog({
            autoOpen: false,
            width: 350,
            modal: true,
            buttons: {
                Cancel: function () {
                    jQuery(this).dialog("close");
                },
                "Send": function () {
                    jQuery.post(ajaxurl, {
                        action: 'mca_filebrowser',
                        nav: "servers",
                        subnav: "filebrowser",
                        mca_get: "createfile",
                        path: _this.path,
                        name: jQuery("#filename").val(),
                        server: jQuery("#serverid").text()
                    }, function (response) {
                        jQuery.notify(response, "warn");
                        jQuery(".toolbar .reload").click();
                    });
                    jQuery(this).dialog("close");
                }
            },
            close: function () {
                jQuery(this).dialog("close");
            }
        });

    }
    this.init = function () {

        _this.setdialog();
        jQuery(document).tooltip({
            items: ".file",
            track: true,
            close: function () {
            },
            position: { my: "left top+15", at: "left bottom", collision: "flipfit" },
            content: function () {
                var element = jQuery(this);
                var $path = element.data("path");
                var endung = element.data("type");
                if (endung == "pdf") {
                    jQuery.when(_this.getpath($OBJ.data("path"))).done(function (a1) {
                        return '<iframe height="300" src="' + _this.parse(a1) + '"></iframe>';
                    });
                } else if (endung == "plain") {
                    /*
                     * TODO URL anpassen
                     */
                    return '<img width="300" src="../wp-content/plugins/minecraft-admin/show.extern.php?output=image&server=1&blog=1&file=' + $path + '" />';
                } else if (endung == "x-empty") {
                    return '<h2>No content.</h2>';
                } else if ((endung == "jpg") || (endung == "jpeg") || (endung == "png") || (endung == "gif")) {
                    return '<img width="300" src="' + _this.getpath(element.data("path")) + '" />';
                } else if (endung == "directory") {
                    return '<img class="dir" src="data:image/png;base64,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" />';
                }
            }
        });
    };
    this.reloadPath = function () {
        _this.load({
            mca_get: "filetree",
        }, function (response) {
            jQuery("#toolbar .content").html(response);
        });
    }
    this.downloadFile = function () {
        _this.$download.dialog("open");
    }
    this.createFile = function () {
        _this.$newfile.dialog("open");
    }
    this.createDir = function () {
        _this.$new.dialog("open");
    }
    this.breadcrumbs.click = function ($OBJ) {
        _this.loaddir($OBJ.data("url"));
    }
}


jQuery(document).ready(function ($) {

    var $MCA = new MCA.Filebrowser();
    $MCA.init();
    $MCA.contextmenunormal();
    jQuery(document).on("click", " #fileview ul li", function () {
        $MCA.infos.click(jQuery(this));
    });
    jQuery("#filebrowser .back").on("click", function () {
        $MCA.Editor.back();
    });
    jQuery(".toolbar .reload").on("click", function () {
        $MCA.toolbar.reload();
    });
    $MCA.toolbar.help();
    jQuery(".toolbar .save").on("click", function () {
        $MCA.toolbar.save();
    });
    jQuery(".toolbar .delete").on("click", function () {
        $MCA.toolbar.delete();
    });
    jQuery("#toolbar .reload").on("click", function () {
        $MCA.reloadPath();
    });
    jQuery(document).on("click", "#breadcrumbs a", function () {
        $MCA.breadcrumbs.click($(this));
    });
    jQuery("#fileview .new").on("click", function () {
        $MCA.createDir();
    });
    jQuery("#fileview .newfile").on("click", function () {
        $MCA.createFile();
    });
    jQuery("#fileview .download").on("click", function () {
        $MCA.downloadFile();
    });
});
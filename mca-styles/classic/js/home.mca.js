MCA.Home = function () {
    var _this = this;
    var SETTINGS = [];
    this.paras = {
        action: 'mca_home',
        path: _this.path,
        nav: "home",
        server: jQuery("#serverid").text()
    }
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
            action: 'mca_home',
            path: _this.path,
            nav: "home",
            server: jQuery("#serverid").text()
        };
        return jQuery.post(ajaxurl, _this.merge_options(paras, PARAMTERS), FUNCTION);
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
    };
    this.update = function () {
        _this.load({action: "updateinfo"}, function (data) {
            data = jQuery.parseJSON(_this.parse(data));
            if (data.update == "yes") {

                jQuery.colorbox({
                    html: "<div>" + data.text + "</div>",
                    title: "Update to " + data.version,
                    onClosed: function () {

                        jQuery('#lizenz').append('<div id="dialog-confirm" title="Empty the recycle bin?">    ' +
                                '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Nimmst du die Lizenz an?</p> ' +
                                '</div>').children('#dialog-confirm').dialog({

                        });
                    },
                    width: "80%",
                    height: "80%",
                });
            }

        });
    }
};
jQuery(document).ready(function ($) {
    var $MCA = new MCA.Home();
    $MCA.init();


    $MCA.update();

});
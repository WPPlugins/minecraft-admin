MCA.Befehle = function () {
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
            nav: "servers",
            subnav: "befehle"
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
    this.tabs = function () {
        jQuery("#tabs").tabs();
    };
    this.buttons = function (OBJ) {
        var name = "";
        var sPageURL = OBJ.attr("href");
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == "p") {
                var p = sParameterName[1];
            }
            if (sParameterName[0] == "name") {
                name = sParameterName[1];
            }
        }
        _this.load({
            action: "buttons",
            button: p,
            name: name,
            serverid: jQuery(document).getUrlParam("serverid")
        }, function (response) {
            jQuery.notify(response, "success");
        });
        return false;
    }
    this.textarea = function () {
        setInterval(function () {
            _this.load({
                action: "log",
                serverid: jQuery(document).getUrlParam("serverid")
            }, function (response) {
                jQuery("#mc_core_log textarea").html(response);
            });
        }, 1000);
    }
    this.forms = function (OBJ) {
        var inputs = [];
        OBJ.children('input').each(function () {
            inputs.push({
                'name': jQuery(this).attr("name"),
                'val': jQuery(this).val()
            });
        });
        OBJ.children("select").children('option:selected').each(function () {
            inputs.push({
                'name': jQuery(this).parent().attr("name"),
                'val': jQuery(this).val()
            });
        });
        _this.load({
            action: "inputs",
            inputs: inputs,
            serverid: jQuery(document).getUrlParam("serverid")
        }, function (response) {
            jQuery.notify(response, "success");
        });


    }
};
jQuery(document).ready(function ($) {
    var $MCA = new MCA.Befehle();
    $MCA.tabs();
    $MCA.textarea();
    jQuery("#tabs .button").click(function () {
        $MCA.buttons($(this));
        return false;
    });
    jQuery("#tabs form").submit(function (event) {
        $MCA.forms($(this));
        event.preventDefault();
        return false;
    });
});
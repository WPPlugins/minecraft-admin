MCA = function () {
    var _this = this;
    var paras = {
        nav: "settings",
    };
    this.setParameter = function (para) {
        paras = para;
    };
    this.addParameter = function (para) {
        paras = _this.merge_options(_this.paras, para);
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
        return jQuery.when(jQuery.post(ajaxurl, _this.merge_options(_this.paras, PARAMTERS), FUNCTION)).done(function (a1) {
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
    this.init = function () {
        if (jQuery(".postbox").length == 1) {
            jQuery(".postbox").css({
                maxWidth: "100%"
            });
        } else if (jQuery(".postbox").length == 3) {
            jQuery(".postbox").css({
                maxWidth: "27%"
            });
        }
        jQuery("#wpfooter").css({
            backgroundColor: jQuery("#adminmenuwrap").css("backgroundColor")
        }).children("#footer-left").children("#footer-thankyou").html('Minecraft Admin &copy; <a href="http://profenter.de">Profenter Systems</a>');
    }
};
jQuery.fn.extend({
    /**
     * Returns get parameters.
     *
     * If the desired param does not exist, null will be returned
     *
     * To get the document params:
     * @example value = jQuery(document).getUrlParam("paramName");
     *
     * To get the params of a html-attribut (uses src attribute)
     * @example value = jQuery('#imgLink').getUrlParam("paramName");
     */
    getUrlParam: function (strParamName) {
        strParamName = escape(unescape(strParamName));

        var returnVal = new Array();
        var qString = null;

        if (jQuery(this).attr("nodeName") == "#document") {
            //document-handler

            if (window.location.search.search(strParamName) > -1) {

                qString = window.location.search.substr(1, window.location.search.length).split("&");
            }

        } else if (jQuery(this).attr("src") != "undefined") {

            var strHref = jQuery(this).attr("src")
            if (strHref.indexOf("?") > -1) {
                var strQueryString = strHref.substr(strHref.indexOf("?") + 1);
                qString = strQueryString.split("&");
            }
        } else if (jQuery(this).attr("href") != "undefined") {

            var strHref = jQuery(this).attr("href")
            if (strHref.indexOf("?") > -1) {
                var strQueryString = strHref.substr(strHref.indexOf("?") + 1);
                qString = strQueryString.split("&");
            }
        } else {
            return null;
        }


        if (qString == null) return null;


        for (var i = 0; i < qString.length; i++) {
            if (escape(unescape(qString[i].split("=")[0])) == strParamName) {
                returnVal.push(qString[i].split("=")[1]);
            }

        }


        if (returnVal.length == 0) return null;
        else if (returnVal.length == 1) return returnVal[0];
        else return returnVal;
    }
});

jQuery(document).ready(function ($) {
    var $MCA = new MCA();
    $MCA.init();
});
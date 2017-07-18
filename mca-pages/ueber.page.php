<?php

/**
 * Minecraft Admin
 *
 * @category   MCA_pages
 * @package    MCA Minecraft Admin
 * @name       Minecraft Admin
 * @author     Profenter Systems
 * @copyright  2012-2013 Profenter Systems "service@profenter.de"
 * @version    see http://wordpress.org/plugins/minecraft-admin/changelog/
 * @license    http://profenter.de/profenter/lizenzierung/psl-1-1 PSL (Profenter Systems License) 1.1
 * @link       http://profenter.de/projekte/minecraft-admin
 * @see        http://wordpress.org/plugins/minecraft-admin
 * @since      File available since Release 0.7.9
 * @deprecated File not deprecated
 * @Todo see @http://profenter.de/projekte/minecraft-admin#todo
 *
 *
 * @class ueber_page
 * @name shows the "about" page and shows debug infos
 * @version 0.0.1
 */
class ueber_page extends pages
{
    function setup()
    {
        $debuginfos = "";
        if (isset($_SERVER["HTTP_USER_AGENT"])) {
            $debug["HTTP_USER_AGENT"] = $_SERVER["HTTP_USER_AGENT"];
        } //isset($_SERVER["HTTP_USER_AGENT"])
        if (isset($_SERVER["HTTP_REFERER"])) {
            $debug["HTTP_REFERER"] = $_SERVER["HTTP_REFERER"];
        } //isset($_SERVER["HTTP_REFERER"])
        if (isset($_SERVER["SERVER_SIGNATURE"])) {
            $debug["SERVER_SIGNATURE"] = $_SERVER["SERVER_SIGNATURE"];
        } //isset($_SERVER["SERVER_SIGNATURE"])
        if (isset($_SERVER["SERVER_NAME"])) {
            $debug["SERVER_NAME"] = $_SERVER["SERVER_NAME"];
        } //isset($_SERVER["SERVER_NAME"])
        if (isset($_SERVER["SERVER_ADDR"])) {
            $debug["SERVER_ADDR"] = $_SERVER["SERVER_ADDR"];
        } //isset($_SERVER["SERVER_ADDR"])
        if (isset($_SERVER["DOCUMENT_ROOT"])) {
            $debug["DOCUMENT_ROOT"] = $_SERVER["DOCUMENT_ROOT"];
        } //isset($_SERVER["DOCUMENT_ROOT"])
        if (isset($_SERVER["SCRIPT_FILENAME"])) {
            $debug["SCRIPT_FILENAME"] = $_SERVER["SCRIPT_FILENAME"];
        } //isset($_SERVER["SCRIPT_FILENAME"])
        if (isset($_SERVER["QUERY_STRING"])) {
            $debug["QUERY_STRING"] = $_SERVER["QUERY_STRING"];
        } //isset($_SERVER["QUERY_STRING"])
        if (isset($_SERVER["SCRIPT_NAME"])) {
            $debug["SCRIPT_NAME"] = $_SERVER["SCRIPT_NAME"];
        } //isset($_SERVER["SCRIPT_NAME"])
        if (isset($_SERVER["PHP_SELF"])) {
            $debug["PHP_SELF"] = $_SERVER["PHP_SELF"];
        } //isset($_SERVER["PHP_SELF"])
        if (isset($_SERVER["REQUEST_URI"])) {
            $debug["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
        } //isset($_SERVER["REQUEST_URI"])
        if (function_exists('php_uname')) {
            $debug["SERVER_OS_UNAME"] = php_uname('s');
        } //function_exists('php_uname')
        if (PHP_OS != "") {
            $debug["SERVER_OS_PHP"] = PHP_OS;
        } //PHP_OS != ""
        if (isset($_SERVER["SERVER_SOFTWARE"])) {
            $debug["SERVER_OS_SOFTWARE"] = $_SERVER['SERVER_SOFTWARE'];
        } //isset($_SERVER["SERVER_SOFTWARE"])
        foreach ($debug as $name => $inhalt) {
            $debuginfos .= $name . "===" . $inhalt . "\n";
        } //$debug as $name => $inhalt
        $vars = array(

            "plugin" => __("Plugin", "minecraft-admin"),
            "author" => __("autor", "minecraft-admin"),
            "helpvideo" => __("Helpvideo", "minecraft-admin"),
            "debuginfos" => __("Debuginfos", "minecraft-admin"),
            "version" => MCA_VERSION,
            "debug" => $debuginfos,
            "imageurl" => MC_WEB_INCLUDE . "/images/",
            "name" => __("Minecraft Admin", 'minecraft-admin')
        );
        $this->vars = $vars;
    }
}

?>
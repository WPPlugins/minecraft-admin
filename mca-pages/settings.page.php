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
 * @Todo see @http://profenter.de/projekte/minecraft-admin#todo
 *
 *
 * @class settings_page
 * @name settings page
 * @version 0.0.4
 */
class settings_page extends pages
{
    function ajax()
    {
        MCADB::set('mca_common');
        if ($_POST["action"] == "mca_uninstall") {
            if (is_file(MCA_PLUGIN_DIR_INCLUDE . "/mca-install/.uninstalled")) {
                echo "ja";
            } else {
                echo "nein";
            }
        } else if ($_POST["action"] == "changedesign") {
            if (isset($_POST['name'])) {
                MCADB::updateOption("value", $_POST["name"], array("type" => "style"));
                echo "Design geändert";
            } //isset($_POST['settings'])
        } else if ($_POST["action"] == "uninstall") {
            if (is_dir(MCA_MINECRAFT_DIR . "servers")) {
                $d = dir(MCA_MINECRAFT_DIR . "servers");
                while (false !== ($entry = $d->read())) {

                    if ($entry != "." && $entry != ".." && is_dir(MCA_MINECRAFT_DIR . "servers/" . $entry)) {
                        MCADB::remove("mca_server_" . $entry);
                    }
                }
            }
            $d->close();
            MCADB::remove("mca_common");
            rmdir(MCA_MINECRAFT_DIR);
        }
    }

    function setup()
    {
        MCADB::set('mca_common');
        $style = MCADB::getOption("value", array("type" => "style"));
        $d = dir(MCA_STYLES_DIR);
        while (false !== ($entry = $d->read())) {
            if ($entry != "." && $entry != "..") {
                if (is_file(MCA_STYLES_DIR . $entry . "/style.php")) {
                    $tmp = new styles($entry);
                    $tmp->loadConfig();
                    $styles_array[$entry] = $tmp->createInfo();
                }

            }
        }
        $d->close();


        if (isset($_POST['submit'])) {
            if (isset($_POST['mc_settings_options']["users"])) {
                MCADB::updateOption("value", $_POST['mc_settings_options']["users"], array("type" => "users"));
            }
        } //isset($_POST['settings'])
        $vars = array(
            "installed" => MCADB::getOption("value", array("type" => "installed")),
            "style" => $style,
            "lochenfrage" => __("Do you really want to remove all data of Minecraft Admin?", "minecraft-admin"),
            "remove_link" => "cd " . MCA_PLUGIN_DIR_INCLUDE . "/mca-install/ && bash ./setup.sh remove",
            "remove_link_e" => __("Run this code to finish uninstalling:", "minecraft-admin"),
            "loading_e" => __("Waiting for uninstalling...", "minecraft-admin"),
            "lochen_e" => __("Uninstall.", "minecraft-admin"),
            "lochen_e2" => __("Fininsh uninstalling. This will remove <b>ALL</b> Data. Take a backup before.", "minecraft-admin"),
            "auszusuchendestyles" => $styles_array,
            "eusers" => __('Users', 'minecraft-admin'),
            "users" => MCADB::getOption("value", array("type" => 'users')),
            "users_info" => MCAF::mc_core_info("users"),
            "lochen_beschreibung_e" => __("Remove all data from Minecraft Admin. This includes the plugins settings and the server data.", "minecraft-admin"),
            "change_style_heading_e" => __("Change style", "minecraft-admin"),
            "change_style_e" => __("Change the style of Minecraft Admin. The 'default' style was create by the developers of Minecraft Admin. Using an other style can break Minecraft Admin.", "minecraft-admin")
        );
        $vars["textarea"] = __("Settings removed correctly.", "minecraft-admin") . __("Removed all Data. Delete this plugin through the WordPress plugin function. Thank you for using Minecraft Admin.", "minecraft-admin");

        $this->vars = $vars;
    }
}

?>

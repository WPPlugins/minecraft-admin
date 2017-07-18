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
 * @class servers_settings_page
 * @name settings page for each mc server
 * @version 0.0.1
 */
class servers_settings_page extends pages
{
    function setup()
    {
        global $blog_id;
        MCADB::set('mca_server_' . $blog_id . '_' . $this->serverid);
        $mc_updated = false;
        if (isset($_POST['settings'])) {
            foreach ($_POST['mc_settings_options'] as $set => $wert) {
                if (null != MCADB::getOption("value", array("type" => $set)) && $set != "pfad") {
                    if ($set == "server") {
                        if ($wert != MCADB::getOption("value", array("type" => $set))) {
                            $cmd = new MCAB("jar");
                            $cmd->setServer($this->serverid)->setParameter($wert)->exec();
                        } //$wert != MCADB::getOption("value", array( "type" => $set ))
                    } //$set == "server"
                    if ($set == "ram") {
                        if ($wert != MCADB::getOption("value", array("type" => $set))) {
                            $cmd = new MCAB("config");
                            $cmd->setServer($this->serverid)->setParameter("msm-ram " . $wert)->exec();
                        } //$wert != MCADB::getOption("value", array( "type" => $set ))
                    } //$set == "server"
                    MCADB::updateOption("value", $wert, array("type" => $set));
                } //null != MCADB::getOption("value", array( "type" => $set )) && $set != "pfad"
            } //$_POST['mc_settings_options'] as $set => $wert
            $mc_updated = true;
        } //isset($_POST['settings'])
        $vars = array(
            "settings" => __('Settings', 'minecraft-admin'),
            "updated" => __('Settings updated.', 'minecraft-admin'),
            "action" => MCAF::mc_url(),
            "path" => __('Path', 'minecraft-admin'),
            "pfad" => MCA_MINECRAFT_DIR . 'servers/' . $blog_id . '_' . $this->serverid . "/",
            "anzahlpfad" => strlen(MCA_MINECRAFT_DIR . 'servers/' . $blog_id . '_' . $this->serverid . "/"),
            "pfad_info" => MCAF::mc_core_info("Pfad"),
            "elogs" => __('Logs', 'minecraft-admin'),
            "logs" => MCADB::getOption("value", array("type" => 'log')),
            "logs_info" => MCAF::mc_core_info("log"),
            "eram" => __('RAM', 'minecraft-admin'),
            "ram" => MCADB::getOption("value", array("type" => 'ram')),
            "RAM_info" => MCAF::mc_core_info("RAM"),
            "ebackup" => __('Backup', 'minecraft-admin'),
            "backuppfad" => MCA_MINECRAFT_DIR . 'archives/backups/' . $blog_id . '_' . $this->serverid . "/",
            "backup_info" => MCAF::mc_core_info("backup"),
            "anzahlbackup" => strlen(MCA_MINECRAFT_DIR . 'archives/backups/' . $blog_id . '_' . $this->serverid . "/"),
            "eserver" => __('Server', 'minecraft-admin'),
            "server" => MCADB::getOption("value", array(
                    "type" => 'server'
                )),
            "server_info" => MCAF::mc_core_info("server"),
            "ename" => __('Name', 'minecraft-admin'),
            "name" => MCADB::getOption("value", array(
                    "type" => 'name'
                )),
            "name_info" => MCAF::mc_core_info("name"),
            "save_changes" => __('save changes', 'minecraft-admin'),
            "installed" => $this->MC["server"]["installed"],
            "selected" => MCADB::getOption("value", array("type" => 'server')),
            "mc_updated" => $mc_updated,
            "jarlist" => MCAF::jarlist()
        );
        $this->vars = $vars;
    }
}

?>
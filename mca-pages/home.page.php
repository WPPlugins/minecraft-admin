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
 * @class home_page
 * @name overview page
 * @version 0.0.5
 */
class home_page extends pages
{
    function setup()
    {
        global $blog_id;
        $ver = explode(" ", shell_exec('msm version'));
        if ($ver == "msm:") {
            MCADB::set('mca_common');
            MCADB::updateOption("value", "nein", array("type" => "installed"));
            unlink(MCA_MINECRAFT_DIR . '.installed');
            echo '<meta http-equiv="refresh" content="0" />';
        }


        if (isset($_POST["server"])) {
            MCADB::set('mca_common');
            $servers = unserialize(MCADB::getOption("value", array(
                "type" => "servers"
            )));
            $anzahl = count($servers[$blog_id]) + 1;
            MCADB::add('mca_server_' . $blog_id . '_' . $anzahl, array(
                'id' => "int NOT NULL AUTO_INCREMENT ",
                'type' => "VARCHAR(50) DEFAULT '' NOT NULL",
                'value' => "TEXT DEFAULT '' NOT NULL"
            ), 'id');
            MCADB::set('mca_server_' . $blog_id . '_' . $anzahl);
            MCADB::setOption(array(
                "type" => "server",
                "value" => $_POST['server']
            ));
            MCADB::setOption(array(
                "type" => "name",
                "value" => $_POST['server'] . "_Server_" . $anzahl
            ));
            MCADB::setOption(array(
                "type" => "ram",
                "value" => "512m"
            ));
            MCADB::setOption(array(
                "type" => "cpu",
                "value" => "1"
            ));
            MCADB::setOption(array(
                "type" => "worlds",
                "value" => "world"
            ));
            MCADB::setOption(array(
                "type" => "log",
                "value" => "100"
            ));
            MCADB::setOption(array(
                "type" => "pfad",
                "value" => MCA_MINECRAFT_DIR . $blog_id . "/" . $anzahl . "/"
            ));
            MCADB::set('mca_common');
            $servers = unserialize(MCADB::getOption("value", array(
                "type" => "servers"
            )));
            $servers[$blog_id][] = $anzahl;
            MCADB::updateOption("value", serialize($servers), array(
                "type" => "servers"
            ));
            MCAF::install_server($anzahl);
        } //isset($_POST["server"])
        if (isset($_GET["mc_server_del"])) {
            $cmd = new MCAB("stop");
            $cmd->setServer($this->serverid)->exec();
            MCADB::remove('mca_server_' . $blog_id . '_' . $_GET["mc_server_del"]);
            MCAF::remove_server($_GET["mc_server_del"]);
            MCADB::set('mca_common');
            $servers = unserialize(MCADB::getOption("value", array(
                "type" => "servers"
            )));
            unset($servers[$blog_id][$_GET["mc_server_del"]]);
            MCADB::updateOption("value", serialize($servers), array(
                "type" => "servers"
            ));
        } //isset($_GET["mc_server_del"])
        include_once(ABSPATH . WPINC . '/feed.php');
        add_filter('wp_feed_cache_transient_lifetime', 'rss_cache');


        $rss = fetch_feed(PROFENTERDE . 'blog/category/minecraft-admin/downloads/feed');
        if (!is_wp_error($rss)) {
            $maxitems = $rss->get_item_quantity(1);
            $rss_items = $rss->get_items(0, $maxitems);
        } //!is_wp_error($rss)
        if ($maxitems == 0) {
            $content = __('No items', 'minecraft-admin');
        } //$maxitems == 0
        else {
            foreach ($rss_items as $item) {
                $version = str_replace("Minecraft Admin", "", $item->get_title());
                $version = str_replace(" ", "", $version);
                $version = preg_replace('/[^0-9\.\-]+/', '', $version);
                $MCA_VERSION = preg_replace('/[^0-9\.\-]+/', '', MCA_VERSION);
                if ($version == $MCA_VERSION) {
                    $uptodate = __("Same version. No update available.", "minecraft-admin");
                } //$version == $MCA_VERSION
                elseif (version_compare($version, $MCA_VERSION, '>')) {
                    $uptodate = __("You can update if you want.", "minecraft-admin");
                } //version_compare($version, $MCA_VERSION, '>')
                else {
                    $uptodate = __("You are newer? What did you do?", "minecraft-admin");
                }
                $link = esc_url($item->get_permalink());
                $content = $item->get_content();
                $title = $version;
                $linkcontent = sprintf(__('Posted %s', 'minecraft-admin'), $item->get_date('j F Y | g:i a'));
            } //$rss_items as $item
        }


        $rss2 = fetch_feed(PROFENTERDE . 'blog/category/minecraft-admin/feed');
        //$rss2->force_feed(true);
        if (!is_wp_error($rss2)) {
            $maxitems2 = $rss2->get_item_quantity(1);
            $rss_items2 = $rss2->get_items(0, $maxitems2);
        } //!is_wp_error($rss)
        if ($maxitems2 == 0) {
            $contentnews = __('No items', 'minecraft-admin');
        } //$maxitems == 0
        else {

            foreach ($rss_items2 as $item) {
                $linknews = esc_url($item->get_permalink());
                $contentnews = $item->get_content();
                $titlenews = $item->get_title();
                $linkcontentnews = sprintf(__('Posted %s', 'minecraft-admin'), $item->get_date('j F Y | g:i a'));
            }
        }


        $this->vars = array(
            "content" => $content,
            "uptodate" => $uptodate,
            "linkcontent" => $linkcontent,
            "title" => $title,
            "link" => $link,
            "versionlist" => array(
                __("improved design", "minecraft-admin"),
                __("new command interface", "minecraft-admin"),
                __("added a lot of new options to command interface", "minecraft-admin"),
                __("added new boxes at the start screen", "minecraft-admin"),
                __("new uninstaller", "minecraft-admin"),
                __("new installer", "minecraft-admin")
            ),
            "contentnews" => $contentnews,
            "linkcontentnews" => $linkcontentnews,
            "titlenews" => $titlenews,
            "linknews" => $linknews,
            "donate_e" => __("donate", "minecraft-admin"),
            "version_e" => __("new in this version", "minecraft-admin"),
            "donate_dis" => __("Minecraft Admin is free and should stay free.<br /> For this we need your help!<br /> We spend a lot of time and expensive resources to develop Minecraft Admin.<br /> Help us by donating us a small contribution.<br /> Every little donation helps to develop Minecraft Admin and keeps it free.", "minecraft-admin"),
            "last_e" => __("newst version available", "minecraft-admin"),
            "deletelink" => __("Delete link", "minecraft-admin"),
            "name" => __("Name", "minecraft-admin"),
            "minecraftservers" => __('Minecraft Servers', 'minecraft-admin'),
            "minecraftserver" => __('Minecraft server', 'minecraft-admin'),
            "newminecraftserver" => __('Create a new Minecraft Server', 'minecraft-admin'),
            "choose" => __("Choose", "minecraft-admin"),
            "delete" => __("Delete", "minecraft-admin"),
            "minecraftadmin" => __("Minecraft Admin", "minecraft-admin"),
            "mcurl" => MCAF::mc_url("home"),
            "version" => MCA_VERSION,
            "newversion" => $version,
            "servers" => MCAF::servers(),
            "attention" => __("", "minecraft-admin"),
            "e_new" => __("create a new server", "minecraft-admin"),
            "change_name_e" => __("Change name", "minecraft-admin"),
            "nachricht" => __('Do you really want remove this server? <a class="ja">Yes!</a><a class="nein">No</a>', "minecraft-admin"),
            "networkadmin" => (is_network_admin()) ? "ja" : "nein",
            "jarlist" => MCAF::jarlist()
        );

    }

    public function ajax()
    {
        if ($_POST["action"] == "updateinfo") {
            if (!is_file(MCA_MINECRAFT_DIR . "/.upgraded") && file_get_contents(MCA_MINECRAFT_DIR . "/.upgraded") != MCA_VERSION)
                $array = array("update" => "yes",
                    "version" => MCA_VERSION,
                    "text" => __('<h1> Upgrading Minecraft Admin to ' . MCA_VERSION . '</h1>
                Minecraft Admin must be upgraded. This makes new functions in this release necessary. Perform the corresponding command on your server:<br />
                Ubuntu: <input type="text" style="position: relative; width: 710px;" value="cd ' . MCA_PLUGIN_DIR_INCLUDE . '/mca-install/ && bash setup.sh -u ubuntu" /><br />
                RedHat: <input type="text" style="position: relative; width: 710px;" value="cd ' . MCA_PLUGIN_DIR_INCLUDE . '/mca-install/ && bash setup.sh -u redhat" /><br />
                <br />
                <strong>Errors may occur if you do not perform this upgrade.</strong>
                ', "minecraft-admin"));
            echo json_encode($array);
        }
    }

    public function rss_cache($seconds)
    {
        return 3600;
    }
}

?>
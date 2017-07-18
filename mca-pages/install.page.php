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
 * @class install_page
 * @name installs Minecraft Admin
 * @version 0.0.10
 */
class install_page extends pages
{
    function ajax()
    {
        if ($_POST["action"] == "install") {
            if (is_file(MCA_MINECRAFT_DIR . "/.installed")) {
                echo "ja";
            } else {
                echo "nein";
            }
        } elseif ($_POST["action"] == "installlog") {
            if (is_file(MCA_PLUGIN_DIR_INCLUDE . '/mca-install/.install_log')) {
                $file = file(MCA_PLUGIN_DIR_INCLUDE . '/mca-install/.install_log');
                for ($i = count($file); $i > 0; $i--) {
                    echo $file[$i];
                }

            }
        } elseif ($_POST["action"] == "installstart") {
            if (is_file(MCA_MINECRAFT_DIR . "/.started")) {
                echo "installing";
            } else {
                echo "not installing";
            }
        }
    }
    function setup()
    {
        global $blog_id;
        $_GET["mca_install"] = (isset($_GET["mca_install"])) ? $_GET["mca_install"] : "";
        $this->vars = array(
            "schritt" => (!empty($_GET["mca_install"]) ? $_GET["mca_install"] : 1),
            "minecraftadmin" => __("Minecraft Admin", "minecraft-admin"),
            "version" => MCA_VERSION,
            "deb" => __("Debian/Ubuntu", "minecraft-admin"),
            "red" => __("Redhat", "minecraft-admin"),
            "others" => __("Others", "minecraft-admin"),
            "setup" => __("Setup", "minecraft-admin"),
            "help_h" => __("Help", "minecraft-admin"),
            "update_h" => __("Update", "minecraft-admin"),
            "intro" => __('Welcome to the installer of the WordPress plugin "Minecraft Admin".<br />
										<br />
										<br />
										In the following you\'ll go through a few steps to install Minecraft Admin.<br />
										Please follow the installation and do not break before finishing, otherwise errors may occur.<br />
										<br />
										Have fun now,<br />
										Profenter Systems<br />
										(developer of the plugin)', "minecraft-admin"),
            "help" => __('You can get help at profenter.de', "minecraft-admin"),
            "ok" => __('All checks were successful.', "minecraft-admin"),
            "install_e" => __("install now", "minecraft-admin"),
            "update_e" => __("update now", "minecraft-admin"),
            "install_e2" => __("next", "minecraft-admin"),
            "install_e3" => __("install", "minecraft-admin"),
            "install_e4" => __("finish", "minecraft-admin"),
            "install_e5" => __("start", "minecraft-admin"),
            "schritt_e" => __("step", "minecraft-admin"),
            "hinweis_e" => __("This new version doesn't import old settings of fewer versions.", "minecraft-admin"),
            "stepto2url" => MCAF::mcurl("mca_install", "2"),
            "stepto3url" => MCAF::mcurl("mca_install", "3"),
            "stepto4url" => MCAF::mcurl("mca_install", "4"),
            "stepto5url" => MCAF::mcurl("mca_install", "5"),
            "steptoupdateurl" => MCAF::mcurl("mca_install", "Update"),
            "steptofertigurl" => MCAF::mc_url(array(
                    "nav" => "home"
                )),
            "agb" => __('Minecraft Admin is released under the <a href="http://profenter.de/profenter/lizenzierung/psl-1-1">PSL 1.1</a>.', "minecraft-admin")
        );

        if ($_GET["mca_install"] == "Update") {
            if (is_dir(MCA_MINECRAFT_DIR)) {
                if ($dh = opendir(MCA_MINECRAFT_DIR)) {
                    while (($file = readdir($dh)) !== false) {

                        if (is_dir(MCA_MINECRAFT_DIR . '/' . $file) && strlen($file) == 1 && is_int((int)$file) && $file != ".") {
                            if ($dh2 = opendir(MCA_MINECRAFT_DIR . '/' . $file)) {
                                while (($file2 = readdir($dh2)) !== false) {
                                    if (strlen($file2) == 1 && is_int((int)$file2) && $file2 != ".") {
                                        shell_exec('/etc/init.d/minecraft ' . $file . ' ' . $file2 . ' stop');
                                    }

                                }
                                closedir($dh2);
                            }
                        }
                    }
                    closedir($dh);
                }
            }

            MCAF::zip_dir(MCA_MINECRAFT_DIR, MCA_MINECRAFT_DIR . '/../mca_backup.zip');

            $this->vars["backup_url"] = get_bloginfo("wpurl") . '/wp-content/mca_backup.zip';
            $this->vars["backupbutton_e"] = __("Download a backup of your Minecraft Files", 'minecraft-admin');
            $this->vars["updatenow_e"] = __("Update now", 'minecraft-admin');
            $this->vars["stepupdate_message"] = sprintf(__("We've stopped all server and created a backup of your Minecraft files. You can download it, if possible.
                                                    If not, the backup archive won't be delete. After this, push the 'Do update now' button.
                                                    We move your old Minecraft Files in a temporary dir and start with a fresh installation of MCA. When you
                                                    finished this installation we will migrate your old data to the new System.<br><br>Please execute this
                                                    command: %s", 'minecraft-admin'), 'cd ' . MCA_PLUGIN_DIR_INCLUDE . '/mca-install/ && bash update.sh');
        } elseif ($_GET["mca_install"] == 2) {
            if (is_dir(MCA_MINECRAFT_DIR)) {
                rename(MCA_MINECRAFT_DIR, ((substr(MCA_MINECRAFT_DIR, -1) == "/") ? substr(MCA_MINECRAFT_DIR, 0, -1) : MCA_MINECRAFT_DIR) . '.update');
            }
        } //$_GET["mca_install"] == 3
        elseif ($_GET["mca_install"] == 3) {
            @mkdir(MCA_MINECRAFT_DIR);
            if (!is_dir(MCA_MINECRAFT_DIR) && !is_writable(MCA_MINECRAFT_DIR)) {
                $this->vars["step3_wirteable"] = '<div class="error">' . __("The Minecraft Admin folder isn't wirteable. Please change the permissions so the Minecraft Admin folder is wirteable.", 'minecraft-admin') . '</div>';
            }
        } //$_GET["mca_install"] == 3
        elseif ($_GET["mca_install"] == 4) {
            if (is_dir(MCA_MINECRAFT_DIR) && is_writable(MCA_MINECRAFT_DIR)) {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $this->vars["step4_os"] = "win";
                    if (is_dir("C:\Program Files\Java\jre7")) {
                        $this->vars["step4_intro"] = __("We detected you are using MS Windows. Minecraft Admin support is under developing at the moment. So be carefully.", 'minecraft-admin');
                        $datei = fopen(MCA_MINECRAFT_DIR . '.installed', "w+");
                        fwrite($datei, "installed", 100);
                        fclose($datei);
                    } else {
                        $this->vars["step4_error"] = __("We didn't found Java. Please install it on your own.", 'minecraft-admin');
                    }
                } else {
                    $this->vars["step4_os"] = "linux";
                    if (!is_file(MCA_MINECRAFT_DIR . '.installed')) {
                        $installer = '#!/bin/bash
                                            msm_dir="' . MCA_MINECRAFT_DIR . '"
                                            msm_user="' . exec('whoami') . '"
                                            mca_inst="' . MCA_MINECRAFT_DIR . '.installed"
                                            mca_start="' . MCA_MINECRAFT_DIR . '.started"';
                        $this->vars["loading_e"] = __("Waiting for installing...", "minecraft-admin");
                        MCAF::create_file('php-infos.sh', $installer, MCA_MINECRAFT_DIR);
                        $this->vars["step4_befehl_debian"] = 'cd ' . MCA_PLUGIN_DIR_INCLUDE . '/mca-install/ && bash install-debian.sh > .install_log';
                        $this->vars["step4_befehl_redhat"] = 'cd ' . MCA_PLUGIN_DIR_INCLUDE . '/mca-install/ && bash install-redhat.sh > .install_log';
                        $this->vars["step4_befehl_sonstiges"] = 'cd ' . MCA_PLUGIN_DIR_INCLUDE . '/mca-install/ && bash install.sh > .install_log';
                        $this->vars["step4_intro"] = __("Log in to your server as root and execute the following command:", 'minecraft-admin');
                        $this->vars["step4_hilfe"] = sprintf(__("If you need help visit the authors website %s", 'minecraft-admin'), '<a href="http://profenter.de/projekte/minecraft-admin">"Profenter Systems"</a>');
                    } //!is_file(MCA_MINECRAFT_DIR . '.installed')
                    else {
                        $this->vars["step4_error"] = __("It seems to be that Minecraft Admin was already installed.", 'minecraft-admin');
                    }
                }
            } //is_dir(MCA_MINECRAFT_DIR) && is_writable(MCA_MINECRAFT_DIR)
            else {
                $this->vars["step4_error"] = __("The Minecraft Admin folder isn't writable. Please change the permissions so the Minecraft Admin folder is wirteable.", 'minecraft-admin');
            }
        } //$_GET["mca_install"] == 4
        elseif ($_GET["mca_install"] == 5) {
            if (is_file(MCA_MINECRAFT_DIR . '.installed')) {
                $this->vars["step5_message"] = '<div class="updated">' . __("Yeeeeessssss. You finished the installation. You can now start using this plugin.", 'minecraft-admin') . '</div>';
                $this->vars["step5_finished"] = true;
                MCADB::setOption(array(
                    "type" => "installed",
                    "value" => "ja"
                ));
                if (!MCADB::check("mca_common")) {
                    MCADB::add('mca_common', array(
                        'id' => "int NOT NULL AUTO_INCREMENT ",
                        'type' => "VARCHAR(50) DEFAULT '' NOT NULL",
                        'value' => "TEXT DEFAULT '' NOT NULL"
                    ), 'id');
                    MCADB::set("mca_common");
                    MCADB::setOption(array(
                        "type" => "version",
                        "value" => MCA_VERSION
                    ));
                    MCADB::setOption(array(
                        "type" => "users",
                        "value" => ""
                    ));
                    MCADB::setOption(array(
                        "type" => "installed",
                        "value" => "ja"
                    ));
                    MCADB::setOption(array(
                        "type" => "servers",
                        "value" => serialize(array())
                    ));
                    MCADB::setOption(array(
                        "type" => "style",
                        "value" => "default"
                    ));
                } //!MCADB::check("mca_common")
                $up = ((substr(MCA_MINECRAFT_DIR, -1) == "/") ? substr(MCA_MINECRAFT_DIR, 0, -1) : MCA_MINECRAFT_DIR) . '.update';
                if (is_dir($up)) {
                    if ($dh = opendir($up)) {
                        while (($file = readdir($dh)) !== false) {
                            if (is_dir($up . '/' . $file) && strlen($file) == 1 && is_int((int)$file) && $file != ".") {
                                if ($dh2 = opendir($up . '/' . $file)) {
                                    while (($file2 = readdir($dh2)) !== false) {
                                        if (strlen($file2) == 1 && is_int((int)$file2) && $file2 != ".") {
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
                                                "value" => "vanilla"
                                            ));
                                            MCADB::setOption(array(
                                                "type" => "name",
                                                "value" => "vanilla_server_" . $anzahl
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
                                            MCADB::setOption(array(
                                                "type" => "installed",
                                                "value" => "ja"
                                            ));
                                            MCAF::install_server($anzahl);
                                            MCADB::set('mca_server_' . $blog_id . '_' . $this->serverid);
                                            $cmd = new MCAB("stop");
                                            $cmd->setBlog($blog_id)->setServer($anzahl)->exec();
                                            unlink(MCA_MINECRAFT_DIR . "/servers/" . $blog_id . '_' . $this->serverid . '/server.properties');
                                            unlink($up . "/" . $file . '/' . $file2 . '/server.jar');
                                            unlink($up . "/" . $file . '/' . $file2 . '/initd.sh');
                                            shell_exec('mv ' . $up . "/" . $file . '/' . $file2 . '/* ' . MCA_MINECRAFT_DIR . "/servers/" . $blog_id . '_' . $this->serverid . '/');
                                            $cmd = new MCAB("start");
                                            $cmd->setBlog($blog_id)->setServer($anzahl)->exec();
                                        }

                                    }
                                    closedir($dh2);
                                }
                            }
                        }
                        closedir($dh);
                    }
                    shell_exec('rm -rf ' . $up);
                    MCAF::deleteDirectory($up);
                }
            } //is_file(MCA_MINECRAFT_DIR . '.installed')
            else {
                $this->vars["step5_message"] = '<div class="error">' . __("It seems to be that you don't finished the installation. Go back an follow the guid.", 'minecraft-admin') . '</div>';
                $this->vars["step5_finished"] = false;
            }
        } //$_GET["mca_install"] == 5
    }
}

?>
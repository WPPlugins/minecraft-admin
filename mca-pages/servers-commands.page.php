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
 * @class servers_befehle_page
 * @name adds a control center
 * @version 0.0.3
 */
class servers_commands_page extends pages
{
    function ajax()
    {
        global $blog_id;
        if ($_POST["action"] == "log") {
            MCADB::set('mca_server_' . $blog_id . '_' . $_POST["serverid"]);
            $zahl = (null === MCADB::getOption("value", array("type" => "log"))) ? 100 : MCADB::getOption("value", array("type" => "log"));
            $file = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $_POST["serverid"] . '/logs/' . "latest.log";
            if (!is_file($file)) {
                $file = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $_POST["serverid"] . '/' . "server.log";
            }
            $ex = array_reverse(explode("\n", MCAF::parse_minecraft_output(shell_exec("tail -n " . $zahl . " " . $file))));
            foreach ($ex as $id => $rest) {
                if (isset($rest) && !empty($rest) && strlen($rest) > 3) {
                    echo $rest . "\n";
                } //isset($ex[$i]) && !empty($ex[$i])
            } //$ex as $id => $rest
        } else if ($_POST["action"] == "buttons") {
            MCADB::set('mca_server_' . $blog_id . '_' . $_POST["serverid"]);
            $p = $_POST["button"];
            if ($p == "wl_on") {
                $cmd = new MCAB("wl");
                echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("on")->exec(true);
            } elseif ($p == "wl_off") {
                $cmd = new MCAB("wl");
                echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("off")->exec(true);
            } elseif ($p == "world_change") {
                $cmd = new MCAB("say");
                $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter(__("Stopping the server for maintenance. Try to reconnect in 60sec.", "minecraft-admin"))->exec();
                $cmd = new MCAB("stop");
                $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->exec();

                $cmd = new MCAB("config");
                echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("level-name " . $_POST["name"])->exec(true);
                $cmd = new MCAB("start");
                $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->exec();
            } elseif ($p == "world_on") {
                $cmd = new MCAB("worlds");
                echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("on " . $_POST["name"])->exec(true);
            } elseif ($p == "world_off") {
                $cmd = new MCAB("say");
                $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter(__("Stopping the server for maintenance. Try to reconnect in 60sec.", "minecraft-admin"))->exec();
                $cmd = new MCAB("stop");
                $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->exec();

                $cmd = new MCAB("worlds");
                echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("off " . $_POST["name"])->exec(true);
                $cmd = new MCAB("start");
                $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->exec();
            } elseif ($p == "reload") {
                $cmd = new MCAB("cmd");
                echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("reload")->exec(true);
            } else {
                if (isset($p) && !empty($p)) {
                    $cmd = new MCAB($p);
                    echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->exec(true);
                }
            }
        } else if ($_POST["action"] == "inputs") {
            foreach ($_POST["inputs"] as $input) {
                switch ($input["name"]) {
                    case "time":
                        $cmd = new MCAB("time");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("set " . $input["val"])->exec(true);
                        break;
                    case "gm":
                        foreach ($_POST["inputs"] as $option) {
                            if ($option["name"] == "type") {
                                $type = $option["val"];
                                break;
                            }
                        }
                        $cmd = new MCAB("gm");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter($type . " " . $input["val"])->exec(true);
                        break;
                    case "cmd":
                        $cmd = new MCAB("cmd");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter($input["val"])->exec(true);
                        break;
                    case "say":
                        $cmd = new MCAB("say");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter($input["val"])->exec(true);
                        break;
                    case "give":
                        $cmd = new MCAB("give");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter($input["val"])->exec(true);
                        break;
                    case "op":
                        $cmd = new MCAB("op");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("add " . $input["val"])->exec(true);
                        break;
                    case "wl":
                        $cmd = new MCAB("wl");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter("add " . $input["val"])->exec(true);
                        break;
                    case "bl":
                        foreach ($_POST["inputs"] as $option) {
                            if ($option["name"] == "type") {
                                $type = $option["val"];
                                break;
                            }
                        }
                        $cmd = new MCAB("bl");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter($type . " add " . $input["val"])->exec(true);
                        break;
                    case "kick":
                        $cmd = new MCAB("kick");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter($input["val"])->exec(true);
                        break;
                    case "xp":
                        $cmd = new MCAB("xp");
                        echo $cmd->setBlog($blog_id)->setServer($_POST["serverid"])->setParameter($input["val"])->exec(true);
                        break;
                }
            }
        }
    }

    function setup()
    {
        global $blog_id;
        $output = "";
        $textarea = "";
        define("WORLDSTORAGE", MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $this->serverid . '/worldstorage/');
        MCADB::set('mca_server_' . $blog_id . '_' . $this->serverid);
        if (isset($_GET["p"]) && !empty($_GET["p"])) {
            if ($_GET["p"] == "wl_del") {
                $cmd = new MCAB("wl remove");
                $cmd->setServer($this->serverid)->setParameter($_GET["name"])->exec();
            } elseif ($_GET["p"] == "op_del") {
                $cmd = new MCAB("op remove");
                $cmd->setServer($this->serverid)->setParameter($_GET["name"])->exec();
            } elseif ($_GET["p"] == "reload") {
                $cmd = new MCAB("cmd");
                $cmd->setServer($this->serverid)->setParameter("reload")->exec();
            } elseif ($_GET["p"] == "toggledownfall") {
                $cmd = new MCAB("toggledownfall");
                $cmd->setServer($this->serverid)->exec();
            } elseif ($_GET["p"] == "wl_on") {
                $cmd = new MCAB("wl");
                $cmd->setServer($this->serverid)->setParameter("on")->exec();
            } elseif ($_GET["p"] == "wl_off") {
                $cmd = new MCAB("wl");
                $cmd->setServer($this->serverid)->setParameter("off")->exec();
            } else if ($_GET["p"] == "bl_del") {
                $cmd = new MCAB("bl player remove");
                $cmd->setServer($this->serverid)->setParameter($_GET["name"])->exec();
            } else {
                $cmd = new MCAB($_GET["p"]);
                $cmd->setBlog($blog_id)->setServer($this->serverid)->exec();
            }
        }
        if (isset($_POST["world_dl"])) {
            shell_exec('wget -O ' . WORLDSTORAGE . 'mc_world.' . $_POST["format"] . ' ' . $_POST["world_dl"]);
            switch ($_POST["format"]) {
                case "zip":
                    shell_exec('unzip ' . WORLDSTORAGE . 'mc_world.zip -d ' . MCA_MINECRAFT_DIR);
                    break;
                case "tar":
                    shell_exec('cd ' . WORLDSTORAGE . ' && tar -xf mc_world.tar');
                    break;
                case "rar":
                    shell_exec('cd ' . WORLDSTORAGE . ' && unrar x mc_world.rar');
                    break;
                case "tar.gz":
                    shell_exec('cd ' . WORLDSTORAGE . ' && tar -xzf mc_world.tar.gz');
                    break;
            }
            shell_exec('rm ' . WORLDSTORAGE . 'mc_world.' . $_POST["format"]);
            $files = scandir(WORLDSTORAGE);
            foreach ($files as $file) {
                if ($file != "." && $file != ".." && is_dir(WORLDSTORAGE . $file)) {
                    rename(WORLDSTORAGE . $file, WORLDSTORAGE . MCAF::clean($file));
                }
            }
        }
        if (isset($_POST["old"]) && isset($_POST["rename"])) {
            rename(WORLDSTORAGE . $_POST["old"], WORLDSTORAGE . $_POST["rename"]);
        }

        $cmd = new MCAB("connected");
        $users = MCAF::parse_minecraft_output($cmd->setServer($this->serverid)->exec(true), false);
        $test = str_replace("\n", "", $users);
        $test = str_replace("\r", "", $test);
        $test = str_replace(' ', "", $test);
        if ("Noplayersareconnected." != $test) {
            $online = __("The following users are online:", "minecraft-admin") . $users;
        } //!empty($test)
        else {
            $online = __("It seems to be no user are online :(", "minecraft-admin");
        }
        $status = MCAF::parse_minecraft_output(shell_exec('msm ' . $blog_id . '_' . $this->serverid . ' status'), false);
        $status = str_replace($blog_id . '_' . $this->serverid, MCADB::getOption("value", array("type" => "name")), $status);

        $file = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $this->serverid . '/logs/' . "latest.log";
        if (!is_file($file)) {
            $file = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $this->serverid . '/' . "server.log";
        }
        $ex = array_reverse(explode("\n", MCAF::parse_minecraft_output(shell_exec("tail -n " . MCADB::getOption("value", array("type" => "log")) . " " . $file))));
        foreach ($ex as $id => $rest) {
            if (isset($rest) && !empty($rest) && strlen($rest) > 3) {
                $textarea .= $rest . "\n";
            } //isset($ex[$i]) && !empty($ex[$i])
        } //true

        $wl_list = new MCAB("wl");
        $wl_list = explode("\n", $wl_list->setServer($this->serverid)->setParameter("list")->exec(true));

        $bl_list = array();
        $file = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $this->serverid . '/banned-players.txt';
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (!empty($line)) {
                    $ex = explode("|", $line);
                    if (!empty($ex)) {
                        $bl_list[] = $ex;
                    }
                }

            }
        } else {
            // error opening the file.
        }
        unset($bl_list[1]); // TODO check what this means

        $worlds = new MCAB("worlds");
        $worlds = explode("\n", $worlds->setServer($this->serverid)->setParameter("list")->exec(true));
        $worldslist = "";
        $def_world = new MCAB("config");
        $def_world = str_replace("\n", "", str_replace(" ", "", $def_world->setServer($this->serverid)->setParameter("level-name")->exec(true)));

        foreach ($worlds as $world) {
            $world = str_replace(" ", "", $world);
            if ($world == $def_world) {
                $def_world_text = '<div class="info" id="default_world">' . __("Default World", "minecraft-admin") . '</div>';
            } else {
                $def_world_text = '<div ><a class="button" href="' . MCAF::mc_url(array("p" => "world_change", "name" => $world)) . '">' . __("change to default world", "minecraft-admin") . '</a></div>';
            }
            if (!empty($world)) {
                if (is_dir(WORLDSTORAGE . $world)) {
                    $worldslist .= '<div class="def_world">
                    <form action="" method="POST">
                        <input type="text" value="' . $world . '" name="rename" />
                        <input type="hidden" value="' . $world . '" name="old" />
                        <input class="button-primary" type="submit" value="' . __("rename", "minecraft-admin") . '" />
                    </form>
                    <br />
                    ' . __("Status", "minecraft-admin") . ':' . __("On", "minecraft-admin") . '<br>
                    ' . $def_world_text . '<br>
                    <a class="button"  href="' . MCAF::mc_url(array("p" => "world_off", "name" => $world)) . '">' . __("deactivate", "minecraft-admin") . '</a>
                    </div>';
                } else {
                    $worldslist .= '<div class="def_world">
                    <form action="" method="POST">
                        <input type="text" value="' . $world . '" name="rename" />
                        <input class="button-primary" type="submit" value="' . __("rename", "minecraft-admin") . '" />
                    </form>
                    <br />
                    ' . __("Status", "minecraft-admin") . ':' . __("Off", "minecraft-admin") . '<br>
                    ' . $def_world_text . '<br>
                    <a class="button" href="' . MCAF::mc_url(array("p" => "world_on", "name" => $world)) . '">' . __("activate", "minecraft-admin") . '</a>
                    </div>';
                }
            }
        }

        $op_list = new MCAB("op");
        $op_list = explode("\n", $op_list->setServer($this->serverid)->setParameter("list")->exec(true));


        $vars = array(
            "send_e" => __('send', 'minecraft-admin'),
            "start_e" => __('start', 'minecraft-admin'),
            "stop_e" => __('stop', 'minecraft-admin'),
            "restart_e" => __('restart', 'minecraft-admin'),
            "reload_e" => __('reload', 'minecraft-admin'),
            "kill_e" => __('kill', 'minecraft-admin'),
            "backup_e" => __('backup', 'minecraft-admin'),
            "save_e" => __('Save worlds', 'minecraft-admin'),
            "send_op_e" => __('add op', 'minecraft-admin'),
            "send_wl_e" => __('add to whitelist', 'minecraft-admin'),
            "op_e" => __('Add a user as op', 'minecraft-admin'),
            "wl_e" => __('Add a user to whitelist', 'minecraft-admin'),
            "wl_on_e" => __('on', 'minecraft-admin'),
            "wl_off_e" => __('off', 'minecraft-admin'),
            "is_wl" => __('Should whitelist be on or off?', 'minecraft-admin'),
            "server_have_to_run" => __('the Minecraft Server have to run', 'minecraft-admin'),
            "send_gm_e" => __('change the gamemode', 'minecraft-admin'),
            "creative" => __('creative', 'minecraft-admin'),
            "survival" => __('survival', 'minecraft-admin'),
            "player" => __('player', 'minecraft-admin'),
            "ip" => __('ip', 'minecraft-admin'),
            "toggledownfall_e" => __('toggle downfall', 'minecraft-admin'),
            "send_bl_e" => __('add to blacklist', 'minecraft-admin'),
            "bl_e" => __('Add a user to blacklist', 'minecraft-admin'),
            "bl_on_e" => __('on', 'minecraft-admin'),
            "bl_off_e" => __('off', 'minecraft-admin'),
            "is_bl" => __('Should blacklist be on or off?', 'minecraft-admin'),
            "kick_e" => __('Kick!', 'minecraft-admin'),
            "time_e" => __('set time', 'minecraft-admin'),
            "say_e" => __('say to all', 'minecraft-admin'),
            "command_e" => __('do it', 'minecraft-admin'),
            "xp_e" => __('bring it up', 'minecraft-admin'),
            "command" => __('Command', 'minecraft-admin'),
            "world" => __('Worlds', 'minecraft-admin'),
            "basic" => __('Basic', 'minecraft-admin'),
            "commands" => __('Commands', 'minecraft-admin'),
            "say" => __('Say', 'minecraft-admin'),
            "time" => __('Time', 'minecraft-admin'),
            "op" => __('OP', 'minecraft-admin'),
            "blacklist" => __('Blacklist', 'minecraft-admin'),
            "whitelist" => __('Whitelist', 'minecraft-admin'),
            "gamemode" => __('Gamemode', 'minecraft-admin'),
            "kick" => __('Kick', 'minecraft-admin'),
            "toggledownfall_tab" => __('Toggle downfall', 'minecraft-admin'),
            "give" => __('Give', 'minecraft-admin'),
            "xp" => __('XP', 'minecraft-admin'),
            "world_dl_e" => __('download & install', 'minecraft-admin'),
            "worldsintro" => __('Here you can change the default world Minecraft is loading when start. Please remove all white spaces, special character (such as "(",")","-","_","§","$") from worlds name.', 'minecraft-admin'),
            "worldsintrodownload" => __('Below you can download worlds and install them to your server. Supported formats are: .zip,.tar.gz, .rar', 'minecraft-admin'),
            "status" => $status,
            "send" => "",
            "start" => MCAF::mc_url(array("p" => "start")),
            "stop" => MCAF::mc_url(array("p" => "stop")),
            "kill" => MCAF::mc_url(array("p" => "kill")),
            "restart" => MCAF::mc_url(array("p" => "restart")),
            "reload" => MCAF::mc_url(array("p" => "reload")),
            "backup" => MCAF::mc_url(array("p" => "backup")),
            "save" => MCAF::mc_url(array("p" => "save_all")),
            "wl_off" => MCAF::mc_url(array("p" => "wl_off")),
            "wl_on" => MCAF::mc_url(array("p" => "wl_on")),
            "bl_off" => MCAF::mc_url(array("p" => "bl_off")),
            "bl_on" => MCAF::mc_url(array("p" => "bl_on")),
            "toggledownfall" => MCAF::mc_url(array("p" => "toggledownfall")),
            "oplist" => $op_list,
            "wllist" => $wl_list,
            "bllist" => $bl_list,
            "worldslist" => $worldslist,
            "output" => $output,
            "online" => $online,
            "textarea" => $textarea,
            "is" => (!strpos($status, "stopped")) ? "ja" : "nein",
            "serverid" => $this->serverid
        );
        $this->vars = $vars;
    }
}

?>

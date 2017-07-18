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
 * @class servers_cronjobs_page
 * @name adds cronjob viewer
 * @version 0.0.1
 */
class servers_cronjobs_page extends pages
{
    function setup()
    {
        global $blog_id;
        /*
         *
         * TODO: * User friendly "additional parameters"
         * TODO: * Add intro text
         *
         */
        $action = "";
        $cronjobs = "";
        $prog = MCA_MINECRAFT_DIR . 'servers/' . $blog_id . '_' . $this->serverid . '/' . "aufgaben.crontab";
        if (!is_file($prog)) {
            touch($prog);
        } //!is_file($prog)
        if (isset($_GET["id"])) {
            $properties = file($prog);
            unlink($prog);
            exec("touch " . $prog);
            $dat = fopen($prog, "w");
            unset($properties[$_GET["id"]]);
            $properties = array_values($properties);
            foreach ($properties as $key => $value) {
                if (!empty($value) or $value != " ") {
                    fwrite($dat, $value);
                } //!empty($value) or $value != " "
            } //$properties as $key => $value
            fclose($dat);
            exec("crontab $prog");
            $action = '<meta http-equiv="refresh" content="0; URL=' . MCAF::mc_url(array()) . '"> ';
        } //isset($_GET["id"])
        if (!empty($_POST["befehl"])) {
            if (is_array($_POST["min"])) {
                foreach ($_POST["min"] as $key => $value) {
                    if (!isset($min)) {
                        $min = $value;
                    } //!isset($min)
                    else {
                        $min .= ',' . $value;
                    }
                } //$_POST["min"] as $key => $value
            } //is_array($_POST["min"])
            else {
                $min = $_POST["min"];
            }
            if ($min == "") {
                $min = "*";
            } //$min == ""
            if (is_array($_POST["hour"])) {
                foreach ($_POST["hour"] as $key => $value) {
                    if (!isset($hour)) {
                        $hour = $value;
                    } //!isset($hour)
                    else {
                        $hour .= ',' . $value;
                    }
                } //$_POST["hour"] as $key => $value
            } //is_array($_POST["hour"])
            else {
                $hour = $_POST["hour"];
            }
            if ($hour == "") {
                $hour = "*";
            } //$hour == ""
            if (is_array($_POST["day"])) {
                foreach ($_POST["day"] as $key => $value) {
                    if (!isset($day)) {
                        $day = $value;
                    } //!isset($day)
                    else {
                        $day .= ',' . $value;
                    }
                } //$_POST["day"] as $key => $value
            } //is_array($_POST["day"])
            else {
                $day = $_POST["day"];
            }
            if ($day == "") {
                $day = "*";
            } //$day == ""
            if (is_array($_POST["month"])) {
                foreach ($_POST["month"] as $key => $value) {
                    if (!isset($month)) {
                        $month = $value;
                    } //!isset($month)
                    else {
                        $month .= ',' . $value;
                    }
                } //$_POST["month"] as $key => $value
            } //is_array($_POST["month"])
            else {
                $month = $_POST["month"];
            }
            if ($month == "") {
                $month = "*";
            } //$month == ""
            if (is_array($_POST["week"])) {
                foreach ($_POST["week"] as $value) {
                    if (!isset($week)) {
                        $week = $value;
                    } //!isset($week)
                    else {
                        $week .= ',' . $value;
                    }
                } //$_POST["week"] as $key => $value
            } //is_array($_POST["week"])
            else {
                $week = $_POST["week"];
            }
            if ($week == "") {
                $week = "*";
            } //$week == ""
            $cmd = new MCAB($_POST["befehl"]);
            if ($_POST["befehl"] == "start" or $_POST["befehl"] == "restart" or $_POST["befehl"] == "stop" or $_POST["befehl"] == "backup" or $_POST["befehl"] == "toggledownfall") {
                $befehl = $cmd->setServer($this->serverid)->parse();
            } elseif ($_POST["befehl"] == "world") {
                $cmd = new MCAB("config");
                $befehl = $cmd->setServer($this->serverid)->setParameter("level-name " . $_POST["mcbefehl"])->parse();
            } else {
                $befehl = $cmd->setServer($this->serverid)->setParameter($_POST["mcbefehl"])->parse();
            }
            shell_exec("echo '$min $hour $day $month $week $befehl' >> $prog");
            shell_exec("crontab $prog");
            $action = '<div class="updated">' . __('recurring action was successfully created', 'minecraft-admin') . '</div>';
        } //!empty($_POST["befehl"])
        $properties = file($prog);
        foreach ($properties as $key => $value) {
            $explode = explode("msm", $value);
            $leer = explode(" ", $explode[0]);
            foreach ($leer as $zähler => $zahl) {
                if ($zahl == '*') {
                    if ($zähler == 0) {
                        $leer[$zähler] = __("every minute", "minecraft-admin");
                    } else if ($zähler == 1) {
                        $leer[$zähler] = __("every hour", "minecraft-admin");
                    } else if ($zähler == 2) {
                        $leer[$zähler] = __("every day", "minecraft-admin");
                    } else if ($zähler == 3) {
                        $leer[$zähler] = __("on all weekdays", "minecraft-admin");
                    } else if ($zähler == 4) {
                        $leer[$zähler] = __("every month", "minecraft-admin");
                    }

                } //$zahl == '*'
                else {
                    if ($zähler == 4) {
                        $new = explode(",", $zahl);
                        $text = "";
                        foreach ($new as $value) {
                            switch ($value) {
                                case 1:
                                    $text .= "," . __('January', 'minecraft-admin');
                                    break;
                                case 2:
                                    $text .= "," . __('February', 'minecraft-admin');
                                    break;
                                case 3:
                                    $text .= "," . __('March', 'minecraft-admin');
                                    break;
                                case 4:
                                    $text .= "," . __('April', 'minecraft-admin');
                                    break;
                                case 5:
                                    $text .= "," . __('May', 'minecraft-admin');
                                    break;
                                case 6:
                                    $text .= "," . __('June', 'minecraft-admin');
                                    break;
                                case 7:
                                    $text .= "," . __('July', 'minecraft-admin');
                                    break;
                                case 8:
                                    $text .= "," . __('August', 'minecraft-admin');
                                    break;
                                case 9:
                                    $text .= "," . __('September', 'minecraft-admin');
                                    break;
                                case 10:
                                    $text .= "," . __('October', 'minecraft-admin');
                                    break;
                                case 11:
                                    $text .= "," . __('November', 'minecraft-admin');
                                    break;
                                case 12:
                                    $text .= "," . __('December', 'minecraft-admin');
                                    break;
                            }
                        }
                        $text = substr($text, 1);
                        $leer[$zähler] = $text;
                    } elseif ($zähler == 3) {
                        $new = explode(",", $zahl);
                        $text = "";
                        foreach ($new as $value) {
                            switch ($value) {
                                case 1:
                                    $text .= "," . __('Sundays', 'minecraft-admin');
                                    break;
                                case 2:
                                    $text .= "," . __('Mondays', 'minecraft-admin');
                                    break;
                                case 3:
                                    $text .= "," . __('Tuesdays', 'minecraft-admin');
                                    break;
                                case 4:
                                    $text .= "," . __('Wednesdays', 'minecraft-admin');
                                    break;
                                case 5:
                                    $text .= "," . __('Thursdays', 'minecraft-admin');
                                    break;
                                case 6:
                                    $text .= "," . __('Fridays', 'minecraft-admin');
                                    break;
                                case 7:
                                    $text .= "," . __('Saturdays', 'minecraft-admin');
                                    break;
                            }
                        }
                        $text = substr($text, 1);
                        $leer[$zähler] = $text;
                    }
                }
            } //$leer as $zähler => $zahl
            $bef = explode(" ", $explode[1]);
            $bef[2] = str_replace("\n", "", $bef[2]);

            if ($bef[2] == "command") {
                $befehl = __('minecraft command', 'minecraft-admin') . ': <div style="color:green;">';
                foreach ($bef as $id => $rest) {
                    if ($id != 0 && $id != 1 && $id != 2) {
                        $befehl .= $rest . ' ';
                    } //$id != 0 && $id != 1
                } //$bef as $id => $rest
                $befehl .= '</div>';
            } //$bef[1] == "command"
            elseif ($bef[2] == "start") {
                $befehl = __('start', 'minecraft-admin');
            } //$bef[1] == "start"
            elseif ($bef[2] == "stop") {
                $befehl = __('stop', 'minecraft-admin');
            } //$bef[1] == "stop"
            elseif ($bef[2] == "restart") {
                $befehl = __('restart', 'minecraft-admin');
            } //$bef[1] == "restart"
            elseif ($bef[2] == "backup") {
                $befehl = __('backup', 'minecraft-admin');
            } //$bef[1] == "backup"
            elseif ($bef[2] == "backup") {
                $befehl = __('toggledownfall', 'minecraft-admin');
            } //$bef[1] == "backup"
            else {
                if (count($bef) > 3) {
                    switch ($bef[2]) {
                        case "say":
                            $BEF = __("Say", "minecraft-admin");
                            break;
                        case "time":
                            $BEF = __("set time to", "minecraft-admin");
                            break;
                        case "bl":
                            $BEF = __("blacklist", "minecraft-admin");
                            break;
                        case "wl":
                            $BEF = __("whitelist", "minecraft-admin");
                            break;
                        case "gm":
                            $BEF = __("set gamemode", "minecraft-admin");
                            break;
                        case "give":
                            $BEF = __("Give", "minecraft-admin");
                            break;
                        case "xp":
                            $BEF = __("XP", "minecraft-admin");
                            break;
                        case "kick":
                            $BEF = __("Kick", "minecraft-admin");
                            break;
                        case "config":
                            $BEF = __("set config to", "minecraft-admin");
                            break;
                        default:
                            $BEF = $bef[2];
                            break;
                    }


                    $befehl = $BEF . " <b>";
                    foreach ($bef as $id => $text) {
                        if ($id > 2) {
                            $befehl .= $text . " ";
                        }
                    }
                    $befehl .= "</b>";
                } else {
                    $befehl = $bef[2];
                }

            }
            $cronjobs .= '<tr><td>' . ($key + 1) . '.</td><td>' . $leer[0] . '</td><td>' . $leer[1] . '</td><td>' . $leer[2] . '</td>
						<td>' . $leer[3] . '</td><td>' . $leer[4] . '</td><td>' . $befehl . '</td><td>
						<a class="button" href="' . MCAF::mc_url(array("id" => $key)) . '">' . __("Delete", "minecraft-admin") . '</a></td></tr>';
            unset($befehl);
        } //$properties as $key => $value
        MCADB::set('mca_common');

        $vars = array(

            "id" => __("Id", "minecraft-admin"),
            "minute" => __("minute", 'minecraft-admin'),
            "hour" => __("hour", 'minecraft-admin'),
            "day" => __("day", 'minecraft-admin'),
            "month" => __("month", 'minecraft-admin'),
            "weekdays" => __("weeksday", 'minecraft-admin'),
            "command" => __("command", "minecraft-admin"),
            "delete" => __("delete", "minecraft-admin"),
            "start" => __("start", "minecraft-admin"),
            "stop" => __("stop", "minecraft-admin"),
            "restart" => __("restart", "minecraft-admin"),
            "save" => __("Save changes", 'minecraft-admin'),
            'January' => __('January', 'minecraft-admin'),
            'February' => __('February', 'minecraft-admin'),
            'March' => __('March', 'minecraft-admin'),
            'April' => __('April', 'minecraft-admin'),
            'May' => __('May', 'minecraft-admin'),
            'June' => __('June', 'minecraft-admin'),
            'July' => __('July', 'minecraft-admin'),
            'August' => __('August', 'minecraft-admin'),
            'September' => __('September', 'minecraft-admin'),
            'October' => __('October', 'minecraft-admin'),
            'November' => __('November', 'minecraft-admin'),
            'December' => __('December', 'minecraft-admin'),
            'Sundays' => __('Sundays', 'minecraft-admin'),
            'Mondays' => __('Mondays', 'minecraft-admin'),
            'Tuesdays' => __('Tuesdays', 'minecraft-admin'),
            'Wednesdays' => __('Wednesdays', 'minecraft-admin'),
            'Thursdays' => __('Thursdays', 'minecraft-admin'),
            'Fridays' => __('Fridays', 'minecraft-admin'),
            'Saturdays' => __('Saturdays', 'minecraft-admin'),
            'onallweekdays' => __('on all weekdays', 'minecraft-admin'),
            "everyday" => __('every day', 'minecraft-admin'),
            "everymonth" => __('every month', 'minecraft-admin'),
            "backup" => __('backup', 'minecraft-admin'),
            "Minecraftcommand" => __('additional paramter', 'minecraft-admin'),
            "commandtest" => __('add option', 'minecraft-admin'),
            "everyhour" => __('every hour', 'minecraft-admin'),
            "everyminute" => __('every minute', 'minecraft-admin'),
            "createrecurringaction" => __('create recurring action', 'minecraft-admin'),
            "youcanselcetmultipleselections" => __('you can selcet multiple selections.', 'minecraft-admin'),
            "installed" => MCADB::getOption("value", array("type" => "installed")),
            "action" => $action,
            "cronjobs" => $cronjobs,
            "commands" => __('Commands', 'minecraft-admin'),
            "say" => __('Say', 'minecraft-admin'),
            "time" => __('Time', 'minecraft-admin'),
            "blacklist_add" => __('add to blacklist', 'minecraft-admin'),
            "whitelist_add" => __('add to whitelist', 'minecraft-admin'),
            "blacklist_remove" => __('remove from blacklist', 'minecraft-admin'),
            "whitelist_remove" => __('remove from whitelist', 'minecraft-admin'),
            "gamemode" => __('Gamemode', 'minecraft-admin'),
            "kick" => __('Kick', 'minecraft-admin'),
            "toggledownfall" => __('Toggle downfall', 'minecraft-admin'),
            "give" => __('Give', 'minecraft-admin'),
            "xp" => __('XP', 'minecraft-admin'),
            "world" => __('Worlds', 'minecraft-admin'),

        );
        $this->vars = $vars;
    }
}

?>
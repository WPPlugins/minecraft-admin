<?php

/**
 * Minecraft Admin
 *
 * @category   MCA_Classes
 * @package    MCA Minecraft Admin
 * @name       Minecraft Admin
 * @author     Profenter Systems
 * @copyright  2012-2013 Profenter Systems "service@profenter.de"
 * @version    see http://wordpress.org/plugins/minecraft-admin/changelog/
 * @license    http://profenter.de/profenter/lizenzierung/psl-1-1 PSL (Profenter Systems License) 1.1
 * @link       http://profenter.de/projekte/minecraft-admin
 * @see        http://wordpress.org/plugins/minecraft-admin
 * @since      File available since Release 0.8.1
 * @Todo see @http://profenter.de/projekte/minecraft-admin#todo
 *
 *
 * @class MCAB
 * @name a class for the MC commands
 * @version 0.0.1
 */
class MCAB
{
    protected $parameter;
    protected $command;
    protected $blog;
    protected $server;

    /**
     * @brief performs a command
     * @param string $command Command
     */
    public function __construct($command)
    {
        global $blog_id;
        $this->command = $command;
        $this->blog = $blog_id;
        return $this;
    }

    /**
     * @brief checks if the command is valid
     * @return $this (class)
     */
    public function check()
    {
        $list = array("start", "restart", "stop", "backup", "status", "worlds list", "worlds load", "worlds ram", "worlds todisk",
            "worlds backup", "worlds", "logroll", "toggledownfall", "wl", "wl list", "bl player", "bl list", "bl ip", "op", "op list", "gm",
            "kick", "say", "time set", "give", "xp", "save", "save all", "cmd", "jargroup list", "jargroup create", "jargroup delete",
            "jargroup rename", "jargroup changeurl", "jargroup getlatest", "config");
        $valid = false;
        foreach ($list as $command) {
            if ($command == $this->command) {
                $valid = true;
                break;
            }
        }
        if (!$valid) {
            $this->command = "help";
        }
        return $this;
    }

    /**
     * @brief adds an parameter
     * @param paramter $parameter
     * @return $this (class)
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
        return $this;
    }

    /**
     * @brief sets the blogid
     * @param BlogID $blog_id
     * @return $this (class)
     */
    public function setBlog($blog_id)
    {
        $this->blog = $blog_id;
        return $this;
    }

    /**
     * @brief sets the serverid
     * @param serverID $server
     * @return $this (class)
     */
    public function setServer($server)
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @brief execute a command
     * @param boolean $return return the answer of shell_exec()
     * @return object $this
     */
    public function exec($return = false)
    {
        $file = MCA_MINECRAFT_DIR . '/servers/' . $this->blog . '_' . $this->server . '/mca.log';
        if (empty($this->server)) {
            if (!empty($this->parameter)) {
                if ($return) {
                    return shell_exec("msm " . $this->command . " " . $this->parameter);
                } else {
                    shell_exec("msm  " . $this->command . " " . $this->parameter . " >> $file");
                }
            } else {
                if ($return) {
                    return shell_exec("msm " . $this->command);
                } else {
                    shell_exec("msm " . $this->command . "  >> $file");
                }
            }
        } else {
            if (!empty($this->parameter)) {
                if ($return) {
                    return shell_exec("msm " . $this->blog . '_' . $this->server . " " . $this->command . " " . $this->parameter);
                } else {
                    shell_exec("msm " . $this->blog . '_' . $this->server . " " . $this->command . " " . $this->parameter . " >> $file");
                }
            } else {
                if ($return) {
                    return shell_exec("msm " . $this->blog . '_' . $this->server . " " . $this->command);
                } else {
                    shell_exec("msm " . $this->blog . '_' . $this->server . " " . $this->command . "  >> $file");
                }
            }
        }
        return $this;
    }

    /**
     * @brief returns a parse msm command
     * @return string shell command
     */
    public function parse()
    {
        if (!empty($this->parameter)) {
            return "msm " . $this->blog . '_' . $this->server . " " . $this->command . " " . $this->parameter;
        } else {
            return "msm " . $this->blog . '_' . $this->server . " " . $this->command;
        }
    }

    /**
     * @brief reset the class
     * @return $this (class)
     */
    public function reset()
    {
        $this->parameter = "";
        return $this;
    }
}
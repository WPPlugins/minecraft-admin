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
 * @since      File available since Release 0.7.9
 * @Todo see @http://profenter.de/projekte/minecraft-admin#todo
 *
 *
 * @class MCAF
 * @name often used functions
 * @version 0.0.1
 */
class MCAF
{
    public static function jarlist()
    {

        $msm = new MCAB("jargroup");
        $list = explode("\n", $msm->setParameter("list")->exec(true));
        $new_ar = array();
        foreach ($list as $line) {
            if ($line == ltrim($line) && !empty($line)) {
                $new_ar[] = $line;
            }
        }
        return $new_ar;
    }

    /**
     * parses the log file for human view
     * @param string $parse Text to parse
     * @param boolean $time remove time
     * @return string $parse
     */
    public static function parse_minecraft_output($parse, $time = true)
    {
        if (!$time) {
            $parse = explode('[INFO]', $parse);
            $zahl = count($parse) - 1;
            $parse = str_replace("[0m", "", $parse[$zahl]);
            $parse = explode('[WARNING]', $parse);
            $zahl = count($parse) - 1;
            $parse = str_replace("[m", "", $parse[$zahl]);
        } //!$time
        $parse = str_replace("[m", "", $parse);
        $parse = str_replace("[0m", "", $parse);
        $parse = str_replace("[35m", "", $parse);
        $parse = str_replace("[33;22m", "", $parse);
        $parse = str_replace("[0;35;1m", "", $parse);
        $parse = str_replace("[37;1m", "", $parse);
        $parse = str_replace("[0;31;1m", "", $parse);
        $parse = str_replace("[m", "", $parse);
        $parse = str_replace("<", "[", $parse);
        $parse = str_replace(">", "]", $parse);
        return $parse;
    }

    /**
     * @brief returns a discription to the question
     * @param question $frage
     * @return answer (string)
     */
    public static function mc_core_info($frage)
    {
        switch ($frage) {
            case ("RAM"):
                return __("Here you can set how much RAM (memory) is used by Minecraft. If you do not know how much you have, ask your hoster. If you do not know how much memory is reasonable, leave the default value of &bdquo;1024&rdquo;", 'minecraft-admin');
                break;
            case ("Pfad"):
                return __("You can find the minecraft server under this path.", 'minecraft-admin');
                break;
            case ("jar"):
                return __("Gebe hier den Name der Datei ein, die zum Start des Servers ausgeführt werden soll.", 'minecraft-admin');
                break;
            case ("users"):
                return __("Enter the names of WordPress users (separated by commas) that have access to this interface. Administrators always have access.", 'minecraft-admin');
                break;
            case ("log"):
                return __("Enter the number of logs you want to view.", 'minecraft-admin');
                break;
            case ("backup"):
                return __("You can find the minecraft backup directory under this path", 'minecraft-admin');
                break;
            case ("server"):
                return __("You can choose whether you want an original Minecraft server or a Bukkit server.", 'minecraft-admin');
                break;
            case ("name"):
                return __("Enter here a name for your server. It's only for you.", 'minecraft-admin');
                break;
        } //$frage
    }

    /**
     * @brief creates a file with content
     * @param file $datei
     * @param content $inhalt
     * @param dir $ordner
     * @return message (string)
     */
    public static function create_file($datei, $inhalt, $ordner)
    {
        $fp = @fopen($ordner . $datei, "w+");
        @fwrite($fp, $inhalt);
        @fclose($fp);
        if (file_exists($ordner . $datei)) {
            return '<div class="updated">' . sprintf(__("The file '%s' was succssesfull created.<br>", 'minecraft-admin'), $datei) . '</div>';
        } //file_exists($ordner . $datei)
        else {
            return '<div class="error">' . sprintf(__("The directory isn't writeable. Create the file '%s' into the directory '%s' and copy the following text into this file.<br><textarea>%s</textarea>", 'minecraft-admin'), $datei, $ordner, $inhalt) . '</div>';
        }
    }

    /**
     * @brief removes a dir
     * @param path $path
     * @return message (integer)
     */
    public static function lochen_verzeichnis($path)
    {
        if (!is_dir($path)) {
            if (is_file($path)) {
                unlink($path);
            } //is_file($path)
            else {
                return -1;
            }
        } //!is_dir($path)
        $dir = @opendir($path);
        if (!$dir) {
            return -2;
        } //!$dir
        while (($entry = @readdir($dir)) !== false) {
            if ($entry == '.' || $entry == '..')
                continue;
            if (is_dir($path . '/' . $entry)) {
                $res = self::lochen_verzeichnis($path . '/' . $entry);
                if ($res == -1) {
                    @closedir($dir);
                    return -2;
                } //$res == -1
                else if ($res == -2) {
                    @closedir($dir);
                    return -2;
                } //$res == -2
                else if ($res == -3) {
                    @closedir($dir);
                    return -3;
                } //$res == -3
                else if ($res != 0) {
                    @closedir($dir);
                    return -2;
                } //$res != 0
            } //is_dir($path . '/' . $entry)
            else if (is_file($path . '/' . $entry) || is_link($path . '/' . $entry)) {
                $res = @unlink($path . '/' . $entry);
                if (!$res) {
                    @closedir($dir);
                    return -2;
                } //!$res
            } //is_file($path . '/' . $entry) || is_link($path . '/' . $entry)
            else {
                @closedir($dir);
                return -3;
            }
        } //($entry = @readdir($dir)) !== false
        @closedir($dir);
        $res = @rmdir($path);
        if (!$res) {
            return -2;
        } //!$res
        return 0;
    }

    /**
     * @brief shows a value in correct size
     * @param size $size
     * @param praefix $praefix
     * @param short $short
     * @return true (bool)
     */
    public static function binary_multiples($size, $praefix = true, $short = true)
    {
        if ($praefix === true) {
            if ($short === true) {
                $norm = array(
                    'B',
                    'kB',
                    'MB',
                    'GB',
                    'TB',
                    'PB',
                    'EB',
                    'ZB',
                    'YB'
                );
            } //$short === true
            else {
                $norm = array(
                    'Byte',
                    'Kilobyte',
                    'Megabyte',
                    'Gigabyte',
                    'Terabyte',
                    'Petabyte',
                    'Exabyte',
                    'Zettabyte',
                    'Yottabyte'
                );
            }
            $factor = 1000;
        } //$praefix === true
        else {
            if ($short === true) {
                $norm = array(
                    'B',
                    'KiB',
                    'MiB',
                    'GiB',
                    'TiB',
                    'PiB',
                    'EiB',
                    'ZiB',
                    'YiB'
                );
            } //$short === true
            else {
                $norm = array(
                    'Byte',
                    'Kibibyte',
                    'Mebibyte',
                    'Gibibyte',
                    'Tebibyte',
                    'Pebibyte',
                    'Exbibyte',
                    'Zebibyte',
                    'Yobibyte'
                );
            }
            $factor = 1024;
        }
        $count = count($norm) - 1;
        $x = 0;
        while ($size >= $factor && $x < $count) {
            $size /= $factor;
            $x++;
        } //$size >= $factor && $x < $count
        $size = sprintf("%01.2f", $size) . ' ' . $norm[$x];
        return $size;
    }

    /**
     * @brief own cmp function for files
     * @param a $a
     * @param b $b
     * @return strcmp (array)
     */
    public static function cmp($a, $b)
    {
        return strcmp($a["file"], $b["file"]);
    }

    /**
     * @brief installs a MC server
     * @param serverid $id
     * @param download $down
     */
    public static function install_server($id)
    {
        global $blog_id;
        MCADB::set('mca_server_' . $blog_id . '_' . $id);
        if (null !== MCADB::getOption("value", array("type" => "server")) OR MCADB::getOption("value", array("type" => "server")) == "vanilla") {
            $group = 'minecraft';
        } //$info->server'] == "orginal"
        elseif (MCADB::getOption("value", array("type" => "server")) == "bukkit") {
            $group = 'bukkit';
        } //$info->server'] == "bukkit"
        elseif (MCADB::getOption("value", array("type" => "server")) == "bukkit_dev") {
            $group = 'bukkit_dev';
        } //$info->server'] == "bukkit_dev"
        elseif (MCADB::getOption("value", array("type" => "server")) == "bukkit_beta") {
            $group = 'bukkit_beta';
        } //$info->server'] == "bukkit_beta"
        exec('msm server create ' . $blog_id . '_' . $id);
        $cmd = new MCAB("jar");
        $cmd->setServer($id)->setParameter($group)->exec();
        $cmd = new MCAB("start");
        $cmd->setServer($id)->exec();
        $file = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $id . '/server.properties';
        shell_exec("sleep 20 && echo 'msm-version=minecraft/1.3.0' >> $file");
    }

    /**
     * @brief removes a MC server
     * @param serverid $id
     */
    public static function remove_server($id)
    {
        global $blog_id;
        if (is_dir(MCA_MINECRAFT_DIR . $blog_id . '_' . $id)) {
            rmdir(MCA_MINECRAFT_DIR . $blog_id . '_' . $id);
        } //is_dir(MCA_MINECRAFT_DIR . $blog_id . '/' . $id)
    }

    /**
     * @brief parse a url
     * @param parameters $other_url
     * @return url (string)
     */
    public static function mc_url($other_url = array())
    {
        if (!isset($other_url["nav"])) {
            $other_url["nav"] = $_GET["nav"];
        } //!isset($other_url["nav"])
        if (isset($_GET["subnav"])) {
            if (!isset($other_url["subnav"])) {
                $other_url["subnav"] = $_GET["subnav"];
            } //!isset($other_url["subnav"])
        } //isset($_GET["subnav"])
        if (isset($_GET["serverid"])) {
            if (!isset($other_url["serverid"])) {
                $other_url["serverid"] = $_GET["serverid"];
            } //!isset($other_url["serverid"])
        } //isset($_GET["serverid"])
        $other = "";
        if (!empty($other_url)) {
            $start = true;
            foreach ($other_url as $name => $parameter) {
                if (!$start) {
                    $other .= "&amp;" . $name . "=" . $parameter;
                } //!$start
                else {
                    $start = false;
                    $other = $name . "=" . $parameter;
                }
            } //$other_url as $name => $parameter
        } //!empty($other_url)
        return site_url() . "/wp-admin/admin.php?page=minecraft-admin&amp;" . $other;
    }

    public static function clean($string)
    {
        $string = str_replace(' ', '', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    /**
     * @brief short function for only 1 parameter
     * @param firstparameter $first
     * @param secoundparameter $sec
     * @return url (string)
     */
    public static function mcurl($first, $sec = "")
    {
        if (!empty($first) && !empty($sec)) {
            return MCAF::mc_url(array(
                $first => $sec
            ));
        } //!empty($other_url)
    }

    /**
     * @brief returns all MC servers
     * @return $serverstoreturn (array)
     */
    public static function servers()
    {
        global $blog_id;
        MCADB::set('mca_common');
        $servers = unserialize(MCADB::getOption("value", array(
            "type" => "servers"
        )));
        $serverstoreturn = array();
        foreach ($servers[$blog_id] as $key => $id) {
            MCADB::set('mca_server_' . $blog_id . '_' . $id);
            $serverstoreturn[$id] = MCADB::getOption("value", array(
                "type" => "name"
            ));
        } //$MC["servers"] as $id => $rest
        return $serverstoreturn;
    }

    /**
     * @brief cartesian an array
     * @return $array_cartesian (array)
     * @from http://stackoverflow.com/a/6313346
     */
    public static function cartesian($input)
    {
        $result = array();
        while (list($key, $values) = each($input)) {
            if (empty($values)) {
                continue;
            } //empty($values)
            if (empty($result)) {
                foreach ($values as $value) {
                    $result[] = array(
                        $key => $value
                    );
                } //$values as $value
            } //empty($result)
            else {
                $append = array();
                foreach ($result as &$product) {
                    $product[$key] = array_shift($values);
                    $copy = $product;
                    // Do step 2 above.
                    foreach ($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    } //$values as $item
                    array_unshift($values, $product[$key]);
                } //$result as &$product
                $result = array_merge($result, $append);
            }
        } //list($key, $values) = each($input)
        return $result;
    }

    public static function zip_file($files = array(), $destination = '', $overwrite = false)
    {
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        $valid_files = array();
        if (is_array($files)) {
            foreach ($files as $file) {

                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        if (count($valid_files)) {
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            foreach ($valid_files as $file) {
                $zip->addFile($file, $file);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
            $zip->close();
            return file_exists($destination);
        } else {
            return false;
        }
    }

    public static function zip_dir($rootPath, $destination = '', $overwrite = false)
    {
        if (file_exists($destination) && !$overwrite) {
            return false;
        }
        $zip = new ZipArchive;
        $zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $name => $file) {
            $filePath = $file->getRealPath();
            $zip->addFile($filePath);
        }
        $zip->close();
    }

    public static function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }
}
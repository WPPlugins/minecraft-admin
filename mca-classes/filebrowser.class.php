<?php

/**
 * Created by PhpStorm.
 * User: alex
 * Date: 04.09.14
 * Time: 14:02
 */
class filebrowser
{


    protected $base = "/tmp";
    protected $path = "/";

    public function setBase($base)
    {
        $this->base = $base;
    }

    public function getBase()
    {
        return $this->base;
    }

    public function verzeichnisloeschen($pfad)
    {
        $res = MCAF::lochen_verzeichnis($pfad);
        switch ($res) {
            case 0:
                _e("The directory was deleted successfully.", 'minecraft-admin');
                break;
            case -1:
                _e("Error: This isn't a directory", 'minecraft-admin');
                break;
            case -2:
                _e("Error: The directory wasn't deleted successfully. Reason: unknown", 'minecraft-admin');
                break;
            case -3:
                _e("Error: The directory wasn't deleted successfully. Reason: filetype unknown", 'minecraft-admin');
                break;
            default:
                _e("Error: The directory wasn't deleted successfully. Reason: The delete function seems to be incorrect. Inform the author.", 'minecraft-admin');
                break;
        } //$res
    }

    function fillArrayWithFileNodes(DirectoryIterator $dir)
    {
        $data = array();
        foreach ($dir as $node) {
            if ($node->isDir() && !$node->isDot()) {
                $data[$node->getFilename()] = $this->fillArrayWithFileNodes(new DirectoryIterator($node->getPathname()));
            } else if ($node->isFile()) {
                $data[] = $node->getFilename();
            }
        }
        return $data;
    }

    function arraytolist($array)
    {
        $html = "<ul>";
        foreach ($array as $id => $item) {
            if (is_array($item)) {
                $html .= '<li data-path="' . $id . '">' . $id . ":<br>" . $this->arraytolist($item) . "</li>";
            } else {
                $html .= '<li data-path="' . $item . '">' . $item . "</li>";
            }
        }
        $html .= "</ul>";
        return $html;
    }

    public function typeimage($type)
    {
        return (is_file(MCA_PLUGIN_DIR_INCLUDE . '/mca-styles/default/images/filebrowser/file_extension_' . $type . '.png')) ? $this->getWebFilebrowserImagePath('file_extension_' . $type . '.png') : $this->getWebFilebrowserImagePath('file_extension_unkown.png');
    }

    function filetree($array, $base)
    {
        $html = "<ul>";
        foreach ($array as $id => $item) {
            if (is_array($item)) {
                $html .= '<li data-path="' . $id . '" data-type="directory" class="dir"><img src="' . $this->typeimage('directory') . '" />' . $id . "<br>" . $this->filetree($item, $base . $id . "/") . "</li>";
            } else {
                $html .= '<li data-path="' . $item . '" data-type="' . $this->filetype($base . $item) . '" class="file"><img src="' . $this->typeimage($this->filetype($base . $item)) . '" />' . $item . "</li>";
            }
        }
        $html .= "</ul>";
        return $html;
    }

    public function filetype($filepath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $filetypebyphp = finfo_file($finfo, $filepath);

        if (strpos($file, ".") !== false) {
            $filetypebyown = explode('.', $file);
            $filetypebyown = (isset($filetypebyown[1]) ? $filetypebyown[1] : $filetypebyown[0]);
            if (is_file(MCA_PLUGIN_DIR_INCLUDE . '/mca-styles/default/images/filebrowser/file_extension_' . $filetypebyown . '.png')) {

                if ($filetypebyown != $filetypebyphp) {
                    $filetype = $filetypebyown;
                } //$filetypebyown != $filetypebyphp
                else {
                    $filetype = $filetypebyphp;
                }
            } else {
                $filetype = $filetypebyphp;
            }
        } //strpos($filepath, ".") !== false
        else {
            $filetype = $filetypebyphp;
        }
        finfo_close($finfo);
        $filetype = explode("/", $filetype);
        $filetype = (isset($filetype[1]) ? $filetype[1] : $filetype[0]);
        return $filetype;
    }

    public function getFileInfos($filepath, $type = "size")
    {
        $stat = stat($filepath);
        switch ($type) {
            case "zugriff":
                return date("d-m-Y, H:i:s", $stat[8]);
                break;
            case "anderung":
                return date("d-m-Y, H:i:s", $stat[9]);
                break;
            case "size" :
                return MCAF::binary_multiples($stat[7], true, false);
                break;
        }
    }

    public function parseFileInfos($filepath, $hidden = false)
    {
        $hid = ($hidden) ? "mc_admin_hidden" : "";
        $filename = explode("/", $filepath);
        $name = $filename[count($filename) - 1];
        $filename = str_replace("-", "", str_replace(".", "", $filename[count($filename) - 1]));
        return '<table class="' . $hid . ' ' . $filename . '">
                    <tr>
                        <td>' . __("Name", "minecraft-admin") . ':</td>
                        <td>' . $name . '</td>
                    </tr>
                    <tr>
                        <td>' . __("Last access", "minecraft-admin") . ':</td>
                        <td> ' . $this->getFileInfos($filepath, "zugriff") . '</td>
                    </tr>
                    <tr>
                        <td>' . __("size", "minecraft-admin") . ':</td>
                        <td> ' . $this->getFileInfos($filepath, "size") . '</td>
                    </tr>
                    <tr>
                        <td>' . __("last change", "minecraft-admin") . ':</td>
                        <td> ' . $this->getFileInfos($filepath, "anderung") . '</td>
                    </tr>
               </table>';
    }

    public function parseFileInfosByDir($dir, $hidden = false)
    {
        $files = $this->getVerzeichnisInhalt($dir);
        $html = "";
        foreach ($files as $id => $item) {
            if (is_array($item)) {
                $html .= $this->parseFileInfos($dir . $id, $hidden);
            } else {
                $html .= $this->parseFileInfos($dir . $item, $hidden);
            }
        }
        return $html;
    }

    public function getVerzeichnisInhalt($path)
    {
        $handle = opendir($path);
        $dir = array();
        while ($file = readdir($handle)) {
            if ($file != "." && $file != "..") {
                if (is_dir($path . "/" . $file)) {
                    $dir[$file] = array();
                } else {
                    $dir[] = $file;
                }

            }

        }
        closedir($handle);
        return $dir;
    }

    public function setpath($path)
    {
        $this->path = $path;
    }

    public function breadcrumbs()
    {
        $html = '<a href="#" data-url="/" data-type="nav">Minecraft</a>/';
        if ($this->path != "/") {
            $ex = explode("/", $this->path);
            $url = "/";
            foreach ($ex as $item) {
                if (!empty($item)) {
                    $url .= $item . '/';
                    $html .= '<a href="#" data-type="nav" data-url="' . $url . '">' . $item . '</a>/';
                }
            }
        }
        return $html;
    }

    public function getWebFilebrowserImagePath($img = "")
    {
        return get_bloginfo("wpurl") . '/wp-content/plugins/minecraft-admin/mca-styles/default/images/filebrowser/' . $img;
    }

    public function getContent($file)
    {
        return file_get_contents($this->base . $file);
    }

    public function savefile($inhalt, $file)
    {
        if (isset($inhalt)) {
            $fp = fopen($this->base . $file);
            fwrite($fp, str_replace('\\', "", $inhalt));
            fclose($fp);
            clearstatcache();
        } //isset($_POST["inhalt"])
        return __("Ok, saved.", "minecraft-admin");
    }

    public function deletefile($name)
    {
        unlink($this->base . $name);
        return __("Ok, deleted.", "minecraft-admin");
    }

    public function download($url, $pfad)
    {
        shell_exec("cd " . $this->base . $pfad . " && wget " . str_replace("&", "\\&", $url) . " ");
        return __("Ok, downloaded.", "minecraft-admin");
    }

    public function createdir($name, $pfad)
    {
        shell_exec("mkdir " . $this->base . $pfad . $name);
        return __("Ok, created.", "minecraft-admin");
    }

    public function createfile($name, $pfad)
    {
        shell_exec("touch " . $this->base . $pfad . $name);
        return __("Ok, created.", "minecraft-admin");
    }

    public function filepath($pfad, $server)
    {
        global $blog_id;
        return get_bloginfo("wpurl") . '/wp-content/minecraft.dir/servers/' . $blog_id . '_' . $server . '/' . $pfad;
    }

    public function move($von, $zu, $server)
    {
        shell_exec("mv " . $this->getBase() . $von . " " . $this->getBase() . $zu);
        return __("Ok, renamed.", "minecraft-admin");
    }
}
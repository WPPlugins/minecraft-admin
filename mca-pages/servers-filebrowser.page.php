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
 * @class servers_filebrowser_page
 * @name adds a filebrowser
 * @version 0.0.1
 */
class servers_filebrowser_page extends pages
{
    function setup()
    {
        global $blog_id;
        MCADB::set('mca_common');
        $pfad = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $this->serverid . '/';
        $filebrowser = new filebrowser();
        $filebrowser->setBase($pfad);
        $filebrowser->setpath($_GET["path"]);
        $vars = array(
            "minecraft" => __("Minecraft", "minecraft-admin"),
            "delete" => __("Delete", 'minecraft-admin'),
            "rename" => __("rename", 'minecraft-admin'),
            "back" => __("back", 'minecraft-admin'),
            "save" => __("Save changes", 'minecraft-admin'),
            "deletedir" => __("Delete actually directory.", 'minecraft-admin'),
            "renamedir" => __("rename actually directory.", "minecraft-admin"),
            "uploadfile" => __("Upload file", "minecraft-admin"),
            "choosefile" => __("Choose a file to be uploaded", "minecraft-admin"),
            "create" => __("create", "minecraft-admin"),
            "installed" => MCADB::getOption("value", array(
                    "type" => "installed"
                )),


            "new_dir" => __("create a new dir", "minecraft-admin"),
            "serverid" => $this->serverid,
            "img_folder_new" => $filebrowser->getWebFilebrowserImagePath("folder_add.png"),
            "breadcrumbs" => $filebrowser->breadcrumbs(),
            "vielview" => $filebrowser->filetree($filebrowser->getVerzeichnisInhalt($filebrowser->getBase()), $filebrowser->getBase()),
            "fileinfos" => $filebrowser->parseFileInfosByDir($filebrowser->getBase(), true),
            "filetree" => $filebrowser->filetree($filebrowser->fillArrayWithFileNodes(new DirectoryIterator($filebrowser->getBase())), $filebrowser->getBase())
        );
        $this->vars = $vars;
    }

    function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }

    function ajax()
    {
        global $blog_id;

        $pfad = MCA_MINECRAFT_DIR . '/servers/' . $blog_id . '_' . $_POST["server"] . '/' . $_POST["path"];
        $filebrowser = new filebrowser();
        $filebrowser->setBase($pfad);
        if ($_POST["mca_get"] == "file") {
            echo $filebrowser->getContent($_POST["path"] . $_POST["file"]);
        } else if ($_POST["mca_get"] == "filetree") {
            echo $filebrowser->filetree($filebrowser->fillArrayWithFileNodes(new DirectoryIterator($filebrowser->getBase())), $filebrowser->getBase());
        } else if ($_POST["mca_get"] == "dir") {
            echo $filebrowser->filetree($filebrowser->fillArrayWithFileNodes(new DirectoryIterator($filebrowser->getBase())), $filebrowser->getBase());
        } else if ($_POST["mca_get"] == "deletefile") {
            echo $filebrowser->deletefile($_POST["file"]);
        } else if ($_POST["mca_get"] == "savefile") {
            echo $filebrowser->savefile($_POST["inhalt"], $_POST["file"]);
        } else if ($_POST["mca_get"] == "createdir") {
            echo $filebrowser->createdir($_POST["name"], $_POST["file"]);
        } else if ($_POST["mca_get"] == "createfile") {
            echo $filebrowser->createfile($_POST["name"], $_POST["file"]);
        } else if ($_POST["mca_get"] == "download") {
            echo $filebrowser->download($_POST["url"], $_POST["file"]);
        } else if ($_POST["mca_get"] == "zip") {
            echo $filebrowser->zip($_POST["file"]);
        } else if ($_POST["mca_get"] == "fileinfos") {
            echo $filebrowser->parseFileInfosByDir($filebrowser->getBase(), true);
        } else if ($_POST["mca_get"] == "filepath") {
            echo $filebrowser->filepath($_POST["path"] . $_POST["file"], $_POST["server"]);
        } else if ($_POST["mca_get"] == "breadcrumbs") {
            $filebrowser->setpath($_POST["path"]);
            echo $filebrowser->breadcrumbs();
        } else if ($_POST["mca_get"] == "rename") {
            echo $filebrowser->move($_POST["file"], $_POST["name"], $_POST["server"]);
        }
    }
}

?>
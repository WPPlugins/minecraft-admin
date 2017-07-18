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
 * @class pages
 * @name class which manages the templatesystem
 * @version 0.0.3
 */
class pages
{
    protected $style;
    protected $smarty;
    protected $page;

    /**
     * @brief sets variables
     * @param string $page pageid
     * @param boolean $server_id serverid
     */
    function __construct($page, $server_id = false)
    {
        require_once(MCA_PLUGIN_DIR_INCLUDE . '/mca-classes/smarty/Smarty.class.php');
        $this->style = new styles(MCA_STYLE);
        $this->serverid = (int)$server_id;
        $this->page = $page;
    }

    /**
     * @brief if the content page has no setup function
     */
    function setup()
    {
        $this->template = __("No content", "minecraft-admin");
    }

    /**
     * @brief parses the pages
     * @return string pagecontent
     */
    function parse()
    {
        $this->style->loadConfig();
        $this->style->loadStyles();
        $this->style->loadScripts();
        $this->smarty = new Smarty();
        $this->smarty->setCacheDir(MCA_PLUGIN_DIR_INCLUDE . '/mca-cache/');
        $this->smarty->caching = false;
        if (isset($this->vars)) {
            $vars = $this->vars;
            foreach ($vars as $name => $inhalt) {
                $this->smarty->assign($name, $inhalt);
            }
        } //isset($this->vars)
        return $this->smarty->fetch('string:' . $this->style->getContent($this->page));
    }
}
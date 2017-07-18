<?php

/**
 * Minecraft Admin
 *
 * @category   MCA_Classes
 * @package    MCA Minecraft Admin
 * @name       Minecraft Admin
 * @author     Profenter Systems
 * @copyright  2012-2014 Profenter Systems "service@profenter.de"
 * @version    see http://wordpress.org/plugins/minecraft-admin/changelog/
 * @license    http://profenter.de/profenter/lizenzierung/psl-1-1 PSL (Profenter Systems License) 1.1
 * @link       http://profenter.de/projekte/minecraft-admin
 * @see        http://wordpress.org/plugins/minecraft-admin
 * @since      File available since Release 0.7.9
 * @Todo see @http://profenter.de/projekte/minecraft-admin#todo
 *
 *
 * @class styles
 * @name class which manages the stylesystem
 * @version 0.0.2
 */
class styles
{
    /**
     * @var string the actual style
     */
    protected $name = "classic";
    /**
     * @var object class of the style
     */
    protected $style;

    /**
     * does some jobs before
     *
     * @param string $name the style which should load by this class
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Loads the config file of the style
     *
     * @return bool style is compatible?
     */
    public function loadConfig()
    {
        if (!include_once(MCA_STYLES_DIR . $this->name . "/style.php")) {
            echo(__("ERROR: Could not load style, using default.", "minecraft-admin"));
            if (!include_once(MCA_STYLES_DIR . "classic/style.php")) {
                die(__("ERROR: Could not load default style.", "minecraft-admin"));
            }
            $this->name = "classic";
            MCADB::setOption(array(
                "type" => "style",
                "value" => "classic"
            ));
        }
        $name = $this->name . "Style";
        $this->style = new $name();
        if (version_compare($this->style->getInfo("compatiblefrom"), MCA_VERSION, '<')) {
            return true;
        } else {
            echo(__("ERROR: The style is not compatible to this version of MCA. Setting style to default.", "minecraft-admin"));
            if (!include_once(MCA_STYLES_DIR . "classic/style.php")) {
                die(__("ERROR: Could not load default style.", "minecraft-admin"));
            }
            $this->name = "classic";
            MCADB::setOption(array(
                "type" => "style",
                "value" => "classic"
            ));
            $name = $this->name . "Style";
            $this->style = new $name();
            return false;
        }
    }

    /**
     * registers all CSS files
     */
    public function loadStyles()
    {
        $path = MCA_PLUGIN_DIR_INCLUDE . '/mca-styles/' . $this->name . '/' . $this->style->getCssPath();
        $ite = new RecursiveDirectoryIterator($path);
        foreach ($ite as $file) {
            if (is_file($file)) {
                $ex = explode(".css", str_replace($path, "", $file));
                wp_register_style('mca-styles-' . $this->name . '-' . str_replace(".", "-", $ex[0]), plugins_url(str_replace(WP_CONTENT_DIR . '/plugins/', "", $file)), array(), str_replace(".", "", $this->style->getInfo("version")), 'all');
            }
        }
        $this->style->registerOtherCss();
    }

    /**
     * registers all JS files
     */
    public function loadScripts()
    {
        $path = MCA_PLUGIN_DIR_INCLUDE . '/mca-styles/' . $this->name . '/' . $this->style->getJsPath();
        $ite = new RecursiveDirectoryIterator($path);
        foreach ($ite as $file) {
            if (is_file($file)) {
                $ex = explode(".js", str_replace($path, "", $file));
                wp_register_script('mca-scripts-' . $this->name . '-' . str_replace(".", "-", $ex[0]), plugins_url(str_replace(WP_CONTENT_DIR . '/plugins/', "", $file)));
            }
        }
        $this->style->registerOtherJs();
    }

    /**
     * creates a info box for changing style
     *
     * @return string HTML box a style
     */
    public function createInfo()
    {
        return '<div class="box" data-name="' . $this->style->getInfo("name") . '">
            <h3 class="title">' . $this->style->getInfo("name") . ' <span class="version">' . $this->style->getInfo("version") . '</span></h3>
             ' . __("by", "minecraft-admin") . '
             <span class="author">
                <a href="' . $this->style->getInfo("authorurl") . '">' . $this->style->getInfo("author") . '</a>
             </span>
            <div class="screenshot">
                <img src="' . plugins_url(MCA_PLUGIN_DIR . '/mca-styles/' . $this->name . '/' . $this->style->getInfo("screenshot")) . '" />
            </div>


        </div>';
    }

    /**
     * creates the whole template of one page
     *
     * @param string $page template page name
     * @return string HTML
     */
    public function getContent($page)
    {
        return $this->style->getHeader() . $this->style->getTemplate($page) . $this->style->getFooter();
    }
}

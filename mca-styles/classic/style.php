<?php

/**
 * Class classicStyle
 */
class classicStyle
{
    /**
     * @var string Name of Theme
     */
    protected $name = "classic";
    /**
     * @var string Version
     */
    protected $version = "0.0.6";
    /**
     * @var string the author of the style
     */
    protected $author = "Profenter Systems";
    /**
     * @var string a url to authors website
     */
    protected $authorurl = "http://profenter.de/projekte/minecraft-admin";
    /**
     * @var string minimal version of MCA
     */
    protected $compatiblefrom = "0.8.2.5";
    /**
     * @var string name of a screenshot for the preview
     */
    protected $screenshot = "screenshot.png";

    /**
     * pre work by the style
     */
    public function __construct()
    {
    }

    /**
     * MCA uses this function to get the CSS path
     *
     * @return string CSS dir
     */
    public function getCssPath()
    {
        return "css/";
    }

    /**
     * register additional CSS
     */
    public function registerOtherCss()
    {
        wp_enqueue_style('plugin_name-admin-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css', false, '20140928', false);
    }

    /**
     * MCA uses this function to get the JS path
     *
     * @return string
     */
    public function getJsPath()
    {
        return "js/";
    }

    /**
     * register additional JS
     */
    public function registerOtherJs()
    {
        wp_enqueue_script('mca-scripts-classic-base64', plugins_url(MCA_PLUGIN_DIR . '/mca-styles/classic/js/base64.js'));
        wp_enqueue_script('mca-scripts-classic-jquery-contextMenu', plugins_url(MCA_PLUGIN_DIR . '/mca-styles/classic/js/jquery.contextMenu.js'));
        wp_enqueue_script('mca-scripts-classic-jquery-core', plugins_url(MCA_PLUGIN_DIR . '/mca-styles/classic/js/jquery.mca.js'));
        wp_enqueue_script('mca-scripts-classic-jquery-ui-position', plugins_url(MCA_PLUGIN_DIR . '/mca-styles/classic/js/jquery.ui.position.js'));
        wp_enqueue_script('mca-scripts-classic-notify', plugins_url(MCA_PLUGIN_DIR . '/mca-styles/classic/js/notify.min.js'));
        wp_enqueue_script('colorbox', plugins_url(MCA_PLUGIN_DIR . '/mca-styles/classic/js/jquery.colorbox-min.js'));
        wp_register_script('mca-styles-classic-autosize', plugins_url(MCA_PLUGIN_DIR . '/mca-styles/classic/js/jquery.autosize.min.js'));
    }

    /**
     * MCA calls this function to get infos about this style
     *
     * @param string $name Name of the info
     * @return string|int the info
     */
    public function getInfo($name)
    {
        return $this->$name;
    }

    /**
     * MCA calls this function to get the HTML header
     *
     * @return string HEADER.html
     */
    public function getHeader()
    {
        return file_get_contents(MCA_STYLES_DIR . $this->name . "/templates/header.template.html");
    }

    /**
     * MCA calls this function to get the HTML footer
     *
     * @return string FOOTER.HTML
     */
    public function getFooter()
    {
        return file_get_contents(MCA_STYLES_DIR . $this->name . "/templates/footer.template.html");
    }

    /**
     * MCA calls this function to get the HTML template
     *
     * @param string $name Name of the template
     * @return string HTML template
     */
    public function getTemplate($name)
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-tooltip');
        wp_enqueue_style('mca-styles-classic-jquery-contextMenu');
        wp_enqueue_style('mca-styles-classic-classic');

        wp_enqueue_script('mca-styles-classic-autosize');
        wp_enqueue_script('colorbox');
        wp_enqueue_style('mca-styles-classic-' . $name . "-mca");
        wp_enqueue_script('mca-scripts-classic-servers-' . $name . '-mca');
        wp_enqueue_script('mca-scripts-classic-' . $name . '-mca');
        return file_get_contents(MCA_STYLES_DIR . $this->name . "/templates/" . $name . ".template.html");
    }
} 
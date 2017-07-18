<?php
/*
Plugin Name: Minecraft Admin
Plugin URI: http://profenter.de/en/projekte/minecraft-admin
Description:"Minecraft Admin for Wordpress" is a plugin for Wordpress, you can create, configure and manage your Minecraft/Bukkit Server with your wordpress blog.
Version: 0.8.4.2.3
Author: Profenter Systems
Author URI: http://profenter.de
*/
/**
 * Minecraft Admin
 *
 * @category   MCA_loader
 * @package    MCA Minecraft Admin
 * @name       Minecraft Admin
 * @author     Profenter Systems
 * @copyright  2012-2014 Profenter Systems "service@profenter.de"
 * @version    see http://wordpress.org/plugins/minecraft-admin/changelog/
 * @license    http://profenter.de/profenter/lizenzierung/psl-1-1 PSL (Profenter Systems License) 1.1
 * @link       http://profenter.de/en/projekte/minecraft-admin
 * @see        http://wordpress.org/plugins/minecraft-admin
 * @since      File available since Release 0.7.9
 * @Todo see @http://profenter.de/projekte/minecraft-admin#todo
 *
 *
 * @class MCA
 * @name Minecraft Admin load class
 * @version 0.2.4
 */
if (!defined('ABSPATH'))
    exit;
/**
 * the MCA version
 */
define('MCA_VERSION', '0.8.4.2');
/**
 * base MCA plugin path
 */
define('MCA_PLUGIN_DIR', dirname(plugin_basename(__FILE__)));
/**
 * base MCA plugin path for including
 */
define('MCA_PLUGIN_DIR_INCLUDE', WP_CONTENT_DIR . '/plugins/' . dirname(plugin_basename(__FILE__)));
/**
 * the styles path
 */
define('MCA_STYLES_DIR', MCA_PLUGIN_DIR_INCLUDE . '/mca-styles/');
/**
 * the data path
 */
define('MCA_MINECRAFT_DIR', WP_CONTENT_DIR . '/minecraft.dir/');


/**
 * Class MCA
 */
class MCA
{
    /**
     * @var string Version von MCA in der DB
     */
    protected $version = "0.0.0.0";
    /**
     * @var array All loaded classes
     */
    protected $classes = array();
    /**
     * @var array All pages
     */
    protected $pages = array();

    /**
     * @brief loads the core functions of this class
     */
    public function __construct()
    {
        $_GET["nav"] = (isset($_GET["nav"]) && !empty($_GET["nav"])) ? $_GET["nav"] : "home";
        $this->setLang();
        $this->setProfenter();
        $this->loadClasses();
        $this->update();
        $this->setStyle();
        $this->scanPages();
        $this->genContent();
        $this->setup();
    }

    /**
     * @brief set up the language
     */
    public function setLang()
    {
        load_plugin_textdomain('minecraft-admin', false, MCA_PLUGIN_DIR . '/mca-languages/');
    }

    /**
     * @brief loads all needed classes
     */
    function loadClasses()
    {
        $pfad = MCA_PLUGIN_DIR_INCLUDE . '/mca-classes/';
        if ($handle = opendir($pfad)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..")
                    if (is_file($pfad . $file)) {
                        if (!include_once($pfad . $file)) {
                            die(__('MC-ERROR: The MCA class file ' . $pfad . $file . ' couldn\'t be load. Please check your path settings.', 'minecraft-admin'));
                        }
                        $this->classes[] = $file;
                    }
            }
            closedir($handle);
        }
    }

    /**
     * @brief updates the content of the plugin
     */
    protected function update()
    {
        global $blog_id;
        MCADB::set('mca_common');
        $this->version = MCADB::getOption("value", array(
            "type" => "version"
        ));
        if (version_compare($this->version, "0.8.3.7", '<')) {
            $cmd = new MCAB("jargroup");
            $cmd->setParameter("delete bukkit")->exec(false)->reset()->setParameter("delete bukkit_dev")->exec()->reset()->setParameter("delete bukkit_beta")->exec()->reset()->setParameter("create glowestone_last http://ci.chrisgward.com/job/Glowstone/98/artifact/build/libs/glowstone.jar")->exec()->reset();
        }
        if (version_compare($this->version, "0.8.3.3", '<')) {
            echo '<div class="info">' . __("Please verfiy that the following programms are installed: rar, unrar, tar", "minecraft-admin") . '</div>';
        }
        if (version_compare($this->version, "0.8.2.7", '<')) {
            MCADB::setOption(array(
                "type" => "style",
                "value" => "classic"
            ));
        }
        if (version_compare($this->version, "0.8.2.5", '<')) {
            MCADB::setOption(array(
                "type" => "users",
                "value" => ""
            ));
        }
        MCADB::updateOption("value", MCA_VERSION, array("type" => "version"));
    }

    /**
     * @brief loads the core functions of this class
     */
    public function setProfenter()
    {
        $lang = explode("-", get_bloginfo("language"));
        if ($lang[0] != "de") {
            define("PROFENTERDE", "http://profenter.de/en/");
        } else {
            define("PROFENTERDE", "http://profenter.de/");
        }
    }

    /**
     * @brief sets up the style
     */
    public function setStyle()
    {
        MCADB::set('mca_common');
        $this->style = MCADB::getOption("value", array(
            "type" => "style"
        ));
        define('MCA_STYLE', $this->style);
        define('MC_WEB_INCLUDE', plugins_url('/mca-styles/' . $this->style . '/', __FILE__));
    }

    /**
     * @brief scan all pages in a variable
     */
    public function scanPages()
    {
        $pfad = MCA_PLUGIN_DIR_INCLUDE . '/mca-pages/';
        if ($handle = opendir($pfad)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..")
                    if (is_file($pfad . $file) && strpos($file, '.page.php') !== false) {
                        if (!include_once($pfad . $file)) {
                            die(__('MC-ERROR: The MCA page file ' . $pfad . $file . ' couldn\'t be load. Please check your path settings.', 'minecraft-admin'));
                        }
                        $name = str_replace(".page.php", "", $file);
                        if (strpos($name, '-') !== false) {
                            $ex = explode("-", $name);
                            if (!is_array($this->pages[$ex[0]])) {
                                $this->pages[$ex[0]] = array();
                            }
                            $this->pages[$ex[0]][$ex[1]] = true;
                        } else {
                            if (!isset($this->pages[$name])) {
                                $this->pages[$name] = true;
                            }
                        }
                        include_once($pfad . $file);
                        $this->classes[] = $file;
                    }
            }
            closedir($handle);
        }
    }

    /**
     * @brief check ifs a ajax request or not
     */
    public function genContent()
    {
        if (strpos($_SERVER["SCRIPT_FILENAME"], "admin-ajax.php")) {
            $this->ajax();
        } else {
            $this->loadPage();
        }
    }

    /**
     * @brief loads the content
     */
    public function loadPage()
    {
        MCADB::set('mca_common');
        if (MCADB::getOption("value", array("type" => "installed")) == "ja") {
            if ($this->pages[$_GET["nav"]] || is_array($this->pages[$_GET["nav"]])) {
                $LOAD = $_GET["nav"];
                $CLASS = $LOAD . "_page";
                if (is_array($this->pages[$_GET["nav"]]) && isset($_GET["subnav"])) {
                    $LOAD .= "-" . str_replace($LOAD . "_", "", $_GET["subnav"]);
                    $CLASS = $_GET["nav"] . "_" . $_GET["subnav"] . "_page";
                }
            }
        } //MCADB::getOption("value", array( "type" => "installed" )) == "ja"
        else {
            $LOAD = "install";
            $CLASS = $LOAD . "_page";
        }
        $pfad = MCA_PLUGIN_DIR_INCLUDE . '/mca-pages/' . $LOAD . '.page.php';
        if (is_file($pfad)) {
            include_once($pfad);
        } //is_file($pfad)
        else {
            die(__('MC-ERROR: The MCA page file ' . $pfad . ' couldn\'t be load. Please check your path settings.', 'minecraft-admin'));
        }
        $this->pageclass = new $CLASS($LOAD, $_GET["serverid"]);
    }

    /**
     * @brief generates the bottom html
     */
    protected function genBottom()
    {
        echo '</div>
                    <!-- Piwik -->
                            <script type="text/javascript">
                            var _paq = _paq || [];
                                _paq.push(["setCookieDomain", "*"]);
                                _paq.push(["trackPageView"]);
                                _paq.push(["enableLinkTracking"]);
                            (function() {
                                var u=(("https:" == document.location.protocol) ? "https" : "http") + "://stats.profenter.de/";
                                _paq.push(["setTrackerUrl", u+"piwik.php"]);
                                _paq.push(["setSiteId", "4"]);
                                var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
                                g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
                            })();
                        </script>
					<!-- End Piwik Code -->';
    }

    /**
     * @brief generates the nav
     */
    protected function genNav()
    {
        $subnav = '<h2 class="nav-tab-wrapper mca_subnav">';
        foreach ($this->pages as $page => $content) {
            if (is_array($content) && $page == $_GET["nav"]) {
                foreach ($content as $subpage => $tmp) {
                    if ($subpage == ((isset($_GET["subnav"])) ? $_GET["subnav"] : "")) {
                        $subnav .= '<a class="nav-tab mca_sub_nav nav-tab-active" href="admin.php?page=minecraft-admin&nav=' . $page . '&subnav=' . $subpage . '&serverid=ID">
                                            ' . __($subpage, "minecraft-admin") . '

                                      </a>';
                    } //$page == $_GET["subnav"]
                    else {
                        $subnav .= '<a class="nav-tab mca_sub_nav" href="admin.php?page=minecraft-admin&nav=' . $page . '&subnav=' . $subpage . '&serverid=ID">
                                            ' . __($subpage, "minecraft-admin") . '

                                     </a>';
                    }
                }
            }
        }
        $subnav .= '</h2><div class="clear"></div>';
        $this->subnav = $subnav;
    }

    /**
     * @brief generates nav with the servers inside
     */
    protected function genServerNav()
    {
        if ($_GET["nav"] == "servers") {
            echo '<div id="mca_servers"><ul class="mca_servers_list">';
            $servers = MCAF::servers();
            foreach ($servers as $id => $name) {
                if ($id == $_GET["serverid"]) {
                    echo '<li class="current"><a href="admin.php?page=minecraft-admin&nav=' . $_GET["nav"] . '&serverid=' . $id . '">' . $name . '</a></li>';
                } //$id == $_GET["serverid"]
                else {
                    echo '<li><a href="admin.php?page=minecraft-admin&nav=' . $_GET["nav"] . '&serverid=' . $id . '">' . $name . '</a></li>';
                }
            } //$servers as $id => $name
            echo '</ul></div><div class="clear"></div>';
        } //$page == "servers
    }

    /**
     * @brief parses the full generated content
     */
    public function parse()
    {
        $this->genNav();
        $this->pageclass->setup();
        echo '<div id="mca"><div id="mca_infos"></div>';
        $this->genServerNav();
        if (isset($_GET["serverid"])) {
            echo str_replace("serverid=ID", "serverid=" . $_GET["serverid"], $this->subnav);
        } //isset($_GET["serverid"])
        echo $this->pageclass->parse();
        if (isset($_GET["subnav"])) {
            $classname = $_GET["nav"] . "_" . $_GET["subnav"] . "_page";
            $ex = explode("_", $_GET["subnav"]);
            include_once(MCA_PLUGIN_DIR_INCLUDE . '/mca-pages/' . $ex[0] . '-' . $ex[1] . '.page.php');
            $class = new $classname($_GET["subnav"], $_GET["serverid"]);
            $class->setup();
            echo $class->parse();
        } //isset($_GET["subnav"])
        $this->genBottom();
    }

    /**
     * @brief adds a WP admin page
     */
    function setup()
    {
        add_action('admin_menu', array(
            &$this,
            'admin_menu_page'
        ));
        if (is_multisite()) {
            add_action('network_admin_menu', array(
                &$this,
                'network_menu'
            ));
        } //is_multisite()
        if (!is_dir(MCA_MINECRAFT_DIR)) {
            mkdir(MCA_MINECRAFT_DIR);
        } //!is_dir(MCA_MINECRAFT_DIR)
    }

    /**
     * @brief adds a menu page to wordpress
     */
    function admin_menu_page()
    {
        $usercanaccess = false;
        $current_user = wp_get_current_user();
        $users = explode(",", MCADB::getOption("value", array("type" => "users")));
        foreach ($users as $user) {
            if ($user == $current_user->user_login) {
                $usercanaccess = true;
            }
        }
        if (current_user_can('manage_options') xor $usercanaccess) {
            add_menu_page(__('Minecraft Admin', 'minecraft-admin'), __('Minecraft', 'minecraft-admin'), 'read', "minecraft-admin", array(
                &$this,
                'parse'
            ), plugins_url(MCA_PLUGIN_DIR . '/mca-styles/default/images/icon.png'));
            MCADB::set('mca_common');
            if (MCADB::getOption("value", array("type" => "installed")) == "ja") {
                add_submenu_page('minecraft-admin', __('Home', 'minecraft-admin'), __('Home', 'minecraft-admin'), "read", "minecraft-admin", array(
                    &$this,
                    'parse'
                ));
                add_submenu_page('minecraft-admin', __('Servers', 'minecraft-admin'), __('Servers', 'minecraft-admin'), "read", "minecraft-admin&nav=servers", array(
                    &$this,
                    'parse'
                ));
                add_submenu_page('minecraft-admin', __('About', 'minecraft-admin'), __('About', 'minecraft-admin'), "read", "minecraft-admin&nav=ueber", array(
                    &$this,
                    'parse'
                ));
                if (!is_multisite()) {
                    add_submenu_page('minecraft-admin', __('Settings', 'minecraft-admin'), __('Settings', 'minecraft-admin'), "read", "minecraft-admin&nav=settings", array(
                        &$this,
                        'parse'
                    ));
                }
            } //MCADB::getOption("value", array( "type" => "installed" )) == "ja"
        }
    }

    /**
     * @brief adds a menu to network pages
     */
    public function network_menu()
    {
        add_menu_page(__('Minecraft Admin', 'minecraft-admin'), __('Minecraft', 'minecraft-admin'), 'read', "minecraft-admin", array(
            &$this,
            'parse'
        ), plugins_url(MCA_PLUGIN_DIR . '/mca-styles/default/images/icon.png'));
        add_submenu_page('minecraft-admin', __('Settings', 'minecraft-admin'), __('Settings', 'minecraft-admin'), "read", "minecraft-admin&nav=settings", array(
            &$this,
            'parse'
        ));
    }

    /**
     * @brief renders the pages and naviagtion
     */
    public function ajax()
    {
        if (isset($_POST["subnav"])) {
            $cl = $_POST["nav"] . "_" . $_POST["subnav"] . "_page";
        } else {
            $cl = $_POST["nav"] . "_page";
        }
        $tmp = new $cl($_POST["nav"]);
        if (method_exists($tmp, "ajax")) {
            add_action('wp_ajax_' . $_POST["action"], array(
                $cl,
                "ajax"
            ));
        }
    }
}

$MCA = new MCA();

/**
 *
 *
 * @TODO this is a bug because if you put this line in the correct position it doesn't work. We will try to fix it soon
 *
 *
 */
function mca_register_widgets()
{
    register_widget('MCA_CORE_WIDGET');
}

include_once(MCA_PLUGIN_DIR_INCLUDE . '/mca-classes/widget.class.php');
add_action('widgets_init', 'mca_register_widgets');

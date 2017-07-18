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
 * @deprecated File not deprecated
 * @Todo see @http://profenter.de/projekte/minecraft-admin#todo
 *
 *
 * @class MCA_CORE_WIDGET
 * @name widget for MCA
 * @version 0.0.1
 */
class MCA_CORE_WIDGET extends WP_Widget
{
    /**
     * @brief set the title of the widget
     */
    function MCA_CORE_WIDGET()
    {
        parent::__construct(false, __('Minecraft Server Status', 'minecraft-admin'));
    }

    /**
     * @brief is shown on frontend
     * @param instance $instance
     * @param agruments $args
     * @return true (bool)
     */
    function widget($args, $instance)
    {
        global $blog_id;
        $args = (object)$args;
        echo $args->before_widget;
        echo $args->before_title;
        echo $args->widget_name;
        echo $args->after_title;
        $users = MCAF::parse_minecraft_output(shell_exec('sh /etc/init.d/minecraft ' . $blog_id . ' ' . get_option("mca_core_widget") . ' command "list"'), false);
        $test = str_replace("\n", "", $users);
        $test = str_replace("\r", "", $test);
        $test = str_replace(' ', "", $test);
        if (!empty($test)) {
            _e("The following users are online:", "minecraft-admin");
            $us = explode(" ", $users);
            foreach ($us as $user) {
                if (!empty($user)) {
                    echo '<div>
										<img src="http://minecraft.aggenkeech.com/face.php?u=' . $user . '&s=25" /> ' . $user . '
											<div class="hover">
												<span class="name">' . $user . '</span>
												<img src="http://minecraft.aggenkeech.com/body.php?u=' . $user . '&s=50&r=255&g=250&b=238" />
											</div>
										</div><br />';
                }
            }
        } //!empty($test)
        else {
            _e("It seems to be no user are online :(", "minecraft-admin");
        }
        $outputs = shell_exec('sh /etc/init.d/minecraft ' . $blog_id . ' ' . get_option("mca_core_widget") . ' status');
        $outputs = explode(" ", $outputs);
        $outputs = str_replace(".", "", $outputs[1]);
        $outputs = str_replace("\n", "", $outputs);
        $outputs = str_replace("\r", "", $outputs);
        $outputs = str_replace(' ', "", $outputs);
        echo '<br>';
        if ($outputs == __("runs", "minecraft-admin")) {
            echo '<a class="button" style="color:green;position: absolute;">' . $outputs . "</a>";
        } //$outputs == __("runs", "minecraft-admin")
        else {
            echo '<a class="button" style="color:red;position: absolute;">' . $outputs . "</a>";
        }
        echo '<br>' . __("Join it now on", 'minecraft-admin') . " ";
        $ex = explode("\n", shell_exec("tail -n 100 " . MCA_MINECRAFT_DIR . $blog_id . '/' . get_option("mca_core_widget") . '/' . "server.properties"));
        foreach ($ex as $s) {
            if (strpos($s, "server-ip") !== false) {
                $s = explode("=", $s);
                $server = $s[1];
                break;
            } //strpos($s, "server-ip") !== false
        } //$ex as $s
        echo (!empty($server)) ? $server : $_SERVER["SERVER_ADDR"];
        foreach ($ex as $s) {
            if (strpos($s, "server-port") !== false) {
                $s = explode("=", $s);
                echo ':' . $s[1];
                break;
            } //strpos($s, "server-port") !== false
        } //$ex as $s
        echo $args->after_widget;
    }

    /**
     * @brief update function when the user changes parameters
     * @param new_instance $new_instance
     * @param old_instance $old_instance
     * @return $instance (array)
     */
    function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /**
     * @brief form for the admin interface
     * @param instance $instance
     * @return true (bool)
     */
    function form($instance)
    {
        $this->servers = MCAF::servers();
        if (!get_option("mca_core_widget")) {
            add_option("mca_core_widget");
        }
        if (isset($_POST["mc_core_server"])) {
            update_option("mca_core_widget", (int)strip_tags(stripslashes($_POST["mc_core_server"])));
        } //isset($_POST["mc_core_server"])
        ?>
        <p>
            <label for="mc_core_server">
                <?php
                _e('Server:', 'minecraft-admin');
                ?>
            </label>
            <select id="mc_core_server" name="mc_core_server">
                <?php

                if (!empty($this->servers)) {
                    foreach ($this->servers as $id => $server) {
                        ?>
                        <option value="<?php echo $id; ?>" <?php if (get_option("mca_core_widget") == $id) {
                            echo "selected";
                        } ?>>
                            <?php
                            echo $server;
                            ?>
                        </option>
                    <?php
                    } //$this->MC->servers as $id => $server
                } //!empty($this->MC->servers)
                ?>
            </select>
        </p>
    <?php
    }
}
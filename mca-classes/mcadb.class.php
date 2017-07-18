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
 * @class MCADB
 * @name Helpclass to get better access to MySQL
 * @version 0.0.3
 */
class MCADB
{
    protected static $name = '';

    /**
     * set down the table which is used
     * @param string $TABLEname table
     * @return boolean
     */
    public static function set($TABLEname)
    {
        self::$name = $TABLEname;
        if (MCADB::check($TABLEname)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * saves an option
     * @param array $where_array restriction
     * @return boolean
     */
    public static function setOption($where_array = array())
    {
        global $wpdb;
        $TABLEname = self::$name;
        if (MCADB::check($TABLEname)) {
            $table_name = $wpdb->prefix . $TABLEname;
            $insert[0] = '';
            $insert[1] = '';
            foreach ($where_array as $key => $value) {
                $insert[0] .= "`" . $key . "`,";
                $insert[1] .= "'" . $value . "',";
            } //$where_array as $key => $value
            if (substr($insert[1], -1) == ',') {
                $insert[1] = substr($insert[1], 0, -1);
            } //substr($insert[1], -1) == ','
            if (substr($insert[0], -1) == ',') {
                $insert[0] = substr($insert[0], 0, -1);
            } //substr($insert[0], -1) == ','
            $query = "INSERT INTO " . $table_name . " (" . $insert[0] . ") VALUES (" . $insert[1] . ")";
            $wpdb->query($query);
            if ($wpdb->last_error) {
                die($wpdb->last_error);
            } //$wpdb->last_error
            return true;
        } else {
            return false;
        }
    }

    /**
     * updates an option
     * @param array $where_array restriction
     * @param string $WERTname key
     * @param string $wert value
     * @return boolean
     */
    public static function updateOption($WERTname, $wert, $where_array = array())
    {
        global $wpdb;
        $TABLEname = self::$name;
        if (MCADB::check($TABLEname)) {
            $table_name = $wpdb->prefix . $TABLEname;
            $update = $WERTname . ' = ' . "'$wert'";
            $where = '';
            foreach ($where_array as $key => $value) {
                $where .= ' ' . $key . " like '" . $value . "' " . $or;
            } //$where_array as $key => $value
            if (substr($where, -2) == $or) {
                $where = substr($where, 0, -2);
            } //substr($where, -2) == $or
            if ($_GET["er"]) {
            } //$_GET["er"]
            if (!empty($where)) {
                $query = "UPDATE " . $table_name . " SET " . $update . " WHERE " . $where;
            } //!empty($where)
            else {
                $query = "UPDATE " . $table_name . " SET " . $update;
            }
            $wpdb->query($query);
            if ($wpdb->last_error) {
                die($wpdb->last_error);
            } //$wpdb->last_error
            return true;
        } else {
            return false;
        }
    }

    /**
     * returns an option
     * @param array $where_array restriction
     * @param string $wert value
     * @return string option
     */
    public static function getOption($wert, $where_array = array())
    {
        global $wpdb;
        $TABLEname = self::$name;
        if (MCADB::check($TABLEname)) {
            $table_name = $wpdb->prefix . $TABLEname;
            $where = '';
            if (!empty($where_array)) {
                foreach ($where_array as $key => $value) {
                    $where .= " " . $key . " like '" . $value . "' &&";
                } //$where_array as $key => $value
                if (substr($where, -2) == "&&") {
                    $where = substr($where, 0, -2);
                } //substr($where, -2) == "&&"
                $query = "SELECT " . $wert . " FROM " . $table_name . " WHERE " . $where . " ORDER BY " . $wert . " LIMIT 0,1";
            } //!empty($where_array)
            else {
                $query = "SELECT " . $wert . " FROM " . $table_name . " ORDER BY " . $wert . " LIMIT 0,1";
            }
            $pageposts = $wpdb->get_results($query, ARRAY_A);
            if ($wpdb->last_error) {
                die($wpdb->last_error);
            } //$wpdb->last_error
            return $pageposts[0][$wert];
        } else {
            return false;
        }
    }

    /**
     * removes an option
     * @param array $where_array restriction
     * @return boolean
     */
    public static function removeOption($where_array = array())
    {
        global $wpdb;
        $TABLEname = self::$name;
        if (MCADB::check($TABLEname)) {
            $table_name = $wpdb->prefix . $TABLEname;
            $where = '';
            foreach ($where_array as $key => $value) {
                $where .= $key . " like '" . $value . "' " . $or;
            } //$where_array as $key => $value
            if (substr($where, -2) == $or) {
                $where = substr($where, 0, -2);
            } //substr($where, -2) == $or
            if (!empty($where)) {
                $query = "DELETE FROM " . $table . " WHERE " . $where;
            } //!empty($where)
            else {
                $query = "DELETE FROM " . $table;
            }
            $wpdb->query($query);
            if ($wpdb->last_error) {
                die($wpdb->last_error);
            } //$wpdb->last_error
            return true;
        } else {
            return false;
        }
    }

    /**
     * checks if a table exists
     * @param string $TABLEname table
     * @return boolean
     */
    public static function check($TABLEname = false)
    {
        global $wpdb;
        if (!$TABLEname) {
            $TABLEname = self::$name;
        } //!$TABLEname
        $table_name = $wpdb->prefix . $TABLEname;
        $DBname = $wpdb->dbname;
        $querystr = "SELECT COUNT(*) FROM $table_name LIMIT 0,1;";
        $pageposts = $wpdb->get_results($querystr, ARRAY_A);
        if (!$wpdb->last_error) {
            return true;
        } //$pageposts[0]["COUNT(*)"] == 1
        else {
            return false;
        }
    }

    /**
     * adds a table
     * @param string $TABLEname table
     * @param string $felder fields
     * @param string $primarykey primarykey
     * @return boolean false
     */
    public static function add($TABLEname, $felder, $primarykey = false)
    {
        global $wpdb;
        if (!MCADB::check($TABLEname)) {
            $table_name = $wpdb->prefix . $TABLEname;
            $sql = 'CREATE TABLE ' . $table_name . ' ( ';
            foreach ($felder as $name => $type) {
                $sql .= $name . ' ' . $type . ',';
            } //$felder as $name => $type
            if ($primarykey) {
                $sql .= ' PRIMARY KEY  (' . $primarykey . ') ';
            } //$primarykey
            $sql .= ");";
            $wpdb->query($sql);
            if ($wpdb->last_error) {
                die($wpdb->last_error);
            } //$wpdb->last_error
        } //!SELF::check($TABLEname)
    }

    /**
     * removes a table
     * @param string $TABLEname
     */
    public static function remove($TABLEname)
    {
        global $wpdb;
        if (MCADB::check($TABLEname)) {
            $table_name = $wpdb->prefix . $TABLEname;
            $sql = 'DROP TABLE ' . $table_name;
            $wpdb->query($sql);
            if ($wpdb->last_error) {
                die($wpdb->last_error);
            } //$wpdb->last_error
        } //!SELF::check($TABLEname)
    }
}
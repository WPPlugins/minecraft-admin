<?php
function smarty_function_mca_url($params, &$smarty)
{
    $other_url = $params;
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
    $smarty->assign($params['var'], site_url() . "/wp-admin/admin.php?page=minecraft-admin&amp;" . $other);
}
<?php
require_once('../wp-blog-header.php');
global $wpdb;

$one = $_POST['node_1'];
$two = $_POST['node_2'];

if ($two > $one)
{
    $swap = $one;
    $one = $two;
    $two = $one;
}

// Get user IP address
if ( isset($_SERVER['HTTP_CLIENT_IP']) && ! empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

$ip = filter_var($ip, FILTER_VALIDATE_IP);
$ip = ($ip === false) ? '0.0.0.0' : $ip;

$location = get_site_url() . '/sciencepark/index.html?failure_unknown';

if ($one != $two)
{
    $one_id = "";
    $one_db = $wpdb->get_row($wpdb->prepare("SELECT * FROM sp_nodes WHERE name = %s", $one));
    $two_id = "";
    $two_db = $wpdb->get_row($wpdb->prepare("SELECT * FROM sp_nodes WHERE name = %s", $two));

    if ($one_db && $two_db) {
        $one_id = $one_db->id;
        $two_id = $two_db->id;
        if (!$wpdb->get_row($wpdb->prepare("SELECT * FROM sp_reported WHERE node_1 = %d AND node_2 = %d AND ip = %s", $one_id, $two_id, $ip))) {
            if ($wpdb->insert("sp_reported", array("node_1" => $one_id, "node_2" => $two_id, "ip" => $ip), array("%d", "%d", "%s")));
                $location = get_site_url() . '/sciencepark/index.html?success';
        }
        else
            $location = get_site_url() . '/sciencepark/index.html?failure_duplicate';
    }
}

header('Location: ' . $location);
exit();

<?php
define('MOZG', true);
define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/app');
header('Content-type: text/html; charset=utf-8');

//AJAX
$ajax = $_POST['ajax'];
$logged = false;
$user_info = false;
include ENGINE_DIR.'/init.php';

require_once ROOT_DIR.'/api/api_functions.php';

$api_version = '1.0.0';
$response_data     = array();
$error_code    = 0;
$error_message = '';
$user_id = $user_info['user_id'];
$ids = (!empty($_GET['ids'])) ? Api_Secure($_GET['ids'], 0) : $user_id;
$type = (!empty($_GET['type'])) ? Api_Secure($_GET['type'], 0) : false;
$server_key = (!empty($_POST['server_key'])) ? Api_Secure($_POST['server_key'], 0) : false;

if (empty($type)) {
    $response_data       = array(
        'api_status'     => '404',
        'errors'         => array(
        'error_id'   => '1',
        'error_text' => 'Error: 404 API Type not specified'
        )
    );
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}

if ($type == 'users.get') {
    $response_data = $db->super_query("SELECT * FROM `".PREFIX."_users` WHERE user_id = '{$ids}'", true, "profile_{$ids}", true);
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}

if ($type == 'friends.get') {
    $response_data = $db->super_query("SELECT tb1.friend_id, tb2.user_birthday, user_photo, user_search_pref, user_country_city_name, user_last_visit, user_logged_mobile FROM `".PREFIX."_friends` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = '{$ids}' AND tb1.friend_id = tb2.user_id AND tb1.subscriptions = 0", 1);
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}

if ($type == 'friends.online.get') {
    $response_data = $db->super_query("SELECT tb1.user_id, user_country_city_name, user_search_pref, user_birthday, user_photo, user_logged_mobile FROM `".PREFIX."_users` tb1, `".PREFIX."_friends` tb2 WHERE tb1.user_id = tb2.friend_id AND tb2.user_id = '{$ids}' AND tb1.user_last_visit >= '{$online_time}' AND tb2.subscriptions = 0", 1);
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}

if ($type == 'friends.common.get') {
    $response_data = $db->super_query("SELECT tb1.friend_id, tb3.user_birthday, user_photo, user_search_pref, user_country_city_name, user_last_visit, user_logged_mobile FROM `".PREFIX."_users` tb3, `".PREFIX."_friends` tb1 INNER JOIN `".PREFIX."_friends` tb2 ON tb1.friend_id = tb2.user_id WHERE tb1.user_id = '{$user_info['user_id']}' AND tb2.friend_id = '{$ids}' AND tb1.subscriptions = 0 AND tb2.subscriptions = 0 AND tb1.friend_id = tb3.user_id ORDER by `friends_date`", 1);
    echo json_encode($response_data, JSON_PRETTY_PRINT);
    exit();
}

$db->close();
?>
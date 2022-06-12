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
$user_id = $user_info['user_id'];

$f = '';
$s = '';
if (isset($_GET['f'])) {
    $f = Requests_Secure($_GET['f'], 0);
}
if (isset($_GET['s'])) {
    $s = Requests_Secure($_GET['s'], 0);
}
$hash_id = '';
if (!empty($_POST['hash_id'])) {
    $hash_id = $_POST['hash_id'];
} else if (!empty($_GET['hash_id'])) {
    $hash_id = $_GET['hash_id'];
} else if (!empty($_GET['hash'])) {
    $hash_id = $_GET['hash'];
} else if (!empty($_POST['hash'])) {
    $hash_id = $_POST['hash'];
}
$data            = array();
$allow_array     = array(
    'subscriptions_add',
    'add_videos',
    'add_send_videos',
    'add_load_videos',
    'deleted_videos',
    'edit_videos',
    'edit_save_videos',
    'long_polling',    
    'subscriptions_deleted'
);
if (!in_array($f, $allow_array)) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            exit("Restrcited Area");
        }
    } else {
        exit("Restrcited Area");
    }
}
$files = scandir('app/xhr');
unset($files[0]);
unset($files[1]);
if (file_exists('app/xhr/' . $f . '.php') && in_array($f . '.php', $files)) {
    include 'app/xhr/' . $f . '.php';
}



unset($wo);
$tpl->clear();
$db->free();
$db->close();
exit();
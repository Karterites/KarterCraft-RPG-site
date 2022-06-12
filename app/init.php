<?php
if (!defined('MOZG')) die('Hacking attempt!');

@ini_set('session.cookie_httponly',1);
@ini_set('session.use_only_cookies',1);
@header("X-FRAME-OPTIONS: SAMEORIGIN");

//Подключаем конфигурацию
@include ENGINE_DIR . '/data/config.php';
@include ENGINE_DIR.'/data/social_config.php';

//Подключаем классы
include ENGINE_DIR . '/classes/mysql.php';
include ENGINE_DIR . '/classes/templates.php';

//Коннект к базе
include ENGINE_DIR . '/inc/db.php';

//Функция cookies
include ENGINE_DIR . '/data/cookies.php';

//Язык сайта
include ENGINE_DIR . '/data/langs.php';

//Функции сайта
include ENGINE_DIR . '/modules/functions.php';

//Подключаем шаблонизатор 
$tpl = new mozg_template;
$tpl->dir = ROOT_DIR . '/tpl/' . $config['temp'];
define('TEMPLATE_DIR', $tpl->dir);

//Время и ...
$_DOCUMENT_DATE = false;
$server_time = intval($_SERVER['REQUEST_TIME']);

//Проверка на авторизацию
include ENGINE_DIR . '/modules/login.php';

//Доп.модули плагины для работы сайта
if($config['site_stats'] == 'yes') include ENGINE_DIR . '/modules/simple_stats.php';
if ($config['offline'] == "yes") include ENGINE_DIR . '/modules/offline.php';
if ($user_info['user_delet'] or $user_info['user_delete_type'] != 0) include ENGINE_DIR . '/modules/profile_delet.php';
$sql_banned = $db->super_query("SELECT * FROM " . PREFIX . "_banned", true, "banned", true);
if (isset($sql_banned)) $blockip = check_ip($sql_banned);
else $blockip = false;
if ($user_info['user_ban_date'] >= $server_time OR $user_info['user_ban_date'] == '0' OR $blockip) include ENGINE_DIR . '/modules/profile_ban.php';

//Елси юзер залогинен то обновляем последнюю дату посещения в таблице друзей и на личной стр
if ($logged) {
    //Начисления 1 убм.
    if (!$user_info['user_lastupdate']) $user_info['user_lastupdate'] = 1;
    if (date('Y-m-d', $user_info['user_lastupdate']) < date('Y-m-d', $server_time)) $sql_balance = ", user_balance = user_balance+1, user_lastupdate = '{$server_time}'";
    //Определяем устройство
    if ($check_smartphone) $device_user = 1;
    else $device_user = 0;
    if (($user_info['user_last_visit'] + 60) <= $server_time) {
        $db->query("UPDATE LOW_PRIORITY `" . PREFIX . "_users` SET user_logged_mobile = '{$device_user}', user_last_visit = '{$server_time}' {$sql_balance} WHERE user_id = '{$user_info['user_id']}'");
    }
}

//Настройки групп пользователей
$user_group = unserialize(serialize(array(1 => array( #Администрация
'addnews' => '1',), 2 => array( #Главный модератор
'addnews' => '0',), 3 => array( #Модератор
'addnews' => '0',), 4 => array( #Техподдержка
'addnews' => '0',), 5 => array( #Пользователи
'addnews' => '0',),)));

//Время онлайна
$online_time = $server_time - $config['online_time'];

//FOR MOBILE VERSION 1.0
if ($config['temp'] == 'mobile') $lang['online'] = '<img src="/images/monline.gif" />';

//Автозагрузчик модулей
if($page = str_replace(array("/", "&#047;", "&#092;", "\\"), "", htmlspecialchars(strip_tags(stripslashes(trim(urldecode($_GET['go']))))))){
    if(is_file(ENGINE_DIR."/modules/{$page}.php"))
       include ENGINE_DIR."/modules/{$page}.php";
    else
       header('location: /');
} else 
    include ENGINE_DIR."/modules/register_main.php";
if ($go == 'register' OR $go == 'main' AND !$logged) include ENGINE_DIR . '/modules/register_main.php';




?>

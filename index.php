<?php
/*
	Сделано для пользователей форума cmstools.ru
*/
if (isset($_POST["PHPSESSID"])) {
    session_id($_POST["PHPSESSID"]);
}
@session_start();
@ob_start();
@ob_implicit_flush(0);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_WARNING);
define('MOZG', true);
define('ROOT_DIR', dirname(__FILE__));
define('ENGINE_DIR', ROOT_DIR . '/app');
header('Content-type: text/html; charset=utf-8');
//AJAX
$ajax = $_POST['ajax'];
$logged = false;
$user_info = false;
include ENGINE_DIR . '/init.php';

if (!$metatags['title']) $metatags['title'] = $config['home'];
if ($user_speedbar) $speedbar = $user_speedbar;
else $speedbar = $lang['welcome'];
$headers = '<title>' . $metatags['title'] . '</title>
<meta name="generator" content="CMS TOOLS" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />';

//Если юзер перешел по реф ссылке, то добавляем ид реферала в сессию
if ($_GET['reg']) $_SESSION['ref_id'] = intval($_GET['reg']);
//Опридиления браузера
if (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) $xBrowser = 'ie6';
elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) $xBrowser = 'ie7';
elseif (stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) $xBrowser = 'ie8';
if ($xBrowser == 'ie6' OR $xBrowser == 'ie7' OR $xBrowser == 'ie8') header("Location: /badbrowser.php");
//Загружаем кол-во новых новостей
$CacheNews = mozg_cache('user_' . $user_info['user_id'] . '/new_news');
if ($CacheNews) {
    $new_news = "<div class=\"headm_newac\" style=\"margin-left:18px\">{$CacheNews}</div>";
    $news_link = '/notifications';
}
//Загружаем кол-во новых подарков
$CacheGift = mozg_cache("user_{$user_info['user_id']}/new_gift");
if ($CacheGift) {
    $new_ubm = "<div class=\"headm_newac\" style=\"margin-left:20px\">{$CacheGift}</div>";
    $gifts_link = "/gifts{$user_info['user_id']}?new=1";
} else $gifts_link = '/balance';
//Новые сообщения
$user_pm_num = $user_info['user_pm_num'];
if ($user_pm_num) $user_pm_num = "<div class=\"headm_newac\" style=\"margin-left:-6px\">{$user_pm_num}</div>";
else $user_pm_num = '';
//Новые друзья
$user_friends_demands = $user_info['user_friends_demands'];
if ($user_friends_demands) {
    $demands = "<div class=\"headm_newac\">{$user_friends_demands}</div>";
    $requests_link = '/requests';
} else $demands = '';
//ТП
$user_support = $user_info['user_support'];
if ($user_support) $support = "<div class=\"headm_newac\" style=\"margin-left:26px\">{$user_support}</div>";
else $support = '';
//Отметки на фото
if ($user_info['user_new_mark_photos']) {
    $new_photos_link = 'newphotos';
    $new_photos = "<div class=\"headm_newac\" style=\"margin-left:22px\">" . $user_info['user_new_mark_photos'] . "</div>";
} else {
    $new_photos = '';
    $new_photos_link = $user_info['user_id'];
}
//Приглашения в сообщества
if ($user_info['invties_pub_num']) {
    $new_groups = "<div class=\"headm_newac\" style=\"margin-left:26px\">" . $user_info['invties_pub_num'] . "</div>";
    $new_groups_lnk = '/groups?act=invites';
} else {
    $new_groups = '';
    $new_groups_lnk = '/groups';
}

//Если включен AJAX то загружаем стр.
if($ajax == 'yes'){
    //Если есть POST Запрос и значение AJAX, а $ajax не равняется "yes" то не пропускаем
    if($_SERVER['REQUEST_METHOD'] == 'POST' AND $ajax != 'yes')
        die('Неизвестная ошибка');
        $result_ajax = array(
            'title' => $metatags['title'],
            'user_pm_num' => $user_pm_num,
            'new_news' => $new_news,
            'new_ubm' => $new_ubm,
            'gifts_link' => $gifts_link,
            'support' => $support,
            'news_link' => $news_link,
            'demands' => $demands,
            'guests' => $guests,
            'new_photos' => $new_photos,
            'new_photos_link' => $new_photos_link,
            'requests_link' => $requests_link,
            'new_groups' => $new_groups,
            'new_groups_lnk' => $new_groups_lnk,
            'sbar' => $spBar ? $speedbar : '',
            'content' => $tpl->result['info'].$tpl->result['content']
        );  
    $res = str_replace('{theme}', '/tpl/'.$config['temp'], $result_ajax);
    
    echo json_encode($res);
    $tpl->global_clear();
    $db->close();        
    die();
} 


$tpl->load_template('main.tpl');

//Если юзер залогинен
if($logged){
    $tpl->set(false,array('[logged]' => '', '[/logged]' => ''));
    $tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si","");
    $tpl->array_set(array(
        '{my-id}' => $user_info['user_id'],
        '{myname}' => $user_info['user_search_pref'],
        '{mysex}' => intval($user_info['user_sex']),
        '{tab-id}' => md5($server_time.'_'.$user_info['user_id']),
        '{ts}' => $server_time,
        '{demands}' => $demands,
        '{msg}' => $user_pm_num,
        '{new_photos}' => $new_photos,
        '{new_groups}' => $new_groups,
        '{requests-link}' => $requests_link,
        '{js_new_marks}' => $js_new_marks,
        '{new-news}' => $new_news,
        '{news-link}' => $news_link,
        '{my-page-link}' => '/u' . $user_info['user_id'],
        '{groups-link}' => $new_groups_lnk,
        '{new-ubm}' => $new_ubm,
        '{ubm-link}' => $gifts_link,
        '{new_guests}' => $guests,
        '{new-support}' => $support
    ));
}else{
    $tpl->set_block("'\\[logged\\](.*?)\\[/logged\\]'si","");
    $tpl->set(false, array(
        '[not-logged]' => '', 
        '[/not-logged]' => '',
        '{my-id}' => 0,
        '{tab-id}' => md5($server_time.'_'.$_IP),
        '{ts}' => $server_time,
    ));
}
if($config_social['worldomly_app_ID'] == false)
    $tpl->set_block("'\\[auth-w\\](.*?)\\[/auth-w\\]'si","");
else {
    $tpl->set('[auth-w]', '');
    $tpl->set('[/auth-w]', '');
}
$tpl->set('{worldomly_app_ID}', $config_social['worldomly_app_ID']);
$tpl->set('{header}', $headers);
$tpl->set('{speedbar}', $speedbar);
$tpl->set('{mobile-speedbar}', $mobile_speedbar);
$tpl->set('{info}', $tpl->result['info']);
$tpl->set('{content}', $tpl->result['content']);

//BUILD JS
if ($logged) $tpl->set('{js}', '<script type="text/javascript" src="/js/library/jquery.lib.js"></script>
<script type="text/javascript" src="/js/al/'.$_LANG.'/lang.js"></script>
<script type="text/javascript" src="/js/al/main.js"></script>
<script type="text/javascript" src="/js/al/profile.js"></script>');
else $tpl->set('{js}', '<script type="text/javascript" src="/js/library/jquery.lib.js"></script>
<script type="text/javascript" src="/js/al/'.$_LANG.'/lang.js"></script>
<script type="text/javascript" src="/js/al/main.js"></script>');

// FOR MOBILE VERSION 1.0
if ($user_info['user_photo']) $tpl->set('{my-ava}', "/uploads/users/{$user_info['user_id']}/50_{$user_info['user_photo']}");
else $tpl->set('{my-ava}', "/images/no_ava_50.png");



if ($user_info['user_cover']) $tpl->set('{my-cover}', "/uploads/users/{$user_info['user_id']}/{$user_info['user_cover']}");
else $tpl->set('{my-cover}', "/images/covers.png");





$tpl->set('{my-name}', $user_info['user_search_pref']);
if ($check_smartphone) $tpl->set('{mobile-link}', '<a href="/index.php?act=change_mobile">мобильная версия</a>');
else $tpl->set('{mobile-link}', '');
$tpl->set('{lang}', $_LANG);
$tpl->compile('main');
echo str_replace('{theme}', '/tpl/' . $config['temp'], $tpl->result['main']);
$tpl->global_clear();
$db->close();
?>
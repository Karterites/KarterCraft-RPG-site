<?php
/* 
	Appointment:  Панель управления
	File: adminpanel.php
	Engine: Vii Lait
	Данный код защищен авторскими правами
*/
@session_start();
@ob_start();
@ob_implicit_flush(0);

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

define('MOZG', true);
define('ROOT_DIR', dirname(__FILE__).'/../');
define('ENGINE_DIR', ROOT_DIR.'/system');
define('ADMIN_DIR', ROOT_DIR.'/app/inc');

@include ENGINE_DIR.'/data/config.php';

$admin_index = $config['admin_index'];

$admin_link = '/admin/'.$admin_index;

include ENGINE_DIR.'/classes/mysql.php';
include ENGINE_DIR.'/data/db.php';
include ADMIN_DIR.'/functions.php';
include ADMIN_DIR.'/login.php';

$db->close();
?>
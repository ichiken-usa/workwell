<?php 

// 関数まとめファイル
require_once (dirname(__FILE__). '/functions.php');

session_start();

// セッションクリア
$_SESSION = array();
session_destroy();

// ログイン画面へ遷移
redirect('/login.php');

?>
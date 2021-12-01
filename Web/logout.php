<?php 

session_start();

// セッションクリア
$_SESSION = array();
session_destroy();

// ログイン画面へ遷移
header('Location: /login.php');

?>
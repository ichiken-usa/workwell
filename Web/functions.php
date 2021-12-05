<?php

// DB接続
function connect_db(){

    ////
    // DB設定をパラメータファイルから読み出し
    ////
    require_once (dirname(__FILE__). '/../Config/config.php');
    Config::setConfigDirectory(dirname(__FILE__). '/../Config');
    $DB_NAME = Config::get('DB_NAME');
    $DB_HOST = Config::get('DB_HOST');
    $DB_USER = Config::get('DB_USER');
    $DB_PASSWORD = Config::get('DB_PASSWORD');


    $param = 'mysql:dbname='.$DB_NAME.';host='.$DB_HOST;
    $pdo = new PDO($param, $DB_USER, $DB_PASSWORD);
    $pdo->query('SET NAMES utf8;');

    // デフォルトだと連番とカラム名で重複出力されて無駄なので連番は出力しない
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $pdo;

}

// Datetimeを日付と曜日にフォーマット
function time_format_dw($date){

    $format_date = NULL;
    $week = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    if($date){
        // 日付
        $day = date('d', strtotime($date));

        // 曜日
        $dow_num = date('w', strtotime($date));
        $dow = $week[$dow_num];
        
        $format_date = $day.' ('.$dow.')';
    }

    return $format_date;
}

// Datetimeを月日曜にフォーマット
function time_format_mdw($date){

    $format_date = NULL;
    $week = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    if($date){
        // 日付
        $day = date('m/d', strtotime($date));

        // 曜日
        $dow_num = date('w', strtotime($date));
        $dow = $week[$dow_num];
        
        $format_date = $day.'('.$dow.')';
    }

    return $format_date;
}

function time_format_hm($time){

    $format_time = NULL;

    if($time){
        $format_time = date('H:i', strtotime($time));
    }
    return $format_time;
}

// HTMLエスケープ処理(XSS対策)
function escape($original_str){
    return htmlspecialchars($original_str, ENT_QUOTES, 'UTF-8');
}

// トークン発行
function set_token(){
    $token = sha1(uniqid(mt_rand(),true));
    $_SESSION['CSRF_TOKEN']= $token;
}

// セッションのトークンと画面のトークンが一致するか確認
function check_token(){
    //一致しなければ不正呼び出しと判断
    if(empty($_SESSION["CSRF_TOKEN"])||($_SESSION['CSRF_TOKEN']!=$_POST['CSRF_TOKEN'])){
        unset($pdo);
        redirect('/error.php');
    }
}

// 時刻フォーマット確認
function check_time_format($time){
    if (preg_match('/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/', $time)) {
        return true;
    }else{
        return false;
    }
}

// 指定のPHPへリダイレクト
function redirect($path){
    header('Location: '.$path);
    exit;
}
?>
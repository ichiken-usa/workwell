<?php

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

?>
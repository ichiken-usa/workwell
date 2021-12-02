<?php

// 関数まとめファイル
require_once (dirname(__FILE__). '/functions.php');

////
// 1.ログインをチェックしログインユーザー情報をセッションから取得
////

session_start();

if(!isset($_SESSION['USER'])){
    // ログインされていない場合はログイン画面へ
    header('Location: /login.php');
    exit;
}

// ログインユーザ情報をセッションから取得
$session_user = $_SESSION['USER'];

// 取得データ確認テスト
//var_dump($session_user);
//exit;

////
// 2.ユーザのデータをDBから取得
////

// 選択月を取得
if(isset($_GET['m'])){
    $yyyymm = $_GET['m'];
    $day_count = date('t', strtotime($yyyymm));
}else{
    $yyyymm = date('Y-m');
    $day_count = date('t');
}

// DB接続
$pdo = connect_db();

if ($_SERVER['REQUEST_METHOD']=='POST'){
    // 登録処理

    // 入力値をPOSTパラメータから取得
    $modal_start_time = $_POST['modal_start_time'];
    $modal_end_time = $_POST['modal_end_time'];
    $modal_break_time = $_POST['modal_break_time'];
    $modal_comment = $_POST['modal_comment'];

    // 入力日（当日）のデータがあるかどうか確認
    $sql = "SELECT id FROM d_work WHERE user_id = :user_id AND date = :date LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
    $stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
    $stmt->execute();
    $work = $stmt->fetch();   
    
    if($work){
        // 対象日のデータがあればUPDATE
        $sql = "UPDATE d_work SET start_time = :start_time, end_time = :end_time, break_time = :break_time, comment = :comment WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', (int)$work['id'], PDO::PARAM_INT);
        $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
        $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
        $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
        $stmt->execute();

    }else{
        // 対象日のデータが無ければINSERT
        $sql = "INSERT INTO d_work (user_id, date, start_time, end_time, break_time, comment) VALUES (:user_id, :date, :start_time, :end_time, :break_time, :comment)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
        $stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);    
        $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
        $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
        $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
        $stmt->execute();

    }

} 

// idと年月が一致する全データ取得
// 表で扱いやすいように行のキーを連番ではなく日付で取得
$sql = "SELECT date, id, start_time, end_time, break_time, comment FROM d_work WHERE user_id = :user_id AND DATE_FORMAT(date, '%Y-%m') = :date";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
$stmt->bindValue(':date', $yyyymm, PDO::PARAM_STR);
$stmt->execute();
$work_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);

// 取得データ確認テスト
// echo '<pre>';
// var_dump($work_list);
// echo '</pre>';
// exit;

// 当日のデータがあるかどうかチェック
$sql = "SELECT id, start_time, end_time, break_time, comment FROM d_work WHERE user_id = :user_id AND date = :date LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
$stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
$stmt->execute();
$today_work = $stmt->fetch();   

// モーダルの自動表示判定
$modal_view_flag = TRUE;

if ($today_work){
    //当日データがあったら読み込み
    $modal_start_time = $today_work['start_time'];
    $modal_end_time = $today_work['end_time'];
    $modal_break_time = $today_work['break_time'];
    $modal_comment = $today_work['comment'];

    // startとend両方入ってたら自動表示しない
    if(format_time($modal_start_time) && format_time($modal_end_time)){
        $modal_view_flag = FALSE;
    }
}else{
    // 当日データがない場合はモーダル初期化
    $modal_start_time = '';
    $modal_end_time = '';
    $modal_break_time = '01:00';
    $modal_comment = '';
}

// 3.DBから取得したデータをテーブルリスト表示

// 設定月の日数取得
$day_count = date('t', strtotime("2021-11"));



?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Self-made CSS -->
    <link href="/css/style.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href='//use.fontawesome.com/releases/v5.11.0/css/all.css' rel='stylesheet' type='text/css' />

    <title>Test | Login</title>
</head>

<body class="text-center bg-light">

    <h1 class="mb-4">Test</h1>

    <form class="border rounded bg-white form-time-table" action="index.php">
        <h2 class="h3 my-3">List</h2>

        <select class="form-select rounded-pill mb-3" name="m" onchange="submit(this.form)">
            <?php for($i = 0; $i < 12; $i++): ?>
                <?php $target_yyyymm = strtotime("-{$i}months"); ?>
                <option value="<?= date('Y-m', $target_yyyymm) ?>"<?php if($yyyymm == date('Y-m', $target_yyyymm)) echo 'selected' ?>><?= date('Y/m', $target_yyyymm) ?></option>
            <?php endfor; ?>
        </select>

        <table class="table table-hover table-bordered">
            <thead>
                <tr class="bg-light">
                    <th scope="col">Date</th>
                    <th scope="col">Start</th>
                    <th scope="col">End</th>
                    <th scope="col">Break</th>
                    <th scope="col">Comment</th>
                    <th scope="col">Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php for($i = 1; $i <= $day_count; $i++): ?>
                    <?php
                    // 表に入れるための変数へ代入
                        // 初期化
                        $start_time = '';
                        $end_time = '';
                        $break_time = '';
                        $comment = '';
                        
                        // 対象日取得
                        $target_date = date("Y-m-d", strtotime($yyyymm.'-'.$i));
                        
                        if(isset($work_list[$target_date])){
                            //対象日の配列データ取得
                            $work = $work_list[$target_date];
                        
                            // 対象日の整形
                            

                            // 時刻の表示フォーマット修正(秒を削除)

                            // start_time
                            if($work['start_time']){
                                $start_time = date('H:i', strtotime($work['start_time']));
                            }

                            // end_time
                            if($work['end_time']){
                                $end_time = date('H:i', strtotime($work['end_time']));
                            }

                            // break_time
                            if($work['break_time']){
                                $break_time = date('H:i', strtotime($work['break_time']));
                            }

                            // commentは一定文字数以上を省略
                            if($work['comment']){
                                $comment = mb_strimwidth($work['comment'],0,40,'...');
                            }
                        }
                    ?>
                <tr>
                    <th scope="row"><?= time_format_dw($target_date); ?></th>
                    <td><?= $start_time ?></td>
                    <td><?= $end_time ?></td>
                    <td><?= $break_time ?></td>
                    <td><?= $comment ?></td>
                    <td><i class="far fa-edit"></i></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </form>

    <!-- Modal -->
    <form method="POST">
        <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <p></p>
                        <h5 class="modal-title" id="exampleModalLabel">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-primary" role="alert">
                            <?= date('n').'/'.time_format_dw(date('Y-m-d')) ?>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="modal_start_time" name="modal_start_time" value="<?= format_time($modal_start_time) ?>" placeholder="Start">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" style="width:50px" id="start_btn">Set</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="modal_end_time" name="modal_end_time" value="<?= format_time($modal_end_time) ?>" placeholder="End">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" style="width:50px" id="end_btn">Set</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="modal_break_time" value="<?= format_time($modal_break_time) ?>"  placeholder="Break">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-3">
                                <textarea class="form-control" name="modal_comment" rows="5" placeholder="Comment"><?= $modal_comment ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary rounded-pill">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->

    <script>
        // モーダル自動表示
        <?php if($modal_view_flag): ?>
        var inputModal = new bootstrap.Modal(document.getElementById('inputModal'));
        inputModal.toggle();
        <?php endif ?>
        // startのsetボタン
        document.getElementById("start_btn").onclick = function() {
            const now = new Date();
            const hour = now.getHours().toString().padStart(2,'0');
            const minute = now.getMinutes().toString().padStart(2,'0');
            document.getElementById("modal_start_time").value = hour+':'+minute;
        };

        // endのsetボタン
        document.getElementById("end_btn").onclick = function() {
            const now = new Date();
            const hour = now.getHours().toString().padStart(2,'0');
            const minute = now.getMinutes().toString().padStart(2,'0');
            document.getElementById("modal_end_time").value = hour+':'+minute;
        };

    </script>

</body>

</html>
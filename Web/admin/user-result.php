<?php

// 関数まとめファイル
require_once (dirname(__FILE__). '/../functions.php');

////
// ログインをチェックしログインユーザー情報をセッションから取得
////

session_start();

if(!isset($_SESSION['USER'])){
    // ログインされていない場合はログイン画面へ
    header('Location: /login.php');
    exit;
}

// 選択ユーザ情報をセッションから取得
$user_id = $_REQUEST['id'];

// 取得データ確認テスト
//var_dump($user_id);
//exit;

////
// 選択月の取得
////

if(isset($_GET['m'])){
    // 選択されている場合は選択月取得
    $yyyymm = $_GET['m'];
    
}else{
    // 未選択の場合は当月を取得
    $yyyymm = date('Y-m');
}

$day_count = date('t', strtotime($yyyymm));

////
// DB登録処理
////

// 接続
$pdo = connect_db();

if ($_SERVER['REQUEST_METHOD']=='POST'){
    // Submit時

    // 入力値をPOSTパラメータから取得
    $modal_target_date = $_POST['modal_target_date'];
    $modal_start_time = $_POST['modal_start_time'];
    $modal_end_time = $_POST['modal_end_time'];
    $modal_break_time = $_POST['modal_break_time'];
    $modal_comment = $_POST['modal_comment'];

    // var_dump($modal_target_date);
    // var_dump($modal_start_time);
    // var_dump($modal_end_time);
    // var_dump($modal_break_time);
    // var_dump($modal_comment);

    // 入力日（当日）のデータがあるかどうか確認
    $sql = "SELECT id FROM d_work WHERE user_id = :user_id AND date = :date LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
    $stmt->bindValue(':date', $modal_target_date, PDO::PARAM_STR);
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
        $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
        $stmt->bindValue(':date', $modal_target_date, PDO::PARAM_STR);    
        $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
        $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
        $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
        $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
        $stmt->execute();

    }

} 

////
// リストデータ取得
////

// idと年月が一致する全データ取得
// 表で扱いやすいように行のキーを連番ではなく日付で取得
$sql = "SELECT date, id, start_time, end_time, break_time, comment FROM d_work WHERE user_id = :user_id AND DATE_FORMAT(date, '%Y-%m') = :date";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
$stmt->bindValue(':date', $yyyymm, PDO::PARAM_STR);
$stmt->execute();
$work_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);

// 取得データ確認テスト
// echo '<pre>';
// var_dump($work_list);
// echo '</pre>';
// exit;


////
// モーダル用の処理
////

$modal_break_ini = '01:00';



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

    <title>Test | Home</title>
</head>

<body class="text-center bg-primary">

    <h1 class="mb-4">Test Home</h1>


    <!--リストのフォーム-->
    <form class="border rounded bg-white form-time-table" action="user-result.php">
        <input type="hidden" name="id" value="<?= $user_id ?>">
        <h2 class="h3 my-3">List</h2>

        <div class="btn-toolbar my-3">
            <!-- 月選択 -->
            <select class="form-select rounded-pill m-2" name="m" onchange="submit(this.form)">
                <?php for($i = 0; $i < 12; $i++): ?>
                    <?php $target_yyyymm = strtotime("-{$i}months"); ?>
                    <option value="<?= date('Y-m', $target_yyyymm) ?>"<?php if($yyyymm == date('Y-m', $target_yyyymm)) echo 'selected' ?>><?= date('Y/m', $target_yyyymm) ?></option>
                <?php endfor; ?>
            </select>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary rounded-pill m-2" value="<?= date('Y-m-d') ?>" onclick="show_modal(this)">Register</button>
            <a href="/admin/user-list.php"><button type="button" class="btn btn-secondary rounded-pill m-2">Return</button></a>
        </div>



        <!-- リスト表示テーブル -->
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
                    // 表に入れるための変数初期化
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
                <tr id="tr_<?= $target_date; ?>">
                    <!-- データ行 -->
                    <th scope="row"><?= time_format_mdw($target_date); ?></th>
                    <td><?= $start_time ?></td>
                    <td><?= $end_time ?></td>
                    <td><?= $break_time ?></td>
                    <td><?= $comment ?></td>
                    <td><button type="button" class="btn h-auto py-0" style="width:40px" value="<?= $target_date; ?>" onclick="show_modal(this)"><i class="far fa-edit"></i></button></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </form>

    <!-- Modal -->
    <form method="POST">
        <input type="hidden" id="modal_target_date" name='modal_target_date' value="<?= date("Y-m-d") ?>"/>
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
                            <span id="modal_date" name="modal_date"><?= time_format_mdw(date('Y-m-d')) ?></span>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="modal_start_time" name="modal_start_time" value="<?= time_format_hm($modal_start_time) ?>" placeholder="Start">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" style="width:50px" id="start_btn">Set</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="modal_end_time" name="modal_end_time" value="<?= time_format_hm($modal_end_time) ?>" placeholder="End">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" style="width:50px" id="end_btn">Set</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="modal_break_time" name="modal_break_time" value="<?= time_format_hm($modal_break_time) ?>"  placeholder="Break">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group pt-3">
                                <textarea class="form-control" name="modal_comment" id="modal_comment" rows="5" placeholder="Comment"><?= $modal_comment ?></textarea>
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
        // 編集ボタンでモーダル立ち上げ
        function show_modal(obj){
            var inputModal = new bootstrap.Modal(document.getElementById('inputModal'))
            inputModal.toggle()
            
            // タグのオブジェクトからvalue取得
            var target_date = obj.value
            

            //対象日の表データを取得するためにtrタグに日付idを付与している。trの子要素を取得して次の要素の値取得
            var th = document.getElementById('tr_'+target_date).firstElementChild
            var day = th.innerText
            var start_time = th.nextElementSibling.innerText
            var end_time = th.nextElementSibling.nextElementSibling.innerText
            var break_time = th.nextElementSibling.nextElementSibling.nextElementSibling.innerText
            var comment = th.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling.innerText
            console.log(target_date)

            if(break_time == ''){
                break_time = '<?= $modal_break_ini; ?>'
            }

            // 取得したデータをモーダルへ入力
            document.getElementById("modal_date").innerText = day
            document.getElementById("modal_start_time").value = start_time
            document.getElementById("modal_end_time").value = end_time
            document.getElementById("modal_break_time").value = break_time
            document.getElementById("modal_comment").innerText = comment
            document.getElementById("modal_target_date").value = target_date

            
            
        }


        // startのsetボタン
        document.getElementById("start_btn").onclick = function() {
            const now = new Date()
            const hour = now.getHours().toString().padStart(2,'0')
            const minute = now.getMinutes().toString().padStart(2,'0')
            document.getElementById("modal_start_time").value = hour+':'+minute
        };

        // endのsetボタン
        document.getElementById("end_btn").onclick = function() {
            const now = new Date()
            const hour = now.getHours().toString().padStart(2,'0')
            const minute = now.getMinutes().toString().padStart(2,'0')
            document.getElementById("modal_end_time").value = hour+':'+minute
        };

    </script>

 
</body>

</html>
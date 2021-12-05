<?php

// php読み込み
require_once(dirname(__FILE__) . '/functions.php');

try {

    // 初期化
    $page_title = 'Home';
    $err = array();
    $modal_break_ini = '01:00';
    $modal_view_flag = TRUE;
    $target_date = date('Y-m-d');

    ////
    // ログインをチェックしログインユーザー情報をセッションから取得
    ////

    session_start();

    if (!isset($_SESSION['USER'])) {
        // ログインされていない場合はログイン画面へ
        redirect('/login.php');
    }

    // ログインユーザ情報をセッションから取得
    $session_user = $_SESSION['USER'];


    ////
    // DB登録処理
    ////

    // 接続
    $pdo = connect_db();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // POST時

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

        // 開始時間確認
        if (!empty($modal_start_time)) {
            if (!check_time_format($modal_start_time)) {
                $modal_start_time = '';
                $err['modal_start_time'] = 'Invalid time';
            }
        }

        // 終了時間確認
        if (!empty($modal_end_time)) {
            if (!check_time_format($modal_end_time)) {
                $modal_end_time = '';
                $err['modal_end_time'] = 'Invalid time';
            }
        }

        // 休憩時間確認
        if (!empty($modal_break_time)) {
            if (!check_time_format($modal_break_time)) {
                $modal_comment = '';
                $err['modal_break_time'] = 'Invalid time';
            }
        }

        // コメント文字数チェック
        if (mb_strlen($modal_comment, 'utf-8') > 2000) {
            $err['modal_comment'] = 'Text is too long (Less than 2000)';
        }

        // バリデーションチェック
        if (empty($err)) {

            // 入力日（当日）のデータがあるかどうか確認
            $sql = "SELECT id FROM d_work WHERE user_id = :user_id AND date = :date LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
            $stmt->bindValue(':date', $modal_target_date, PDO::PARAM_STR);
            $stmt->execute();
            $work = $stmt->fetch();

            if ($work) {
                // 対象日のデータがあればUPDATE
                $sql = "UPDATE d_work SET start_time = :start_time, end_time = :end_time, break_time = :break_time, comment = :comment WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', (int)$work['id'], PDO::PARAM_INT);
                $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
                $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
                $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
                $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                // 対象日のデータが無ければINSERT
                $sql = "INSERT INTO d_work (user_id, date, start_time, end_time, break_time, comment) VALUES (:user_id, :date, :start_time, :end_time, :break_time, :comment)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
                $stmt->bindValue(':date', $modal_target_date, PDO::PARAM_STR);
                $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
                $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
                $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
                $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
                $stmt->execute();
            }
            $modal_view_flag = FALSE;

        } else {
        // エラー時
            $modal_view_flag = TRUE;
        }
    } else {
        // POST以外
        ////
        // モーダル自動表示用の処理
        ////

        // モーダルを自動表示するかどうか判定するために当日のデータ取得
        $sql = "SELECT id, start_time, end_time, break_time, comment FROM d_work WHERE user_id = :user_id AND date = :date LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
        $stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
        $stmt->execute();
        $today_work = $stmt->fetch();

        if ($today_work) {
            //当日データがあったら格納
            $modal_start_time = $today_work['start_time'];
            $modal_end_time = $today_work['end_time'];
            $modal_break_time = $today_work['break_time'];
            $modal_comment = $today_work['comment'];
            // startとend両方入ってたら自動表示しない
            if (time_format_hm($modal_start_time) and time_format_hm($modal_end_time)) {
                $modal_view_flag = FALSE;
            }
        } else {
            // 当日データがない場合はモーダルの項目を初期化
            $modal_start_time = '';
            $modal_end_time = '';
            $modal_break_time = $modal_break_ini;
            $modal_comment = '';
        }
    }

    ////
    // リストデータ取得
    ////

    // 選択月の取得
    if (isset($_GET['m'])) {
        // 選択されている場合は選択月取得
        $selected_date = $_GET['m'];

        // Getの日付改ざん確認
        if (count(explode('-', $selected_date)) != 2) {
            throw new Exception('Invalid data', 500);
        }
    } else {
        // 未選択の場合は当月を取得
        $selected_date = date('Y-m');
    }
    // 選択月の日数取得
    $day_count = date('t', strtotime($selected_date));

    // 同じ月ならモーダルを自動表示
    if ($selected_date != date('Y-m')) {
        $modal_view_flag = FALSE;
    }

    // idと年月が一致する全データ取得
    // 表で扱いやすいように行のキーを連番ではなく日付で取得
    $sql = "SELECT date, id, start_time, end_time, break_time, comment FROM d_work WHERE user_id = :user_id AND DATE_FORMAT(date, '%Y-%m') = :date";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
    $stmt->bindValue(':date', $selected_date, PDO::PARAM_STR);
    $stmt->execute();
    $work_list = $stmt->fetchAll(PDO::FETCH_UNIQUE);
} catch (Exception $e) {
    redirect('/error.php');
}

?>

<!doctype html>
<html lang="en">

<!-- headタグ -->
<?php include('template/tag_head.php') ?>

<body class="text-center bg-light">

    <h1 class="mb-4">Test Home</h1>

    <!--リストのフォーム-->
    <form class="border rounded bg-white form-time-table" action="index.php">
        <h2 class="h3 my-3">List</h2>

        <div class="btn-toolbar my-3">

            <!-- 月選択 -->
            <select class="form-select rounded-pill m-2" name="m" onchange="submit(this.form)">
                <?php for ($i = 0; $i < 12; $i++) : ?>
                    <?php $dropdown_date = strtotime("-{$i}months"); ?>
                    <option value="<?= date('Y-m', $dropdown_date) ?>" <?php if ($selected_date == date('Y-m', $dropdown_date)) echo 'selected' ?>><?= date('Y/m', $dropdown_date) ?></option>
                <?php endfor; ?>
            </select>
            <!-- Modal button -->
            <button type="button" class="btn btn-primary rounded-pill m-2" value="<?= date('Y-m-d') ?>" onclick="show_modal(this)">Register</button>
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
                <?php for ($i = 1; $i <= $day_count; $i++) : ?>
                    <?php
                    // 表に入れるための変数初期化
                    $start_time = '';
                    $end_time = '';
                    $break_time = '';
                    $comment = '';

                    // 対象日取得
                    $list_date = date("Y-m-d", strtotime($selected_date . '-' . $i));

                    if (isset($work_list[$list_date])) {
                        //対象日の配列データ取得
                        $work = $work_list[$list_date];

                        // 対象日の整形


                        // 時刻の表示フォーマット修正(秒を削除)

                        // start_time
                        if ($work['start_time']) {
                            $start_time = date('H:i', strtotime($work['start_time']));
                        }

                        // end_time
                        if ($work['end_time']) {
                            $end_time = date('H:i', strtotime($work['end_time']));
                        }

                        // break_time
                        if ($work['break_time']) {
                            $break_time = date('H:i', strtotime($work['break_time']));
                        }

                        // commentは一定文字数以上を省略
                        if ($work['comment']) {
                            $comment = mb_strimwidth($work['comment'], 0, 40, '...');
                        }
                    }
                    ?>
                    <tr id="tr_<?= $list_date; ?>">
                        <!-- データ行 -->
                        <th scope="row"><?= time_format_mdw($list_date); ?></th>
                        <td><?= $start_time ?></td>
                        <td><?= $end_time ?></td>
                        <td><?= $break_time ?></td>
                        <td><?= escape($comment) ?></td>
                        <td><button type="button" class="btn h-auto py-0" style="width:40px" value="<?= $list_date; ?>" onclick="show_modal(this)"><i class="far fa-edit"></i></button></td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </form>

    <!-- Modal -->
    <form method="POST">
        <input type="hidden" id="modal_target_date" name='modal_target_date' value="<?= $target_date ?>" />
        <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <p></p>
                        <h5 class="modal-title" id="exampleModalLabel">Register</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 入力日 -->
                        <div class="alert alert-primary" role="alert">
                            <span id="modal_date" name="modal_date"><?= time_format_mdw($target_date) ?></span>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <!-- 開始時間 -->
                                    <div class="input-group">
                                        <input type="text" class="form-control <?php if (isset($err['modal_start_time'])) echo 'is-invalid'; ?>" id="modal_start_time" name="modal_start_time" value="<?= time_format_hm($modal_start_time) ?>" placeholder="Start">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" style="width:50px" id="start_btn">Set</button>
                                        </div>
                                        <div class="invalid-feedback"><?= $err['modal_start_time'] ?></div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <!-- 終了時間 -->
                                    <div class="input-group">
                                        <input type="text" class="form-control <?php if (isset($err['modal_end_time'])) echo 'is-invalid'; ?>" id="modal_end_time" name="modal_end_time" value="<?= time_format_hm($modal_end_time) ?>" placeholder="End">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" style="width:50px" id="end_btn">Set</button>
                                        </div>
                                        <div class="invalid-feedback"><?= $err['modal_end_time'] ?></div>
                                    </div>
                                </div>
                                <!-- 休憩時間 -->
                                <div class="col-sm">
                                    <div class="input-group">
                                        <input type="text" class="form-control <?php if (isset($err['modal_break_time'])) echo 'is-invalid'; ?>" id="modal_break_time" name="modal_break_time" value="<?= time_format_hm($modal_break_time) ?>" placeholder="Break">
                                    </div>
                                    <div class="invalid-feedback"><?= $err['modal_break_time'] ?></div>
                                </div>
                            </div>
                            <!-- コメント -->
                            <div class="form-group pt-3">
                                <textarea class="form-control <?php if (isset($err['modal_comment'])) echo 'is-invalid'; ?>" name="modal_comment" id="modal_comment" rows="5" placeholder="Comment"><?= $modal_comment ?></textarea>
                                <div class="invalid-feedback"><?= $err['modal_comment'] ?></div>
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

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script>
        // モーダル自動表示
        <?php if ($modal_view_flag) : ?>
            var inputModal = new bootstrap.Modal(document.getElementById('inputModal'))
            inputModal.toggle()
        <?php endif ?>

        // モーダル立ち上げボタン
        function show_modal(obj) {
            var inputModal = new bootstrap.Modal(document.getElementById('inputModal'))
            inputModal.toggle()

            // タグのオブジェクトからvalue取得
            var target_date = obj.value


            //対象日の表データを取得するためにtrタグに日付idを付与している。trの子要素を取得して次の要素の値取得
            var th = document.getElementById('tr_' + target_date).firstElementChild
            var day = th.innerText
            var start_time = th.nextElementSibling.innerText
            var end_time = th.nextElementSibling.nextElementSibling.innerText
            var break_time = th.nextElementSibling.nextElementSibling.nextElementSibling.innerText
            var comment = th.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling.innerText
            console.log(target_date)

            if (break_time == '') {
                break_time = '<?= $modal_break_ini; ?>'
            }

            // 取得したデータをモーダルへ入力
            document.getElementById("modal_date").innerText = day
            document.getElementById("modal_start_time").value = start_time
            document.getElementById("modal_end_time").value = end_time
            document.getElementById("modal_break_time").value = break_time
            document.getElementById("modal_comment").innerText = comment
            document.getElementById("modal_target_date").value = target_date

            // エラー表示をリセット
            document.getElementById("modal_date").classList.remove('is-invalid')
            document.getElementById("modal_start_time").classList.remove('is-invalid')
            document.getElementById("modal_end_time").classList.remove('is-invalid')
            document.getElementById("modal_break_time").classList.remove('is-invalid')
            document.getElementById("modal_comment").classList.remove('is-invalid')


        }

        // startのsetボタン
        document.getElementById("start_btn").onclick = function() {
            const now = new Date()
            const hour = now.getHours().toString().padStart(2, '0')
            const minute = now.getMinutes().toString().padStart(2, '0')
            document.getElementById("modal_start_time").value = hour + ':' + minute
        }

        // endのsetボタン
        document.getElementById("end_btn").onclick = function() {
            const now = new Date()
            const hour = now.getHours().toString().padStart(2, '0')
            const minute = now.getMinutes().toString().padStart(2, '0')
            document.getElementById("modal_end_time").value = hour + ':' + minute
        }
    </script>

</body>

</html>
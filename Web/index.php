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

    // 自動ログアウト
    check_session_expiration();

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
                $modal_start_time = null;
                $err['modal_start_time'] = 'Invalid time';
            }
        }else{
            //空なら
            $modal_start_time = null;
        }

        // 終了時間確認
        if (!empty($modal_end_time)) {
            if (!check_time_format($modal_end_time)) {
                $modal_end_time = null;
                $err['modal_end_time'] = 'Invalid time';
            }
        }else{
            // 空なら
            $modal_end_time = null;
        }

        // 休憩時間確認
        if (!empty($modal_break_time)) {
            if (!check_time_format($modal_break_time)) {
                $modal_comment = null;
                $err['modal_break_time'] = 'Invalid time';
            }
        }else{
            // 空なら
            $modal_break_time = null;
        }

        // コメント文字数チェック
        if (mb_strlen($modal_comment, 'utf-8') > 2000) {
            $err['modal_comment'] = 'Text is too long (Less than 2000)';
        }

        // バリデーションチェック
        if (empty($err)) { // 入力にエラーがない場合

            $modal_view_flag = FALSE;

            // 入力日（当日）のデータがあるかどうか確認
            $sql = "SELECT id FROM d_work WHERE user_id = :user_id AND date = :date LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
            $stmt->bindValue(':date', $modal_target_date, PDO::PARAM_STR);
            $stmt->execute();
            $work = $stmt->fetch();

            if ($work) { // 対象日のデータがあればUPDATE
                $sql = "UPDATE d_work SET start_time = :start_time, end_time = :end_time, break_time = :break_time, comment = :comment WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', (int)$work['id'], PDO::PARAM_INT);
                $stmt->bindValue(':start_time', $modal_start_time, PDO::PARAM_STR);
                $stmt->bindValue(':end_time', $modal_end_time, PDO::PARAM_STR);
                $stmt->bindValue(':break_time', $modal_break_time, PDO::PARAM_STR);
                $stmt->bindValue(':comment', $modal_comment, PDO::PARAM_STR);
                $stmt->execute();
            } else { // 対象日のデータがない場合はINSERT
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
        }
        // 入力にエラーがある場合
        else {
            // 入力エラー表示のためにモーダルを再度自動表示させる
            $modal_view_flag = TRUE;
        }
    } else { // POST以外

        // モーダルを自動表示するかどうか判定するために当日のデータ取得
        $sql = "SELECT id, start_time, end_time, break_time, comment FROM d_work WHERE user_id = :user_id AND date = :date LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', (int)$session_user['id'], PDO::PARAM_INT);
        $stmt->bindValue(':date', date('Y-m-d'), PDO::PARAM_STR);
        $stmt->execute();
        $today_work = $stmt->fetch();

        if ($today_work) { //当日データがあったら格納
            $modal_start_time = $today_work['start_time'];
            $modal_end_time = $today_work['end_time'];
            $modal_break_time = $today_work['break_time'];
            $modal_comment = $today_work['comment'];

            // startとend両方入ってたらモーダルを自動表示しない
            if (time_format_hm($modal_start_time) and time_format_hm($modal_end_time)) {
                $modal_view_flag = FALSE;
            }
        } else { // 当日データがない場合はモーダルの項目を初期化
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
    if (isset($_GET['m'])) { // 選択されている場合は選択月取得
        $selected_date = $_GET['m'];

        // Getの日付改ざん確認
        if (count(explode('-', $selected_date)) != 2) {
            throw new Exception('Invalid data', 500);
        }
    } else { // 未選択の場合は当月を取得
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

<body class="bg-light" style="padding-top:4.5rem;">

    <!-- Navbar -->
    <?php include('template/navbar.php') ?>

    <!-- 日報表示リスト読み込み -->
    <form class="border rounded bg-white form-time-table my-3 text-center " action="index.php">
        <h2 class="h3 my-3">List</h2>

        <div class="btn-toolbar my-3">

            <!-- 月ドロップダウン -->
            <?php include('template/dropdown_month.php') ?>
            <!-- Modal button -->
            <button type="button" class="btn btn-primary rounded-pill m-2" value="<?= date('Y-m-d') ?>" onclick="show_modal(this)">Register</button>
        </div>

        <!-- リスト表示テーブル -->
        <?php include('template/table_time_result.php') ?>
    </form>


    <!-- モーダル読み込み -->
    <?php include('template/form_modal_work_register.php') ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- 日報登録モーダル用のスクリプト読み込み -->
    <?php include('template/script_modal.php') ?>

</body>

</html>
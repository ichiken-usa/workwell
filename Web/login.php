<?php

// 関数まとめファイル
require_once(dirname(__FILE__) . '/functions.php');

// 初期化
$page_title = 'Login';

try {
    // セッション確認
    session_start();
    if (isset($_SESSION['USER'])) {
        // ログイン済みならHOMEへ遷移
        redirect('/');
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // POST処理時

        // 不正呼び出しか確認
        check_token();

        // 入力値取得
        $user_num = $_POST['user_num'];
        $password = $_POST['password'];

        // 入力テスト
        //echo $user_num.'<br>';
        //echo $password;
        //exit;

        // バリデーション
        $err = array();

        if (!$user_num) {
            $err['user_num'] = 'Please input User ID';
        } elseif (mb_strlen($user_num, 'utf-8') > 12) {
            $err['user_num'] = 'User ID is too long';
        }

        if (!$password) {
            $err['password'] = 'Please input Password';
        }

        // エラー文字が格納されているかテスト
        //var_dump($err);

        ////
        // データベース照合
        ////
        if (empty($err)) {
        // エラー無し時
            $pdo = connect_db();

            // SQLインジェクション対策で変数を直接SQL文に入れずプレースホルダを使う($stmt->bindValue)
            // ユーザーとパスワードが一致したらデータ取得
            $sql = "SELECT * FROM m_user WHERE user_num = :user_num LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_num', $user_num, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            ////
            // ログイン処理
            ////
            if ($user && password_verify($password, $user['password'])) {
            // 暗号化パスワード認証成功

                // セッションに保存
                $_SESSION['USER'] = $user;

                // HOME画面へ遷移
                redirect('/');

            } else {
            // 認証エラー
                $err['password'] = 'Password authentication failed';
            }
        }
    } else {
    // 画面初回アクセス時
        $user_num = "";
        $password = "";

        // これで発行したトークンを呼び出し元画面のhiddenに保存しておく
        set_token();
    }
} catch (Exception $e) {
    redirect('/error.php');
}
?>
<!doctype html>
<html lang="en">

<!-- headタグ読み込み -->
<?php include('template/tag_head.php') ?>

<body class="text-center bg-light">

    <!-- header読み込み -->
    <?php include('template/header.php') ?>

    <!-- loginフォーム読み込み -->
    <?php include('template/form_login.php') ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>
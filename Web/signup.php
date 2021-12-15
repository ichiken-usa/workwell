<?php
// 関数まとめファイル
require_once(dirname(__FILE__) . '/functions.php');

// 初期化
$page_title = 'Sign up';
$signup_message = '';


try {
    // セッション確認
    session_start();

    // POST処理時（Submit）
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // 不正呼び出しか確認
        check_token();

        // 入力値をPOSTパラメータから取得
        $user_num = $_POST['user_num'];
        $name = $_POST['name'];
        $password = $_POST['password'];

        // バリデーション
        $err = array();

        // ユーザーID入力なし（HTML側でも制限してるが一応）
        if (!$user_num) {
            $err['user_num'] = 'Please input User ID';
            // ユーザーID長すぎ
        } elseif (mb_strlen($user_num, 'utf-8') > 12) {
            $err['user_num'] = 'User ID is too long';
            // ユーザーID短すぎ
        } elseif (mb_strlen($user_num, 'utf-8') < 4) {
            $err['user_num'] = 'User ID is too short';
        }

        // 名前入力確認
        if (!$name) {
            $err['name'] = 'Please input Name';
            // 名前長すぎ
        } elseif (mb_strlen($name, 'utf-8') > 32) {
            $err['name'] = 'Name is too long';
            // 名前短すぎ
        } elseif (mb_strlen($name, 'utf-8') < 2) {
            $err['name'] = 'Name is too short';
        }

        // パスワード入力チェック
        if (!$password) {
            $err['password'] = 'Please input Password';
        } elseif (mb_strlen($password, 'utf-8') > 24) {
            // パスワード長すぎチェック
            $err['password'] = 'Password is too long';
        } elseif (mb_strlen($password, 'utf-8') < 4) {
            // パスワード短すぎチェック
            $err['password'] = 'Password is too short';
        }

        ////
        // データベース照合
        ////

        // エラー無しならDBからユーザ情報取得
        if (empty($err)) {

            $pdo = connect_db();

            // ユーザID確認
            $sql = "SELECT * FROM m_user WHERE user_num = :user_num LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_num', $user_num, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            // 認証エラー: ユーザーが見つかったら既に登録済み
            if ($user) {
                $err['user_num'] = 'Already used';
            } else {
                // ユーザが存在しないので登録可能。INSERT処理
                $sql = "INSERT INTO m_user (user_num, name, password) VALUES (:user_num, :name, :password)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':user_num', $user_num, PDO::PARAM_STR);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $stmt->execute();

                $signup_message = "Welcome! " . $name . " [ " . $user_num . " ]";

                $user_num = '';
                $name = '';
                $password = '';
            }
        }
    } else {
        // 画面初回アクセス時初期化
        $user_num = '';
        $name = '';
        $password = '';

        // 発行したトークンを呼び出し元画面のhiddenに保存しておく
        set_token();
    }
} catch (Exception $e) {
    redirect('/error.php');
}
?>

<!doctype html>
<html>
<!-- headタグ読み込み -->
<?php include('template/tag_head.php') ?>

<body class="bg-light" style="padding-top:4.5rem;">

    <!-- Navbar -->
    <?php include('template/navbar.php') ?>

    <form class="border rounded bg-white form-login my-5 text-center" method="post">
        <!-- トークン用hiddenタグ -->
        <input type="hidden" name="CSRF_TOKEN" value="<?= $_SESSION['CSRF_TOKEN'] ?>">

        <h2 class="h3 my-3">Sign up</h2>

        <!-- 登録成功メッセージ -->
        <div class="my-3"><?= escape($signup_message) ?></div>


        <div class="form-group p-2">
            <!-- ログインID: エラーが起きても入力値を保持するようにvalue設定 -->
            <label class="form-label">User ID</label>
            <input type="text" class="form-control rounded-pill <?php if (isset($err['user_num'])) echo 'is-invalid'; ?>" name="user_num" value="<?= escape($user_num) ?>" placeholder="User ID (4 - 12)" required>
            <div class="invalid-feedback"><?= $err['user_num'] ?></div>
        </div>
        <div class="form-group p-2">
            <!-- User name -->
            <label class="form-label">User name</label>
            <input type="text" class="form-control rounded-pill <?php if (isset($err['name'])) echo 'is-invalid'; ?>" name="name" value="<?= escape($name) ?>" placeholder="Name (2 - 32)" required>
            <div class="invalid-feedback"><?= $err['name'] ?></div>
        </div>
        <div class="form-group p-2">
            <!-- パスワード -->
            <label class="form-label">Password</label>
            <input type="password" class="form-control rounded-pill <?php if (isset($err['password'])) echo 'is-invalid'; ?>" name="password" placeholder="Password (4 - 24)" required>
            <div class="invalid-feedback"><?= $err['password'] ?></div>
        </div>

        <div class="row mt-4">
            <div class="col"></div>
            <div class="col">
                <button type="submit" class="btn btn-primary text-white rounded-pill my-3">Sign up</button>
            </div>
            <div class="col">
                <!-- Admin画面用の戻るボタン -->
                <a href="/logout.php"><button type="button" class="btn col-sm btn-secondary rounded-pill my-3">Return</button></a>
            </div>
            <div class="col"></div>
        </div>
    </form>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>
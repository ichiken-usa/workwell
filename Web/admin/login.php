<?php

// 関数まとめファイル
require_once (dirname(__FILE__). '/../functions.php');

// 初期化
$page_title = "Login Admin";

try{
    // セッション確認
    session_start();
    if(isset($_SESSION['USER']) && $_SESSION['USER']['type']){
        // ログイン済みならHOMEへ遷移
        redirect('/admin/user-list.php');
    }

    ////
    // ログイン処理
    ////

    // POST処理時（Submit）
    if($_SERVER['REQUEST_METHOD']=='POST'){

        // 不正呼び出しか確認
        check_token();

        $user_num = $_POST['user_num'];
        $password = $_POST['password'];

        // 入力テスト
        //echo $user_num.'<br>';
        //echo $password;
        //exit;

        // バリデーション
        $err = array();

        // ユーザID入力チェック
        $error_message = null;
        $error_message = check_length($user_num, 4, 12, 'User ID');
        if (!empty($error_message)){
            $err['user_num'] = $error_message;
        }

        // パスワード入力チェック
        $error_message = null;
        $error_message = check_length($password, 4, 24, 'Password');
        if (!empty($error_message)){
            $err['password'] = $error_message;
        }

        // エラー文字が格納されているかテスト
        //var_dump($err);

        ////
        // データベース照合
        ////

        // エラー無しならDBからユーザ情報取得
        if(empty($err)){

            $pdo = connect_db();

            // SQLインジェクション対策で変数を直接SQL文に入れずプレースホルダを使う($stmt->bindValue)
            // パスワードは暗号化しているのでそのままでは比較不可 -> password_verifyを使う
            $sql = "SELECT * FROM m_user WHERE user_num = :user_num LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_num', $user_num, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if($user && password_verify($password, $user['password'])){

                // セッションに保存
                $_SESSION['USER'] = $user;
                $_SESSION['timestamp'] = time();

                // HOME画面へ遷移
                redirect('/admin/user-list.php');

            }else{
                // 認証エラー
                $err['password'] = 'Password authentication failed';
            }

        }

    // 画面初回アクセス時
    }else{    
        $user_num="";
        $password="";

        // 発行したトークンを呼び出し元画面のhiddenに保存しておく
        set_token();

    }
}catch(Exception $e){
    redirect('/error.php');
}
?>
<!doctype html>
<html lang="en">

<!-- headタグ読み込み -->
<?php include(dirname(__FILE__). '/../template/tag_head.php') ?>

<body class="text-center bg-primary" style="padding-top:4.5rem;">

    <!-- Navbar -->
    <?php include(dirname(__FILE__). '/../template/navbar.php') ?>

    <!-- loginフォーム読み込み -->
    <?php include(dirname(__FILE__).'/../template/form_login.php') ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>
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

    // POST処理時
    if($_SERVER['REQUEST_METHOD']=='POST'){

        ////
        // 1.入力値取得
        ////
        $user_num = $_POST['user_num'];
        $password = $_POST['password'];

        // 入力テスト
        //echo $user_num.'<br>';
        //echo $password;
        //exit;

        ////
        // 2.バリデーションチェック
        ////
        $err = array();

        if(!$user_num){
            $err['user_num']='Please input User ID';
        }elseif(mb_strlen($user_num, 'utf-8') > 12){
            $err['user_num']='User ID is too long';
        }

        if(!$password){
            $err['password']='Please input Password';
        }

        // エラー文字が格納されているかテスト
        //var_dump($err);

        ////
        // 3.データベース照合
        ////
        if(empty($err)){

            $pdo = connect_db();

            // SQLインジェクション対策で変数を直接SQL文に入れずプレースホルダを使う($stmt->bindValue)
            // ユーザーとパスワードが一致したらデータ取得
            $sql = "SELECT * FROM m_user WHERE user_num = :user_num LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_num', $user_num, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if($user && password_verify($password, $user['password'])){
                ////
                // 4.ログイン処理
                ////

                // セッションに保存
                $_SESSION['USER'] = $user;

                // HOME画面へ遷移
                redirect('/admin/user-list.php');

            }else{
                // 認証エラー
                $err['password'] = 'Password authentication failed';
            }

        }


    }else{
        // 画面初回アクセス時
        $user_num="";
        $password="";

        // トークン発行
        set_token();

    }
}catch(Exception $e){
    header('Location: /error.php');
}
?>
<!doctype html>
<html lang="en">

<!-- headタグ読み込み -->
<?php include(dirname(__FILE__). '/../template/tag_head.php') ?>

<body class="text-center bg-primary">

    <!-- header読み込み -->
    <?php include(dirname(__FILE__).'/../template/header.php') ?>

    <!-- loginフォーム読み込み -->
    <?php include(dirname(__FILE__).'/../template/form_login.php') ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>
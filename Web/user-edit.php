<?php
// 関数まとめファイル
require_once(dirname(__FILE__) . '/functions.php');

// 初期化
$page_title = 'Edit';
$user_num = '';
$name = '';

$message = '';

try {
    // セッション確認
    session_start();

    // ログインされていない or Adminフラグ無しでリダイレクト
    if(!isset($_SESSION['USER'])){
        redirect('/login.php');
    }

    // 選択ユーザ情報をセッションから取得
    //$id = $_REQUEST['id'];

    // ログインユーザ情報をセッションから取得
    $session_user = $_SESSION['USER'];
    $id = (int)$session_user['id'];
    
    // DB接続
    $pdo = connect_db();

    // POST処理時（Submit）
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // 入力値をPOSTパラメータから取得
        $user_num = $_POST['user_num'];
        $name = $_POST['name'];


        // バリデーション
        $err = array();

        // ユーザーID入力なし（HTML側でも制限してるが一応）
        // if (!$user_num) {
        //     $err['user_num'] = 'Please input User ID';
        //     // ユーザーID長すぎ
        // } elseif (mb_strlen($user_num, 'utf-8') > 12) {
        //     $err['user_num'] = 'User ID is too long';
        //     // ユーザーID短すぎ
        // } elseif (mb_strlen($user_num, 'utf-8') < 4) {
        //     $err['user_num'] = 'User ID is too short';
        // }

        // ユーザ名入力チェック
        $error_message = null;
        $error_message = check_length($user_num, 4, 12, 'User ID');
        if (!empty($error_message)){
            $err['user_num'] = $error_message;
        }

        // 名前入力チェック
        $error_message = null;
        $error_message = check_length($name, 2, 32, 'User Name');
        if (!empty($error_message)){
            $err['name'] = $error_message;
        }


        ////
        // データベース照合
        ////

        // エラー無しならDBからユーザ情報取得
        if (empty($err)) {

            // ユーザID確認
            $sql = "SELECT * FROM m_user WHERE id = :id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch();

            // idの存在確認
            if ($user) {
                // idがあるのでデータアップデート
                $sql = "UPDATE m_user SET user_num = :user_num, name = :name WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_STR);
                $stmt->bindValue(':user_num', $user_num, PDO::PARAM_STR);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->execute();

                //$message = 'Updated!';


            } else {
                // idが存在しないので登録不可
                $message = 'There is no id';

            }
        }
    } else {
        // 画面初回アクセス時にIDからデータ取得

        // ユーザID確認
        $sql = "SELECT * FROM m_user WHERE id = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        if($user){
            // ユーザ情報ありならデータ表示
            $user_num = $user['user_num'];
            $name = $user['name'];
        }else{
            // ユーザ情報なしでエラー
            redirect('error.php');
        }


    }
} catch (Exception $e) {
    echo $e;
    redirect('/error.php');
}


?>

<!doctype html>
<html>
<!-- headタグ読み込み -->
<?php include(dirname(__FILE__) . '/template/tag_head.php') ?>

<body class="bg-light" style="padding-top:4.5rem;">

    <!-- Navbar -->
    <?php include(dirname(__FILE__) . '/template/navbar.php') ?>

    <!-- Form: user edit -->
    <?php include(dirname(__FILE__) . '/template/form_user_edit_for_user.php') ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


</body>

</html>
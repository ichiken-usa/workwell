<?php
// 関数まとめファイル
require_once(dirname(__FILE__) . '/../functions.php');

// 初期化
$page_title = 'Edit';
$user_num = '';
$name = '';
$type = '';
$message = '';

try {
    // セッション確認
    session_start();

    // ログインされていない or Adminフラグ無しでリダイレクト
    if(!isset($_SESSION['USER']) || $_SESSION['USER']['type'] != 1){
        redirect('/admin/login.php');
    }

    // 選択ユーザ情報をセッションから取得
    $id = $_REQUEST['id'];

    if(!$id){
        throw new Exception('Invalid user_id', 500);
    }
    
    // DB接続
    $pdo = connect_db();

    // POST処理時（Submit）
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // 入力値をPOSTパラメータから取得
        $user_num = $_POST['user_num'];
        $name = $_POST['name'];
        $type = $_POST['type'];

        // 空だと自動で0が入るのでNULL変換
        if($type==''){
            $type = NULL;
        }

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

        // type入力チェック
        if(!$type){
            //nullの長さチェックはエラーになるのでifで制限
        }
        elseif (mb_strlen($type, 'utf-8') > 1) {
            // パスワード長すぎチェック
            $err['type'] = 'Type is too long';
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
                $sql = "UPDATE m_user SET user_num = :user_num, name = :name, type = :type WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $id, PDO::PARAM_STR);
                $stmt->bindValue(':user_num', $user_num, PDO::PARAM_STR);
                $stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $stmt->bindValue(':type', $type, PDO::PARAM_INT);
                $stmt->execute();

                //$message = 'Updated!';

                // Update完了でリダイレクト
                redirect('/admin/user-list.php');

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
            $type = $user['type'];
        }else{
            // ユーザ情報なしで初期化
            $user_num = '';
            $name = '';
            $type = '';
        }


    }
} catch (Exception $e) {
    redirect('/error.php');
}


?>

<!doctype html>
<html>
<!-- headタグ読み込み -->
<?php include(dirname(__FILE__). '/../template/tag_head.php') ?>

<body class="bg-primary" style="padding-top:4.5rem;">

    <!-- Navbar -->
    <?php include(dirname(__FILE__). '/../template/navbar.php') ?>

    <!-- Form: user edit -->
    <?php include(dirname(__FILE__). '/../template/form_user_edit.php') ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


</body>

</html>
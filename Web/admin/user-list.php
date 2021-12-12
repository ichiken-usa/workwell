<?php
// 関数まとめファイル
require_once (dirname(__FILE__). '/../functions.php');

// 初期化
$page_title = 'User list';


try{
    // セッション確認
    session_start();
    // ログインされていない or Adminフラグ無しでリダイレクト
    if(!isset($_SESSION['USER']) || $_SESSION['USER']['type'] != 1){
        redirect('/admin/login.php');
    }

    // DBから全ユーザリスト取得
    $pdo = connect_db();

    $sql = "SELECT * FROM m_user";
    $stmt = $pdo->query($sql);
    $user_list = $stmt->fetchAll();

}catch(Exception $e){
    redirect('/error.php');
}
?>

<!doctype html>
<html lang="en">

<!-- headタグ読み込み -->
<?php include(dirname(__FILE__). '/../template/tag_head.php') ?>

<body class="text-center bg-primary">

    <!-- header読み込み -->
    <?php include(dirname(__FILE__).'/../template/header_admin.php') ?>

    <!-- ユーザーリスト -->
    <?php include(dirname(__FILE__).'/../template/user_list.php') ?>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>
<?php
// 関数まとめファイル
require_once (dirname(__FILE__). '/../functions.php');

try{
    // セッション確認
    session_start();
    if(!isset($_SESSION['USER']) || $_SESSION['USER']['type'] != 1){
        // ログインされていないならログイン画面へ
        header('Location:/admin/login.php');
        exit;
    }

    $pdo = connect_db();

    $sql = "SELECT * FROM m_user";
    $stmt = $pdo->query($sql);
    $user_list = $stmt->fetchAll(); 
}catch(Exception $e){
    header('Location: /error.php');
}
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

    <title>Test | Login Admin</title>
</head>

<body class="text-center bg-primary">

    <h1 class="mb-4 text-white">Test</h1>

    <form class="border rounded bg-white form-user-table">
        <h2 class="h3 my-3">User List</h2>
        <table class="table table-hover table-bordered">
            <thead>
                <tr class="bg-light">
                    <th scope="col">User ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($user_list as $user): ?>
                <tr>
                    <th scope="row"><?= $user['user_num'] ?></th>
                    <!-- 選択ユーザをGETパラメータで渡す -->
                    <td><a href="/admin/user-result.php?id=<?= $user['id'] ?>"><?= $user['name'] ?></td>
                    <td><?php if($user['type'] == 1) echo 'Administrator' ?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </form>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>
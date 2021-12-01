<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
    // POST処理時

    // 1.入力値取得
    $user_num = $_POST['user_num'];
    $password = $_POST['password'];

    // 入力テスト
    //echo $user_num.'<br>';
    //echo $password;
    //exit;

    // 2.バリデーションチェック
    $err = array();

    if(!$user_num){
        $err['user_num']='Please input User ID.';
    }

    if(!$password){
        $err['password']='Please input Password.';
    }

    // エラー文字が格納されているかテスト
    //var_dump($err);

    // 3.データベース称号

    // 4.ログイン処理

    // 5.home画面へ遷移

}else{
    // 画面初回アクセス時
    $user_num="";
    $password="";

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

    <title>Test | Login</title>
</head>

<body class="text-center bg-light">

    <h1 class="mb-4">Test</h1>

    <form class="border rounded bg-white form-login" method="post">
        <h2 class="h3 my-3">Login</h2>
        <div class="form-group p-3">

            <!-- ログインID: バリデーションエラーが起きても入力値を保持するようにvalue設定 -->
            <input type="text" class="form-control rounded-pill<?php if(isset($err['user_num'])) echo ' is-invalid'; ?>" name="user_num" value="<?= $user_num ?>" placeholder="User ID">
            <div class="invalid-feedback"><?= $err['user_num'] ?></div>
        </div>
        <div class="form-group p-3">

            <input type="password" class="form-control rounded-pill<?php if(isset($err['password'])) echo ' is-invalid'; ?>" name="password" placeholder="Password">
            <div class="invalid-feedback"><?= $err['password'] ?></div>
        </div>

        <button type="submit" class="btn btn-primary text-white rounded-pill px-5 my-4">Login</button>
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
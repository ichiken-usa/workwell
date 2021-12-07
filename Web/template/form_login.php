<form class="border rounded bg-white form-login" method="post">
    <!-- トークン用hiddenタグ -->
    <input type="hidden" name="CSRF_TOKEN" value="<?= $_SESSION['CSRF_TOKEN'] ?>">

    <h2 class="h3 my-3">Login</h2>
    <div class="form-group p-3">

        <!-- ログインID: バリデーションエラーが起きても入力値を保持するようにvalue設定 -->
        <input type="text" class="form-control rounded-pill <?php if (isset($err['user_num'])) echo 'is-invalid'; ?>" name="user_num" value="<?= $user_num ?>" placeholder="User ID" required>
        <div class="invalid-feedback"><?= $err['user_num'] ?></div>
    </div>
    <div class="form-group p-3">
        <!-- パスワード -->
        <input type="password" class="form-control rounded-pill <?php if (isset($err['password'])) echo 'is-invalid'; ?>" name="password" placeholder="Password">
        <div class="invalid-feedback"><?= $err['password'] ?></div>
    </div>


    <div class="row mt-4">
            <div class="col"></div>
            <div class="col">
                <button type="submit" class="btn btn-primary text-white rounded-pill my-3">Login</button>
            </div>
            <div class="col">
                <!-- Admin画面用の戻るボタン -->
                <a href="/signup.php"><button type="button" class="btn col-sm btn-success rounded-pill my-3">Sign up</button></a>
            </div>
            <div class="col"></div>
        </div>
    </form>

</form>
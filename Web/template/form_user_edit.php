<form class="border rounded bg-white form-login text-center" method="post">

    <h2 class="h3 my-3">Edit</h2>

    <!-- 登録成功メッセージ -->
    <div class="my-3"><?= escape($message) ?></div>


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
        <!-- type -->
        <label class="form-label">Type</label>
        <input type="number" class="form-control rounded-pill <?php if (isset($err['type'])) echo 'is-invalid'; ?>" name="type" value="<?= escape($type) ?>" placeholder="Type [null:normal 1:admin]">
        <div class="invalid-feedback"><?= $err['type'] ?></div>
    </div>

    <div class="row mt-4">
        <div class="col"></div>
        <div class="col">
            <button type="submit" class="btn btn-primary text-white rounded-pill my-3">Update</button>
        </div>
        <div class="col">
            <!-- Admin画面用の戻るボタン -->
            <a href="/admin/user-list.php"><button type="button" class="btn col-sm btn-secondary rounded-pill my-3">Return</button></a>
        </div>
        <div class="col"></div>
    </div>
</form>
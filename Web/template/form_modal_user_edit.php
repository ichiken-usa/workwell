<!-- Modal -->
<form method="POST">
    <input type="hidden" id="modal_target_date" name='modal_target_date' value="<?= $target_date ?>" />
    <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p></p>
                    <h5 class="modal-title" id="exampleModalLabel">Edit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 入力日 -->
                    <div class="alert alert-primary" role="alert">
                        <span id="modal_user_id" name="modal_user_id"><?= $id ?></span>
                    </div>
                    <div class="form-group p-3">
                        <!-- ログインID: エラーが起きても入力値を保持するようにvalue設定 -->
                        <input type="text" class="form-control rounded-pill <?php if (isset($err['user_num'])) echo 'is-invalid'; ?>" name="user_num" value="<?= escape($user_num) ?>" placeholder="User ID (4 - 12)" required>
                        <div class="invalid-feedback"><?= $err['user_num'] ?></div>
                    </div>
                    <div class="form-group p-3">
                        <!-- User name -->
                        <input type="text" class="form-control rounded-pill <?php if (isset($err['name'])) echo 'is-invalid'; ?>" name="name" value="<?= escape($name) ?>" placeholder="Name (2 - 32)" required>
                        <div class="invalid-feedback"><?= $err['name'] ?></div>
                    </div>
                    <div class="form-group p-3">
                        <!-- パスワード -->
                        <input type="password" class="form-control rounded-pill <?php if (isset($err['password'])) echo 'is-invalid'; ?>" name="password" placeholder="Password (4 - 24)" required>
                        <div class="invalid-feedback"><?= $err['password'] ?></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary rounded-pill">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
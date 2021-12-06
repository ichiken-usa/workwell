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
            <?php foreach ($user_list as $user) : ?>
                <tr>
                    <th scope="row"><?= $user['user_num'] ?></th>
                    <!-- 選択ユーザをGETパラメータで渡す -->
                    <td><a href="/admin/user-result.php?id=<?= $user['id'] ?>"><?= $user['name'] ?></td>
                    <td><?php if ($user['type'] == 1) echo 'Administrator' ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</form>
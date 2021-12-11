<form class="border rounded bg-white form-user-table">
    <h2 class="h3 my-3">User List</h2>
    <table class="table table-hover table-bordered">
        <thead>
            <tr class="bg-light">
                <th scope="col">User ID</th>
                <th scope="col">Name</th>
                <th scope="col">Type</th>
                <th scope="col">Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($user_list as $user) : ?>
                <tr>
                    <th scope="row"><?= $user['user_num'] ?></th>
                    <!-- 選択ユーザをGETパラメータで渡す -->
                    <td><a href="/admin/user-result.php?id=<?= $user['id'] ?>"><?= $user['name'] ?></td>
                    <td><?= $user['type'] ?></td>
                    <td><button type="button" class="btn h-auto py-0" style="width:40px" value="<?= $user['id']; ?>" onclick="show_modal(this)"><i class="far fa-edit"></i></button></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</form>
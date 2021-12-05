<form class="border rounded bg-white form-time-table" action="index.php">
    <h2 class="h3 my-3">List</h2>

    <div class="btn-toolbar my-3">

        <!-- 月選択 -->
        <select class="form-select rounded-pill m-2" name="m" onchange="submit(this.form)">
            <?php for ($i = 0; $i < 12; $i++) : ?>
                <?php $dropdown_date = strtotime("-{$i}months"); ?>
                <option value="<?= date('Y-m', $dropdown_date) ?>" <?php if ($selected_date == date('Y-m', $dropdown_date)) echo 'selected' ?>><?= date('Y/m', $dropdown_date) ?></option>
            <?php endfor; ?>
        </select>
        <!-- Modal button -->
        <button type="button" class="btn btn-primary rounded-pill m-2" value="<?= date('Y-m-d') ?>" onclick="show_modal(this)">Register</button>
    </div>

    <!-- リスト表示テーブル -->
    <table class="table table-hover table-bordered">
        <thead>
            <tr class="bg-light">
                <th scope="col">Date</th>
                <th scope="col">Start</th>
                <th scope="col">End</th>
                <th scope="col">Break</th>
                <th scope="col">Comment</th>
                <th scope="col">Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= $day_count; $i++) : ?>
                <?php
                // 表に入れるための変数初期化
                $start_time = '';
                $end_time = '';
                $break_time = '';
                $comment = '';

                // 対象日取得
                $list_date = date("Y-m-d", strtotime($selected_date . '-' . $i));

                if (isset($work_list[$list_date])) {
                    //対象日の配列データ取得
                    $work = $work_list[$list_date];

                    // 対象日の整形


                    // 時刻の表示フォーマット修正(秒を削除)

                    // start_time
                    if ($work['start_time']) {
                        $start_time = date('H:i', strtotime($work['start_time']));
                    }

                    // end_time
                    if ($work['end_time']) {
                        $end_time = date('H:i', strtotime($work['end_time']));
                    }

                    // break_time
                    if ($work['break_time']) {
                        $break_time = date('H:i', strtotime($work['break_time']));
                    }

                    // commentは一定文字数以上を省略
                    if ($work['comment']) {
                        $comment = mb_strimwidth($work['comment'], 0, 40, '...');
                    }
                }
                ?>
                <tr id="tr_<?= $list_date; ?>">
                    <!-- データ行 -->
                    <th scope="row"><?= time_format_mdw($list_date); ?></th>
                    <td><?= $start_time ?></td>
                    <td><?= $end_time ?></td>
                    <td><?= $break_time ?></td>
                    <td><?= escape($comment) ?></td>
                    <td><button type="button" class="btn h-auto py-0" style="width:40px" value="<?= $list_date; ?>" onclick="show_modal(this)"><i class="far fa-edit"></i></button></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</form>
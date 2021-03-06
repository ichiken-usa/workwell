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
        <?php for($i = 1; $i <= $day_count; $i++): ?>
            <?php
            // 表に入れるための変数初期化
                $start_time = '';
                $end_time = '';
                $break_time = '';
                $comment = '';
                $comment_title = '';
                
                // 対象日取得
                $list_date = date("Y-m-d", strtotime($selected_date.'-'.$i));
                
                if(isset($work_list[$list_date])){
                    //対象日の配列データ取得
                    $work = $work_list[$list_date];
                
                    // 時刻の表示フォーマット修正(秒を削除)

                    // start_time
                    if($work['start_time']){
                        $start_time = date('H:i', strtotime($work['start_time']));
                    }

                    // end_time
                    if($work['end_time']){
                        $end_time = date('H:i', strtotime($work['end_time']));
                    }

                    // break_time
                    if($work['break_time']){
                        $break_time = date('H:i', strtotime($work['break_time']));
                    }

                    if($work['comment']){
                        // 表示用に一定の長さは省略
                        $comment = mb_strimwidth($work['comment'],0,40,'...');
                        // マウス載せたら全文表示＆モーダルはこっちから取得
                        $comment_title = $work['comment'];
                    }
                }
            ?>
        <tr id="tr_<?= $list_date; ?>">
            <!-- データ行 -->
            <th scope="row"><?= time_format_mdw($list_date); ?></th>
            <td><?= $start_time ?></td>
            <td><?= $end_time ?></td>
            <td><?= $break_time ?></td>
            <td class="comment text-break" style="white-space: normal" title="<?= escape($comment_title) ?>"><?= escape($comment) ?></td>
            <td><button type="button" class="btn h-auto py-0" style="width:40px" value="<?= $list_date; ?>" onclick="show_modal(this)"><i class="far fa-edit"></i></button></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
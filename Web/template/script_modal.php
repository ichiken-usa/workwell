<script>
    // モーダル自動表示
    <?php if ($modal_view_flag) : ?>
        var inputModal = new bootstrap.Modal(document.getElementById('inputModal'))
        inputModal.toggle()
    <?php endif ?>

    // モーダル立ち上げボタン
    function show_modal(obj) {
        var inputModal = new bootstrap.Modal(document.getElementById('inputModal'))
        inputModal.toggle()

        // タグのオブジェクトからvalue取得
        var target_date = obj.value

        //対象日の表データを取得するためにtrタグに日付idを付与している。trの子要素を取得して次の要素の値取得
        var th = document.getElementById('tr_' + target_date).firstElementChild
        var day = th.innerText
        var start_time = th.nextElementSibling.innerText
        var end_time = th.nextElementSibling.nextElementSibling.innerText
        var break_time = th.nextElementSibling.nextElementSibling.nextElementSibling.innerText
        var comment = th.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling.innerText
        console.log(target_date)

        if (break_time == '') {
            break_time = '<?= $modal_break_ini; ?>'
        }

        // 取得したデータをモーダルへ入力
        document.getElementById("modal_date").innerText = day
        document.getElementById("modal_start_time").value = start_time
        document.getElementById("modal_end_time").value = end_time
        document.getElementById("modal_break_time").value = break_time
        document.getElementById("modal_comment").innerText = comment
        document.getElementById("modal_target_date").value = target_date

        // エラー表示をリセット
        document.getElementById("modal_date").classList.remove('is-invalid')
        document.getElementById("modal_start_time").classList.remove('is-invalid')
        document.getElementById("modal_end_time").classList.remove('is-invalid')
        document.getElementById("modal_break_time").classList.remove('is-invalid')
        document.getElementById("modal_comment").classList.remove('is-invalid')

    }

    // startのsetボタン
    document.getElementById("start_btn").onclick = function() {
        const now = new Date()
        const hour = now.getHours().toString().padStart(2, '0')
        const minute = now.getMinutes().toString().padStart(2, '0')
        document.getElementById("modal_start_time").value = hour + ':' + minute
    }

    // endのsetボタン
    document.getElementById("end_btn").onclick = function() {
        const now = new Date()
        const hour = now.getHours().toString().padStart(2, '0')
        const minute = now.getMinutes().toString().padStart(2, '0')
        document.getElementById("modal_end_time").value = hour + ':' + minute
    }
</script>

<!-- 月選択 -->
<select class="form-select rounded-pill m-2" name="m" onchange="submit(this.form)">
    <?php for ($i = 0; $i < 12; $i++) : ?>
        <?php $dropdown_date = strtotime("-{$i}months"); ?>
        <option value="<?= date('Y-m', $dropdown_date) ?>" <?php if ($selected_date == date('Y-m', $dropdown_date)) echo 'selected' ?>><?= date('Y/m', $dropdown_date) ?></option>
    <?php endfor; ?>
</select>
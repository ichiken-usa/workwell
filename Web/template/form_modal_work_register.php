<!-- Modal -->
<form method="POST">
    <input type="hidden" id="modal_target_date" name='modal_target_date' value="<?= $target_date ?>" />
    <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p></p>
                    <h5 class="modal-title" id="exampleModalLabel">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 入力日 -->
                    <div class="alert alert-primary text-center" role="alert">
                        <span id="modal_date" name="modal_date"><?= time_format_mdw($target_date) ?></span>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm">
                                <!-- 開始時間 -->
                                <div class="input-group">
                                    <input type="text" class="form-control <?php if (isset($err['modal_start_time'])) echo 'is-invalid'; ?>" id="modal_start_time" name="modal_start_time" value="<?= time_format_hm($modal_start_time) ?>" placeholder="Start">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" style="width:50px" id="start_btn">Set</button>
                                    </div>
                                    <div class="invalid-feedback"><?= $err['modal_start_time'] ?></div>
                                </div>
                            </div>
                            <div class="col-sm">
                                <!-- 終了時間 -->
                                <div class="input-group">
                                    <input type="text" class="form-control <?php if (isset($err['modal_end_time'])) echo 'is-invalid'; ?>" id="modal_end_time" name="modal_end_time" value="<?= time_format_hm($modal_end_time) ?>" placeholder="End">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" style="width:50px" id="end_btn">Set</button>
                                    </div>
                                    <div class="invalid-feedback"><?= $err['modal_end_time'] ?></div>
                                </div>
                            </div>
                            <!-- 休憩時間 -->
                            <div class="col-sm">
                                <div class="input-group">
                                    <input type="text" style="width:70px;" class="form-control <?php if (isset($err['modal_break_time'])) echo 'is-invalid'; ?>" id="modal_break_time" name="modal_break_time" value="<?= time_format_hm($modal_break_time) ?>" placeholder="Break">
                                </div>
                                <div class="invalid-feedback"><?= $err['modal_break_time'] ?></div>
                            </div>
                        </div>
                        <!-- コメント -->
                        <div class="form-group pt-3">
                            <textarea class="form-control <?php if (isset($err['modal_comment'])) echo 'is-invalid'; ?>" name="modal_comment" id="modal_comment" rows="5" placeholder="Comment"><?= $modal_comment ?></textarea>
                            <div class="invalid-feedback"><?= $err['modal_comment'] ?></div>
                        </div>

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
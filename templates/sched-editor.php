<form class="table form--edit-sched" action="sched-editor.php" method="POST">
        <input class="hide" type="number" name="sched_id" value=<?= $sched['sched_id'] ?? '' ?>>
        <div class="form-item">
            <label>Врач</label>
            <select name="doc_id" required>
                <?php foreach($docs as $cur_doc) : ?>
                    <option 
                        value="<?= $cur_doc['doc_id'] ?>" 
                        <?php if (isset($sched['doc_id']) == TRUE) : ?>
                            <?= $cur_doc['doc_id'] == $sched['doc_id'] ? 'selected=selected' : '' ?>
                        <?php endif ?>
                        ><?= $cur_doc['cab'] . ' ' . $cur_doc['snp'] . ' ' . $cur_doc['post'] ?></option>
                <? endforeach ?>
            </select>
            <label class="error-label"><?= $errors['doc_id'] ?? '' ?></label>
        </div>
        <?php $fl_mon_err = (isset($errors['mon']) || isset($errors['mon_end'])) == TRUE ? TRUE : FALSE ?>
        <div class="form-item <?= $fl_mon_err == TRUE ? 'error-item' : '' ?>">
            <label>Понедельник</label>
            <div class="table-col">
                <input type="time" name="mon" value="<?= $sched['mon'] ?? '' ?>">
                <input type="time" name="mon_end" value="<?= $sched['mon_end'] ?? '' ?>">
            </div>
        </div>
        <?php $fl_tue_err = (isset($errors['tue']) || isset($errors['tue_end'])) == TRUE ? TRUE : FALSE ?>
        <div class="form-item <?= $fl_tue_err == TRUE ? 'error-item' : '' ?>">
            <label>Вторник</label>
            <div class="table-col">
                <input type="time" name="tue" value="<?= $sched['tue'] ?? '' ?>">
                <input type="time" name="tue_end" value="<?= $sched['tue_end'] ?? '' ?>">
            </div>
        </div>
        <?php $fl_wed_err = (isset($errors['wed']) || isset($errors['wed_end'])) == TRUE ? TRUE : FALSE ?>
        <div class="form-item <?= $fl_wed_err == TRUE ? 'error-item' : '' ?>">
            <label>Среда</label>
            <div class="table-col">
                <input type="time" name="wed" value="<?= $sched['wed'] ?? '' ?>">
                <input type="time" name="wed_end" value="<?= $sched['wed_end'] ?? '' ?>">
            </div>
        </div>
        <?php $fl_thu_err = (isset($errors['thu']) || isset($errors['thu_end'])) == TRUE ? TRUE : FALSE ?>
        <div class="form-item <?= $fl_thu_err == TRUE ? 'error-item' : '' ?>">
            <label>Четверг</label>
            <div class="table-col">
                <input type="time" name="thu" value="<?= $sched['thu'] ?? '' ?>">
                <input type="time" name="thu_end" value="<?= $sched['thu_end'] ?? '' ?>">
            </div>
        </div>
        <?php $fl_fri_err = (isset($errors['fri']) || isset($errors['fri_end'])) == TRUE ? TRUE : FALSE ?>
        <div class="form-item <?= $fl_fri_err == TRUE ? 'error-item' : '' ?>">
            <label>Пятница</label>
            <div class="table-col">
                <input type="time" name="fri" value="<?= $sched['fri'] ?? '' ?>">
                <input type="time" name="fri_end" value="<?= $sched['fri_end'] ?? '' ?>">
            </div>
        </div>
        <div class="form-item">
            <label>Экран</label>
            <select name="screen_id" required>
                <?php foreach($screens as $cur_screen) : ?>
                    <option 
                        value="<?= $cur_screen['id'] ?>"
                        <?php 
                            if (isset($sched['screen_id']) == TRUE) {
                                if ($sched['screen_id'] == $cur_screen['id']) {
                                    print ('selected=selected');
                                }
                            }
                        ?>><?= $cur_screen['id'] ?></option>
                <?php endforeach ?>
            </select>
            <label class="error-label"><?= $errors['screen_id'] ?? '' ?></label>
        </div>
        <input class="hide" type="number" name="screen_position" value=<?= $sched['screen_position'] ?? '' ?>>
        <label class="error-label"><?= $errors['screen_position'] ?? '' ?></label>
        <div class="form-item">
            <input type="submit" value="Сохранить">
        </div>      
</form>

<?php if (isset($sched['sched_id']) == TRUE) : ?>
    <form action="delete.php" method="POST">
        <input class="hide" type="number" name="sched_id" value=<?= $sched['sched_id'] ?> required>
        <input class="hide" type="number" name="screen_id" value=<?= $sched['screen_id'] ?> required>
        <div class="form-item">
            <input class="button-delete" type="submit" value="Удалить">
        </div>
    </form>
<?php endif ?>
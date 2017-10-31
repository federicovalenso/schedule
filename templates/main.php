<a class="button" href="sched-editor.php?screen_id=<?= $cur_screen ?? '' ?>">Добавить</a>
<div class="table">
    <div class="table-caption">
        <a class="prev_button" href="?screen=<?= $prev_screen ?? '' ?>"><<</a>
        <span>Экран № <?= $cur_screen ?? '' ?></span>
        <a class="fwd_button" href="?screen=<?= $fwd_screen ?? '' ?>">>></a>
    </div>
    <div class="table-header">
        <div class="table-cell">Позиция</div>
        <div class="table-cell">Отображать</div>
        <div class="table-cell">Каб.</div>
        <div class="table-cell">ФИО</div>
        <div class="table-cell">Должность</div>
        <div class="table-cell">Пн.</div>
        <div class="table-cell">Вт.</div>
        <div class="table-cell">Ср.</div>
        <div class="table-cell">Чт.</div>
        <div class="table-cell">Пт.</div>
    </div>
    <?php foreach($scheds as $cur_sched) : ?>
        <div class="table-row schedule-row" data-label=<?= $cur_sched['sched_id'] ?>>
            <div class="table-cell"><?= $cur_sched['screen_position'] ?? '' ?></div>
            <div class="table-cell"><input type="checkbox" disabled="disabled" <?= $cur_sched['fl_display'] == 1 ? 'checked' : '' ?>></div>    
            <div class="table-cell cab"><?= $cur_sched['cab'] ?? '' ?></div>
            <div class="table-cell"><?= $cur_sched['snp'] ?? '' ?></div>
            <div class="table-cell"><?= $cur_sched['post'] ?? '' ?></div>
            <div class="table-cell">
                <div><?= $cur_sched['mon'] ?? '' ?></div>
                <div><?= $cur_sched['mon_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_sched['tue'] ?? '' ?></div>
                <div><?= $cur_sched['tue_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_sched['wed'] ?? '' ?></div>
                <div><?= $cur_sched['wed_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_sched['thu'] ?? '' ?></div>
                <div><?= $cur_sched['thu_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_sched['fri'] ?? '' ?></div>
                <div><?= $cur_sched['fri_end'] ?? '' ?></div>
            </div>
        </div>
    <? endforeach ?>
</div>
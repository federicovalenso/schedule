<div class="table">
    <div class="table-caption">
        <a class="prev_button" href="?screen=<?= $prev_screen ?? '' ?>"><<</a>
        <span>Экран № </span>
        <a class="fwd_button" href="?screen=<?= $fwd_screen ?? '' ?>">>></a>
    </div>
    <div class="table-header">
        <div class="table-cell">Каб.</div>
        <div class="table-cell">ФИО</div>
        <div class="table-cell">Должность</div>
        <div class="table-cell">Пн.</div>
        <div class="table-cell">Вт.</div>
        <div class="table-cell">Ср.</div>
        <div class="table-cell">Чт.</div>
        <div class="table-cell">Пт.</div>
    </div>
    <?php foreach($docs as $cur_doc) : ?>
        <div class="table-row schedule-row" data-label=<?= $cur_doc['doc_id'] ?>>
            <div class="table-cell cab"><?= $cur_doc['cab'] ?? '' ?></div>
            <div class="table-cell"><?= $cur_doc['snp'] ?? '' ?></div>
            <div class="table-cell"><?= $cur_doc['post'] ?? '' ?></div>
            <div class="table-cell">
                <div><?= $cur_doc['mon'] ?? '' ?></div>
                <div><?= $cur_doc['mon_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_doc['tue'] ?? '' ?></div>
                <div><?= $cur_doc['tue_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_doc['wed'] ?? '' ?></div>
                <div><?= $cur_doc['wed_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_doc['thu'] ?? '' ?></div>
                <div><?= $cur_doc['thu_end'] ?? '' ?></div>
            </div>
            <div class="table-cell">
                <div><?= $cur_doc['fri'] ?? '' ?></div>
                <div><?= $cur_doc['fri_end'] ?? '' ?></div>
            </div>
        </div>
    <? endforeach ?>
</div>
<div class="doc-data">
    <ul class="doc-data-header">
        <li>Кабинет</li>
        <li>ФИО</li>
        <li>Должность</li>
    </ul>
    <ul class="doc-data-row" data-label=<?= $doc['doc_id']?>>
        <li><?= $doc['cab'] ?? '' ?></li>    
        <li><?= $doc['snp'] ?? '' ?></li>
        <li><?= $doc['post'] ?? '' ?></li>
    </ul>
</div>
<form class="table form--edit-sched" action="editor.php" method="POST">
        <div>Понедельник</div>
        <div class="table-col">
            <input type="time" value="<?= $doc['mon' ?? '' ]?>">
            <input type="time" value="<?= $doc['mon_end' ?? '' ]?>">
        </div>
        <div>Вторник</div>
        <div class="table-col">
            <input type="time" value="<?= $doc['tue' ?? '' ]?>">
            <input type="time" value="<?= $doc['tue_end' ?? '' ]?>">
        </div>
        <div>Среда</div>
        <div class="table-col">
            <input type="time" value="<?= $doc['wed' ?? '' ]?>">
            <input type="time" value="<?= $doc['wed_end' ?? '' ]?>">
        </div>
        <div>Четверг</div>
        <div class="table-col">
            <input type="time" value="<?= $doc['thu' ?? '' ]?>">
            <input type="time" value="<?= $doc['thu_end' ?? '' ]?>">
        </div>
        <div>Пятница</div>
        <div class="table-col">
            <input type="time" value="<?= $doc['fri' ?? '' ]?>">
            <input type="time" value="<?= $doc['fri_end' ?? '' ]?>">
        </div>
</form>
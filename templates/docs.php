<a class="button" href="doc-editor.php">Добавить</a>
<div class="doc-data">
    <ul class="doc-data-header">
        <li>Кабинет</li>
        <li>ФИО</li>
        <li>Должность</li>
    </ul>
    <?php foreach($docs as $cur_doc) : ?>
        <ul class="doc-data-row" data-label=<?= $cur_doc['doc_id']?>>
            <li><?= $cur_doc['cab'] ?? '' ?></li>    
            <li><?= $cur_doc['snp'] ?? '' ?></li>
            <li><?= $cur_doc['post'] ?? '' ?></li>
        </ul>
    <?php endforeach ?>
</div>
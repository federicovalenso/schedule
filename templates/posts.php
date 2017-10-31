<a class="button" href="post-editor.php">Добавить</a>
<div class="table post-data">
    <div class="table-header">
        <div class="table-cell">Наименование</div>
    </div>
    <?php foreach($posts as $cur_post) : ?>
        <div class="table-row post-data-row" data-label=<?= $cur_post['id']?>>
            <div class="table-cell"><?= $cur_post['name'] ?? '' ?></div>
        </div>
    <?php endforeach ?>
</div>
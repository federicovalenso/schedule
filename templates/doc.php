<!-- data-label=<?= $doc['doc_id'] ?? '' ?> -->
<form class="table" action="doc.php" method="POST">
    <div class="form-item">
        <label>Кабинет</label>
        <input type="text" name="doc-cab" value="<?= $doc['cab'] ?? '' ?>">
    </div>
    <div class="form-item">
        <label>Фамилия</label>
        <input type="text" name="doc-surname" value="<?= $doc['surname'] ?? '' ?>">
    </div>
    <div class="form-item">
        <label>Имя</label>
        <input type="text" name="doc-name" value="<?= $doc['name'] ?? '' ?>">
    </div>
    <div class="form-item">
        <label>Отчество</label>
        <input type="text" name="doc-patronymic" value="<?= $doc['patronymic'] ?? '' ?>">
    </div>
    <div class="form-item">
        <label>Должность</label>
        <input type="text" name="doc-post" value="<?= $doc['post'] ?? '' ?>">
    </div>
</form>
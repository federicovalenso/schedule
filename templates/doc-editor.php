<!-- <?= var_dump($errors) ?>
<?= var_dump($doc) ?> -->
<form class="table" action="doc-editor.php" method="POST">
    <input class="hide" type="number" name="doc_id" value=<?= $doc['doc_id'] ?? '' ?>>
    <div class="form-item">
        <label>Кабинет</label>
        <input type="text" name="cab" value="<?= $doc['cab'] ?? '' ?>">
        <label class="error-label"><?= $errors['cab'] ?? '' ?></label>
    </div>
    <div class="form-item">
        <label>Фамилия</label>
        <input type="text" name="surname" value="<?= $doc['surname'] ?? '' ?>">
        <label class="error-label"><?= $errors['surname'] ?? '' ?></label>
    </div>
    <div class="form-item">
        <label>Имя</label>
        <input type="text" name="name" value="<?= $doc['name'] ?? '' ?>">
        <label class="error-label"><?= $errors['name'] ?? '' ?></label>
    </div>
    <div class="form-item">
        <label>Отчество</label>
        <input type="text" name="patronymic" value="<?= $doc['patronymic'] ?? '' ?>">
        <label class="error-label"><?= $errors['patronymic'] ?? '' ?></label>
    </div>
    <div class="form-item">
        <label>Должность</label>
        <select name="post_id" required>
            <?php foreach($posts as $cur_post) : ?>
                <option 
                    value="<?= $cur_post['id'] ?>" 
                    <?= $doc['post_id'] == $cur_post['id'] ? 'selected=selected' : '' ?>><?= $cur_post['name'] ?></option>
            <?php endforeach ?>
        </select>
        <label class="error-label"><?= $errors['post'] ?? '' ?></label>
    </div>
    <div class="form-item">
        <input type="submit" value="Сохранить">
    </div>    
</form>
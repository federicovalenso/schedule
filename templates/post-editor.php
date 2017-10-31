<form class="table" action="post-editor.php" method="POST">
    <input class="hide" type="number" name="post-id" value=<?= $post['id'] ?? '' ?>>
    <div class="form-item">
        <label>Наименование</label>
        <input type="text" name="post-name" value="<?= $post['name'] ?? '' ?>">
        <label class="error-label"><?= $errors['post-name'] ?? '' ?></label>
    </div>
    <div class="form-item">
        <input type="submit" value="Сохранить">
    </div>
</form>
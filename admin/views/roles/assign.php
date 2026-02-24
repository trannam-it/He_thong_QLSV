<h3>Gán quyền cho vai trò</h3>

<form method="post">
    <?php foreach ($permissions as $p): ?>
        <div>
            <label>
                <input type="checkbox" name="permissions[]" value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['code']) ?>
            </label>
        </div>
    <?php endforeach; ?>

    <button type="submit">Lưu quyền</button>
</form>

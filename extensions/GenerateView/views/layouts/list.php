<h2>Имя страницы(список): <?= $pageName ?></h2>
<!--'pageName', 'fillable', 'data'-->
<table border="3" style="border-collapse: collapse">
    <thead>
    <tr>
        <?php foreach ($fillable as $column): ?>
            <th><?= $column ?></th>
        <?php endforeach; ?>
        <th>Редактировать</th>
        <th>Удалить</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $items): ?>
        <tr>
            <?php foreach ($items as $col_name => $item): ?>
                <?php if (in_array($col_name, array_keys($fillable))): ?>
                    <td style="padding: 5px"><?= $item ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td>Редактировать</td>
            <td>Удалить</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
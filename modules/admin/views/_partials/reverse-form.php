<?php

declare(strict_types=1);

/** @var array<string, mixed> $action */

use yii\bootstrap5\Html;

?>
<?= Html::beginForm($action, 'post', ['class' => 'mt-4 p-3 border rounded']) ?>
<label class="form-label" for="reverse-reason">Причина сторно</label>
<input type="text" class="form-control mb-2" id="reverse-reason" name="reason" value="Сторно" maxlength="500">
<?= Html::submitButton('Сторнировать', ['class' => 'btn cc-btn', 'data-confirm' => 'Создать и провести сторно-документ?']) ?>
<?= Html::endForm() ?>
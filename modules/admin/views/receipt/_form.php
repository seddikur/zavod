<?php

declare(strict_types=1);

/** @var app\models\inventory\Receipt $model */
/** @var app\models\inventory\ReceiptItem[] $itemRows */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<?php $form = ActiveForm::begin(require Yii::getAlias('@app/modules/admin/views/_partials/cc-form-config.php')); ?>

<div class="row g-3">
    <div class="col-md-3"><?= $form->field($model, 'doc_number')->textInput() ?></div>
    <div class="col-md-3"><?= $form->field($model, 'doc_date')->input('date') ?></div>
    <div class="col-md-3"><?= $form->field($model, 'supplier_id')->dropDownList($suppliers, ['class' => 'form-select']) ?></div>
    <div class="col-md-3"><?= $form->field($model, 'warehouse_id')->dropDownList($warehouses, ['class' => 'form-select']) ?></div>
</div>

<h3 class="h6 mt-4">Строки (кг)</h3>
<table class="table table-sm">
    <thead><tr><th>Материал</th><th>Кол-во</th><th>Ед.</th><th>Цена/кг</th></tr></thead>
    <tbody>
    <?php foreach ($itemRows as $i => $row): ?>
        <tr>
            <td><?= Html::activeDropDownList($row, 'material_id', $materials, ['class' => 'form-select', 'name' => "ReceiptItem[$i][material_id]", 'prompt' => '']) ?></td>
            <td><?= Html::activeTextInput($row, 'quantity', ['class' => 'form-control', 'name' => "ReceiptItem[$i][quantity]", 'step' => '0.01', 'inputmode' => 'decimal']) ?></td>
            <td><?= Html::activeDropDownList($row, 'unit_id', $units, ['class' => 'form-select', 'name' => "ReceiptItem[$i][unit_id]"]) ?></td>
            <td><?= Html::activeTextInput($row, 'price', ['class' => 'form-control', 'name' => "ReceiptItem[$i][price]", 'step' => '0.01', 'inputmode' => 'decimal']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn cc-btn cc-btn-primary']) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>

<?php ActiveForm::end(); ?>
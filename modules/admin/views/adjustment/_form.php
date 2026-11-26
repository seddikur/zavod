<?php

declare(strict_types=1);

/** @var app\models\inventory\Adjustment $model */
/** @var app\models\inventory\AdjustmentItem[] $itemRows */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

?>
<?php $form = ActiveForm::begin(require Yii::getAlias('@app/modules/admin/views/_partials/cc-form-config.php')); ?>
<div class="row g-3">
    <div class="col-md-3"><?= $form->field($model, 'doc_number')->textInput() ?></div>
    <div class="col-md-3"><?= $form->field($model, 'doc_date')->input('date') ?></div>
    <div class="col-md-3"><?= $form->field($model, 'warehouse_id')->dropDownList($warehouses, ['class' => 'form-select']) ?></div>
    <div class="col-12"><?= $form->field($model, 'reason')->textarea(['rows' => 2]) ?></div>
</div>
<p class="text-muted small">Положительное количество — излишек, отрицательное — недостача (кг).</p>
<table class="table table-sm">
    <thead><tr><th>Материал</th><th>Кол-во, кг</th><th>Ед.</th></tr></thead>
    <tbody>
    <?php foreach ($itemRows as $i => $row): ?>
        <tr>
            <td><?= Html::activeDropDownList($row, 'material_id', $materials, ['class' => 'form-select', 'name' => "AdjustmentItem[$i][material_id]", 'prompt' => '']) ?></td>
            <td><?= Html::activeTextInput($row, 'quantity', ['class' => 'form-control', 'name' => "AdjustmentItem[$i][quantity]", 'step' => '0.01', 'inputmode' => 'decimal']) ?></td>
            <td><?= Html::activeDropDownList($row, 'unit_id', $units, ['class' => 'form-select', 'name' => "AdjustmentItem[$i][unit_id]"]) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="d-flex gap-2">
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn cc-btn cc-btn-primary']) ?>
    <?= Html::a('Отмена', ['index'], ['class' => 'btn cc-btn']) ?>
</div>
<?php ActiveForm::end(); ?>
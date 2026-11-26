<?php

declare(strict_types=1);

namespace app\services\inventory;

use Yii;
use yii\db\Exception;
use yii\db\Query;

/**
 * Сидер пресета «асфальт» (Основа.md этап 1).
 */
final class AsphaltPresetSeeder
{
    public function seed(bool $force = false): void
    {
        $db = Yii::$app->db;
        $exists = (new Query())->from('{{%material_categories}}')->exists($db);
        if ($exists && !$force) {
            echo "Пресет асфальта уже загружен (есть категории). Используйте --force для повторной загрузки.\n";
            return;
        }

        if ($exists && $force) {
            $this->clearReferenceData($db);
        }

        $transaction = $db->beginTransaction();
        try {
            $warehouseIds = $this->insertWarehouses($db);
            $unitIds = $this->insertUnits($db);
            $categoryIds = $this->insertCategories($db);
            $this->insertMaterials($db, $warehouseIds, $unitIds, $categoryIds);
            $this->insertPartners($db);
            $this->insertDemoRecipe($db, $unitIds, $warehouseIds);
            $transaction->commit();
            echo "Пресет «асфальт» успешно загружен.\n";
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @return array<string, int>
     */
    private function insertWarehouses(\yii\db\Connection $db): array
    {
        $map = [];
        foreach (
            [
                'gravel1' => 'Бункер щебня №1',
                'sand' => 'Бункер песка',
                'bitumen' => 'Битумохранилище',
                'additives' => 'Склад добавок',
            ] as $key => $name
        ) {
            $db->createCommand()->insert('{{%warehouses}}', [
                'name' => $name,
                'is_active' => 1,
            ])->execute();
            $map[$key] = (int) $db->getLastInsertID();
        }

        return $map;
    }

    /**
     * @return array<string, int>
     */
    private function insertUnits(\yii\db\Connection $db): array
    {
        $db->createCommand()->insert('{{%units}}', [
            'name' => 'Килограмм',
            'short_name' => 'кг',
            'base_unit_id' => null,
            'factor' => '1',
        ])->execute();
        $kgId = (int) $db->getLastInsertID();

        $db->createCommand()->insert('{{%units}}', [
            'name' => 'Тонна',
            'short_name' => 'т',
            'base_unit_id' => $kgId,
            'factor' => '1000',
        ])->execute();
        $tId = (int) $db->getLastInsertID();

        $db->createCommand()->insert('{{%units}}', [
            'name' => 'Кубический метр',
            'short_name' => 'м³',
            'base_unit_id' => $kgId,
            'factor' => '1',
        ])->execute();
        $m3Id = (int) $db->getLastInsertID();

        return ['kg' => $kgId, 't' => $tId, 'm3' => $m3Id];
    }

    /**
     * @return array<string, int>
     */
    private function insertCategories(\yii\db\Connection $db): array
    {
        $map = [];
        foreach (['Заполнители', 'Вяжущие', 'Добавки'] as $name) {
            $db->createCommand()->insert('{{%material_categories}}', ['name' => $name])->execute();
            $key = match ($name) {
                'Заполнители' => 'filler',
                'Вяжущие' => 'binder',
                default => 'additive',
            };
            $map[$key] = (int) $db->getLastInsertID();
        }

        return $map;
    }

    /**
     * @param array<string, int> $warehouseIds
     * @param array<string, int> $unitIds
     * @param array<string, int> $categoryIds
     */
    private function insertMaterials(
        \yii\db\Connection $db,
        array $warehouseIds,
        array $unitIds,
        array $categoryIds,
    ): void {
        $rows = [
            [
                'code' => 'GRAVEL-5-10',
                'name' => 'Щебень гранитный 5–10',
                'category_id' => $categoryIds['filler'],
                'default_warehouse_id' => $warehouseIds['gravel1'],
            ],
            [
                'code' => 'SAND-0-5',
                'name' => 'Песок 0–5',
                'category_id' => $categoryIds['filler'],
                'default_warehouse_id' => $warehouseIds['sand'],
            ],
            [
                'code' => 'BITUMEN-BND',
                'name' => 'Битум дорожный БНД',
                'category_id' => $categoryIds['binder'],
                'default_warehouse_id' => $warehouseIds['bitumen'],
            ],
            [
                'code' => 'MIN-POWDER',
                'name' => 'Минеральный порошок',
                'category_id' => $categoryIds['filler'],
                'default_warehouse_id' => $warehouseIds['sand'],
            ],
            [
                'code' => 'ADD-LT4',
                'name' => 'Противовыдельное добавление',
                'category_id' => $categoryIds['additive'],
                'default_warehouse_id' => $warehouseIds['additives'],
            ],
        ];

        foreach ($rows as $row) {
            $db->createCommand()->insert('{{%materials}}', [
                ...$row,
                'unit_id' => $unitIds['kg'],
                'is_active' => 1,
            ])->execute();
        }
    }

    private function insertDemoRecipe(\yii\db\Connection $db, array $unitIds, array $warehouseIds): void
    {
        $schema = $db->schema->getTableSchema('{{%recipes}}', true);
        if ($schema === null) {
            return;
        }

        if ((new Query())->from('{{%recipes}}')->where(['code' => 'SHCHMA-16'])->exists($db)) {
            return;
        }

        $codes = ['GRAVEL-5-10', 'SAND-0-5', 'BITUMEN-BND', 'MIN-POWDER'];
        $materialIds = [];
        foreach ($codes as $code) {
            $id = (new Query())->from('{{%materials}}')->select('id')->where(['code' => $code])->scalar($db);
            if ($id === false) {
                return;
            }
            $materialIds[$code] = (int) $id;
        }

        $db->createCommand()->insert('{{%recipes}}', [
            'code' => 'SHCHMA-16',
            'name' => 'ЩМА-16 (демо)',
            'output_quantity' => '1000',
            'output_unit_id' => $unitIds['kg'],
            'recipe_date' => date('Y-m-d'),
            'is_active' => 1,
        ])->execute();
        $recipeId = (int) $db->getLastInsertID();

        $lines = [
            ['GRAVEL-5-10', '514.91', $warehouseIds['gravel1']],
            ['SAND-0-5', '406.50', $warehouseIds['sand']],
            ['BITUMEN-BND', '29.81', $warehouseIds['bitumen']],
            ['MIN-POWDER', '48.78', $warehouseIds['sand']],
        ];
        foreach ($lines as [$code, $qty, $wh]) {
            $db->createCommand()->insert('{{%recipe_items}}', [
                'recipe_id' => $recipeId,
                'material_id' => $materialIds[$code],
                'warehouse_id' => $wh,
                'quantity' => $qty,
                'unit_id' => $unitIds['kg'],
            ])->execute();
        }
    }

    private function insertPartners(\yii\db\Connection $db): void
    {
        $db->createCommand()->insert('{{%suppliers}}', [
            'name' => 'ООО «КарьерПоставка»',
            'inn' => '7700000001',
            'is_active' => 1,
        ])->execute();

        $db->createCommand()->insert('{{%customers}}', [
            'name' => 'ООО «ДорСтрой»',
            'inn' => '7700000002',
            'is_active' => 1,
        ])->execute();
    }

    private function clearReferenceData(\yii\db\Connection $db): void
    {
        if ($db->schema->getTableSchema('{{%recipe_items}}', true) !== null) {
            $db->createCommand()->delete('{{%recipe_items}}')->execute();
            $db->createCommand()->delete('{{%recipes}}')->execute();
        }
        $db->createCommand()->delete('{{%materials}}')->execute();
        $db->createCommand()->delete('{{%material_categories}}')->execute();
        $db->createCommand()->delete('{{%units}}')->execute();
        $db->createCommand()->delete('{{%warehouses}}')->execute();
        $db->createCommand()->delete('{{%customers}}')->execute();
        $db->createCommand()->delete('{{%suppliers}}')->execute();
    }
}
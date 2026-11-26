<?php

declare(strict_types=1);

namespace app\services\inventory;

use app\models\inventory\Recipe;
use Yii;
use yii\db\Query;

/**
 * Версионирование рецептов: одна активная версия (is_active) на code.
 */
final class RecipeVersionService extends TransactionalService
{
    /**
     * @throws InventoryException
     */
    public function deactivateActiveVersions(string $code): void
    {
        Recipe::updateAll(
            ['is_active' => false],
            'code = :code AND is_active = 1',
            [':code' => $code],
        );
    }

    /**
     * @throws InventoryException
     */
    public function assertSingleActive(string $code, ?int $excludeId = null): void
    {
        $query = (new Query())
            ->from('{{%recipes}}')
            ->where(['code' => $code, 'is_active' => 1]);

        if ($excludeId !== null) {
            $query->andWhere(['not', ['id' => $excludeId]]);
        }

        if ($query->exists()) {
            throw new InventoryException(
                'Уже есть активная версия рецепта с кодом «' . $code . '». Создайте новую версию или деактивируйте текущую.',
            );
        }
    }

    /**
     * @param array<int, array{material_id:int, quantity:string, unit_id:int, warehouse_id?:int|null}> $items
     * @throws InventoryException
     */
    public function createVersion(array $header, array $items): Recipe
    {
        return $this->transaction(function () use ($header, $items): Recipe {
            $code = (string) $header['code'];
            $this->assertSingleActive($code);
            unset($header['id']);
            $header['is_active'] = $header['is_active'] ?? true;

            $recipe = new Recipe($header);
            if (!$recipe->save()) {
                throw new InventoryException('Не удалось создать рецепт: ' . json_encode($recipe->errors, JSON_UNESCAPED_UNICODE));
            }

            foreach ($items as $item) {
                $recipeItem = new \app\models\inventory\RecipeItem([
                    'recipe_id' => $recipe->id,
                    'material_id' => $item['material_id'],
                    'warehouse_id' => $item['warehouse_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                ]);
                if (!$recipeItem->save()) {
                    throw new InventoryException('Не удалось сохранить строку рецепта.');
                }
            }

            return $recipe;
        });
    }

    /**
     * @throws InventoryException
     */
    public function publishNewVersion(int $currentRecipeId, array $header, array $items): Recipe
    {
        return $this->transaction(function () use ($currentRecipeId, $header, $items): Recipe {
            $current = Recipe::findOne($currentRecipeId);
            if ($current === null) {
                throw new InventoryException('Рецепт не найден.');
            }

            $this->deactivateActiveVersions((string) $current->code);

            $header['code'] = $current->code;
            $header['is_active'] = true;

            return $this->createVersion($header, $items);
        });
    }
}
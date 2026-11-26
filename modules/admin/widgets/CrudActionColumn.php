<?php

declare(strict_types=1);

namespace app\modules\admin\widgets;

use yii\grid\ActionColumn;
use yii\helpers\Html;

/**
 * Колонка CRUD-действий в стиле Clean Card:
 * иконки view/edit/delete + inline-подтверждение удаления.
 */
class CrudActionColumn extends ActionColumn
{
    /**
     * Какие кнопки показывать.
     *
     * @var list<string>
     */
    public array $actions = ['view', 'update', 'delete'];

    /**
     * @var array<string, string> Material Symbols icon name
     * Без type-hint: в yii\grid\ActionColumn::$icons тип не объявлен (PHP 8).
     */
    public $icons = [
        'view' => 'search',
        'update' => 'edit',
        'delete' => 'delete',
    ];

    /**
     * @var array<string, string>
     */
    public array $titles = [
        'view' => 'Просмотр',
        'update' => 'Изменить',
        'delete' => 'Удалить',
    ];

    public $header = '';

    public function init(): void
    {
        $this->template = '{buttons}';
        parent::init();

        $this->contentOptions = array_merge(
            ['class' => 'cc-crud-actions-cell'],
            is_array($this->contentOptions) ? $this->contentOptions : [],
        );

        if (!isset($this->headerOptions['style'])) {
            $this->headerOptions['style'] = 'width: 88px';
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return $this->renderActions($model, $key, (int) $index);
    }

    protected function renderActions(mixed $model, mixed $key, int $index): string
    {
        $main = [];
        $deleteUrl = null;

        foreach ($this->actions as $name) {
            if ($name === 'delete') {
                continue;
            }

            if (!$this->isActionVisible($name, $model, $key, $index)) {
                continue;
            }

            $actionUrl = $this->createUrl($name, $model, $key, $index);
            $icon = $this->icons[$name] ?? $name;
            $title = $this->titles[$name] ?? $name;
            $mod = $name === 'update' ? 'edit' : $name;

            $main[] = Html::a(
                Html::tag('i', $icon, ['class' => 'material-symbols-rounded', 'aria-hidden' => 'true']),
                $actionUrl,
                [
                    'class' => 'cc-act cc-act-' . $mod,
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ],
            );
        }

        if (in_array('delete', $this->actions, true)
            && $this->isActionVisible('delete', $model, $key, $index)
        ) {
            $deleteUrl = $this->createUrl('delete', $model, $key, $index);
            $title = $this->titles['delete'];
            $main[] = Html::button(
                Html::tag('i', $this->icons['delete'], [
                    'class' => 'material-symbols-rounded',
                    'aria-hidden' => 'true',
                ]),
                [
                    'type' => 'button',
                    'class' => 'cc-act cc-act-delete',
                    'title' => $title,
                    'aria-label' => $title,
                    'data-cc-delete-open' => '1',
                ],
            );
        }

        $confirm = '';
        if ($deleteUrl !== null) {
            $confirm = Html::tag(
                'div',
                Html::beginForm($deleteUrl, 'post', [
                    'class' => 'cc-crud-actions__delete-form',
                    'data-pjax' => '0',
                ])
                . Html::submitButton('Удалить', [
                    'class' => 'cc-act-confirm-delete',
                ])
                . Html::endForm()
                . Html::button(
                    Html::tag('i', 'close', [
                        'class' => 'material-symbols-rounded',
                        'aria-hidden' => 'true',
                    ]),
                    [
                        'type' => 'button',
                        'class' => 'cc-act-cancel',
                        'title' => 'Отмена',
                        'aria-label' => 'Отмена',
                        'data-cc-delete-cancel' => '1',
                    ],
                ),
                ['class' => 'cc-crud-actions__confirm'],
            );
        }

        return Html::tag(
            'div',
            Html::tag('div', implode('', $main), ['class' => 'cc-crud-actions__main'])
            . $confirm,
            [
                'class' => 'cc-crud-actions',
                'data-cc-crud-actions' => '1',
            ],
        );
    }

    protected function isActionVisible(string $name, mixed $model, mixed $key, int $index): bool
    {
        if (!isset($this->visibleButtons[$name])) {
            return true;
        }

        $visible = $this->visibleButtons[$name];
        if (is_bool($visible)) {
            return $visible;
        }

        return (bool) call_user_func($visible, $model, $key, $index);
    }
}

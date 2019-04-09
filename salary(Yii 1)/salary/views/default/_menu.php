<?
$aSubMenu = [
    [
        'class' => 'green', 'link' => 'salary/default/reportCreate',
        'title' => 'Новый расчет', 'icon' => 'ti-plus', 'visible' => $this->checkAccess('create')
    ],
    [
        'link' => 'salary/default/index',
        'title' => 'Расчеты', 'icon' => 'ti-wallet',
    ],
    [
        'link' => 'salary/default/settings',
        'title' => 'Настройка ЗП', 'icon' => 'ti-settings'
    ],
    [
        'link' => 'salary/default/trash', 'class' => '',
        'title' => 'Корзина ' . SalaryReport::model()->getTrashCount(), 'icon' => 'ti-trash', 'visible' => $this->checkAccess('update'),
    ],


    [
        'link' => 'salary/default/hours',
        'title' => 'Выгрузка по часам', 'icon' => 'ti-timer',
        'class' => 'pull-right'
    ],
    [
        'link' => 'salary/default/services',
        'title' => 'Выгрузка по услугам', 'icon' => 'ti-list',
        'class' => 'pull-right'
    ],
];
$this->submenu($aSubMenu);

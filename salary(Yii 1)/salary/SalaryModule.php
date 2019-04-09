<?php

class SalaryModule extends Module
{
    public $commentsModels = ['SalaryReport'];
    public $filesModels = [];

    /**
     *
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return array|void
     */
    public function additionalLevels()
    {
        return [
            'manage' => 'Управление расчетами'
        ];
    }

}
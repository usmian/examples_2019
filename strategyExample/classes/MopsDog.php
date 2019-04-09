<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 19.10.2017
 *
 */
spl_autoload_register(function ($class_name) {
    include  $class_name . '.php';
});

class MopsDog extends Dog
{
    private $specificSound;
    const HRR = 'hrrrr';

    public function getSpecificSound()
    {
        $this->speсificSound = self::HRR;
        return $this->specificSound;
    }
}
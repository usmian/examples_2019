<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 19.10.2017
 *
 */
namespace Dogs\classes;

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
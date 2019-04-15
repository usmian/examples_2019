<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 19.10.2017
 *
 */
namespace Dogs\classes;

use Dogs\contracts\WoofBehaviorInterface;
use Dogs\contracts\HuntingInterface;

abstract class Dog
{
    protected $sound;
    protected $hunt;

    public function makeSound(WoofBehaviorInterface $sound){
        return $sound->woof();
    }

    public function makeHunt(HuntingInterface $hunting){
        return $hunting->hunt();
    }

    /**
     * @return mixed
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * @param mixed $hunt
     */
    public function setHunt($hunt)
    {
        $this->hunt = $hunt;
    }

    /**
     * @param mixed $sound
     */
    public function setSound($sound)
    {
        $this->sound = $sound;
    }

    /**
     * @return mixed
     */
    public function getHunt()
    {
        return $this->hunt;
    }
}
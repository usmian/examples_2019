<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 19.10.2017
 *
 */
namespace Dogs\classes;

class NotHuntingBehavior implements HuntingInterface
{
    public function hunt()
    {
        return 'i can\'t hunt';
    }
}
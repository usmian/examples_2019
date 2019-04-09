<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 19.10.2017
 *
 */
class SilentWoofBehavior implements WoofBehaviorInterface
{
    public function woof()
    {
        return 'i can\'t make sounds';
    }
}
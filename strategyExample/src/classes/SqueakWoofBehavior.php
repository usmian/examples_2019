<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 19.10.2017
 *
 */
namespace Dogs\classes;

class SqueakWoofBehavior implements WoofBehaviorInterface
{
    public function woof()
    {
        return 'squeak';
    }
}
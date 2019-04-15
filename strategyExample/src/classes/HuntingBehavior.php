<?php
namespace Dogs\classes;

class HuntingBehavior implements HuntingInterface
{
    public function hunt()
    {
        return 'seek and hunt';
    }
}
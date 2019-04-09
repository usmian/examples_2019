<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 13.02.2019
 * Time: 14:20
 */

namespace UdsGame\Contracts;


interface Request
{
    public function send($action, $method, $data=null);
}
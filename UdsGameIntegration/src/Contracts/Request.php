<?php

namespace UdsGame\Contracts;


interface Request
{
    public function send($action, $method, $data=null);
}
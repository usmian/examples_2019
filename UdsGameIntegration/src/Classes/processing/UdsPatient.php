<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 13.02.2019
 * Time: 18:14
 */

namespace UdsGame\Classes\processing;


use UdsGame\Contracts\Patient as IPatient;

class UdsPatient implements IPatient
{

    private $patientInfo;

    public function __construct($patientInfo)
    {
        $this->patientInfo = $patientInfo;
    }

    public static function getInstance($patientInfo)
    {
        return new static($patientInfo);
    }


}
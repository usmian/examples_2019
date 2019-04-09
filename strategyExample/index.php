<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8"/>
    <title>Песики</title>
</head>
<body>

<?php
//Strategies
if (isset($_GET['command']) && isset($_GET['type'])) {
    //get parameters
    $command = trim(strtolower($_GET['command']));
    $type = trim(strtolower($_GET['type']));
    //------------create object dog---------------------//
    //with necessary strategy for every dogs type
    $dog = DogFactory::create($type, $command);
    //displays necessary
    if ($command == 'hunt') {
        echo $dog->getHunt();
        echo '<br>';
    }
    if ($command == 'sound') {
        echo $dog->getSound();
        echo '<br>';
    }
} else {
    exit('please specify the command');
};

?>
</body>
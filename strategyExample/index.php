<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8"/>
    <link href="static/style.css" rel="stylesheet">
    <title>Песики</title>
</head>
<body>
<form action="/index.php">
    <input type="text" name="type" placeholder="type">
    <input type="text" name="command" placeholder="command">
    <input type="submit" value="check">
</form>
</body>

<?
require_once 'vendor/autoload.php';
use Dogs\classes\DogFactory;

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
<script src="static/js/jquery-3.2.1.min.js" type="text/javascript"></script>
<script src="static/js/main.js" type="text/javascript"></script>

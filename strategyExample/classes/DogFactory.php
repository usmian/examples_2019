<?php

/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 19.10.2017
 *
 */
class DogFactory
{
    public static function create($type, $command)
    {
        switch ($type) {
            case 'mops':

                $dog = new MopsDog();
                if ($command == 'hunt') {
                    //use necessary strategy
                    $hunt = $dog->makeHunt(new NotHuntingBehavior());
                    $dog->setHunt($hunt);
                }

                if ($command == 'sound') {
                    $sound = $dog->makeSound(new WoofBehavior());
                    $dog->setSound($sound);
                }

                return $dog;
                break;
            case 'labrador':
                $dog = new PlushLabradorDog();
                if ($command == 'hunt') {
                    $hunt = $dog->makeHunt(new NotHuntingBehavior());
                    $dog->setHunt($hunt);
                }

                if ($command == 'sound') {
                    $sound = $dog->makeSound(new SilentWoofBehavior());
                    $dog->setSound($sound);
                }

                return $dog;
                break;
            case 'ribbed':
                $dog = new RibbedDog();
                if ($command == 'hunt') {
                    $hunt = $dog->makeHunt(new NotHuntingBehavior());
                    $dog->setHunt($hunt);
                }

                if ($command == 'sound') {
                    $sound = $dog->makeSound(new SqueakWoofBehavior());
                    $dog->setSound($sound);
                }

                return $dog;
                break;
            case'siba-inu':
                $dog = new SibaInuDog();
                if ($command == 'hunt') {
                    $hunt = $dog->makeHunt(new HuntingBehavior());
                    $dog->setHunt($hunt);
                }

                if ($command == 'sound') {
                    $sound = $dog->makeSound(new WoofBehavior());
                    $dog->setSound($sound);
                }

                return $dog;
                break;
            case 'dachshund':

                $dog = new DachshunDog();
                if ($command == 'hunt') {
                    $hunt = $dog->makeHunt(new HuntingBehavior());
                    $dog->setHunt($hunt);
                }

                if ($command == 'sound') {
                    $sound = $dog->makeSound(new WoofBehavior());
                    $dog->setSound($sound);
                }
                break;
        }
    }
}
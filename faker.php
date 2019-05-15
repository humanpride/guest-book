<?php

require_once 'vendor/fzaninotto/faker/src/autoload.php';
require_once 'model.php';

$faker = Faker\Factory::create();

$config = parse_ini_file("config.ini");

$dbconnect = new DbConnect($config['dbHost'], $config['dbUser'], $config['dbPass'], $config['dbName'], $config['charset']);

for ($i=0; $i < $config['fakerAddRows']; $i++) { 
    $fakerTags = explode(' ', $faker->words(4, true));
    $fakerDateTime = $faker->dateTimeThisYear('now', 'Europe/Moscow')->format('Y-m-d H:i:s');
    $dbconnect->save($faker->userName, $faker->email, $faker->url, $faker->text, $fakerTags, $fakerDateTime);
}

$dbconnect->close();

echo 'Данные созданы';

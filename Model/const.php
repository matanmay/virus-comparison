<?php
require 'vendor/autoload.php';
require '../Controller/dbcontroller.php';

$client = new \MongoDB\Client(getMongoUrl());
$db = $client->malwereDB;
$collection = $db->malweres;
define($GETDB, $collection);

?>
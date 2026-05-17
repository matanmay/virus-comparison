<?php
session_start();

$md5_to_remove = $_GET['md5'];

// search for the index of the value in the array
$index = array_search($md5_to_remove, $_SESSION['allMD5ValuesToCompare']);

// if the value is found in the array, remove it
if ($index !== false) {
    array_splice($_SESSION['allMD5ValuesToCompare'], $index, 1);
    echo "MD5 removed from session: " . $md5_to_remove;
} 
?>

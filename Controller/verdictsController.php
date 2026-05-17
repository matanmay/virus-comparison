<?php
use function PHPUnit\Framework\once;

require_once 'dbcontroller.php';

// This method gets MD5 and return the malwere with the verdicts the user chose to search

function getSameVerdicts($verdictsToSearch)
{
    // Connecting to DB
    $client = new \MongoDB\Client(getMongoUrl());
    $db = $client->malwereDB;
    $collection = $db->malweres;

    // Convert the comma-separated string to an array
    $verdictsToSearch = explode(',', $verdictsToSearch);

    // Trim white spaces from each element of the array
    $verdictsToSearch = array_map('trim', $verdictsToSearch);

    // Compare the values between the db and the verdicts the user chose
    $allWithSameVerdicts = $collection->aggregate([
        [
            '$addFields' => [
                'verdictsArray' => ['$split' => ['$verdicts', ',']]
            ]
        ],
        [
            '$addFields' => [
                'verdictsArray' => [
                    '$map' => [
                        'input' => '$verdictsArray',
                        'in' => ['$trim' => ['input' => '$$this']]
                    ]
                ]
            ]
        ],
        [
            '$match' => [
                'verdictsArray' => ['$all' => $verdictsToSearch]
            ]
        ]
    ]);


    // Getting array of md5
    $result = [];
    foreach ($allWithSameVerdicts as $doc) {
        $result[] = $doc['md5'];
    }

    return $result;
}
?>
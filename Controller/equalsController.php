<?php

#This function get JsonArray of some malwares and field that want to compare and return a json array of the equals in this field
/*function getFieldValueCounts($array, $fieldName) {
    $counts = array();
    foreach ($array as $obj) {
        $fieldValue = isset($obj[$fieldName]) ? $obj[$fieldName] : 'null';
        if (!isset($counts[$fieldValue])) {
            $counts[$fieldValue] = 1;
        } else {
            $counts[$fieldValue]++;
        }
    }
    return $counts;
}*/
function getFieldValueCounts($array, $fieldName) {
    $counts = array();
    $equal = true; // Flag to track equality of field values
    $firstValue = null; // Variable to store the first field value

    foreach ($array as $index => $obj) {
        $fieldValue = isset($obj[$fieldName]) ? $obj[$fieldName] : 'null';

        // If it's the first object, set the first field value and continue to the next object
        if ($index === 0) {
            $firstValue = $fieldValue;
            continue;
        }

        // If the current field value is not equal to the first field value, set the equality flag to false and break
        if ($fieldValue !== $firstValue) {
            $equal = false;
            break;
        }
    }

    // If all field values are equal, set the count as 1 for the first field value, and mark it as equal
    if ($equal) {
        $counts[$firstValue] = array(
            'count' => 1,
            'marked' => true
        );
    } else {
        // If not all field values are equal, use the original logic to count occurrences
        foreach ($array as $index => $obj) {
            $fieldValue = isset($obj[$fieldName]) ? $obj[$fieldName] : 'null';
            if (!isset($counts[$fieldValue])) {
                $counts[$fieldValue] = array(
                    'count' => 1,
                    'marked' => false
                );
            } else {
                $counts[$fieldValue]['count']++;
            }
        }
    }

    return $counts;
}






#This function get a JsonArray of some malwares and return Json of the equals things.
function getJsonOfEquals($arrayJson)
{
    $myArray = array(); #The JSON Array
    $md5Counts = getFieldValueCounts($arrayJson, 'MD5');
    $myArray['MD5'] = $md5Counts;
    $namesCounts = getFieldValueCounts($arrayJson, 'names');
    $myArray['names'] = $namesCounts;
    $totalVotesCounts = getFieldValueCounts($arrayJson, 'total_votes');
    $myArray['total_votes'] = $totalVotesCounts;
    $fileSizeCounts = getFieldValueCounts($arrayJson, 'file_size');
    $myArray['file_size'] = $fileSizeCounts;
    $meaningfulNameCounts = getFieldValueCounts($arrayJson, 'meaningful_name');
    $myArray['meaningful_name'] = $meaningfulNameCounts;
    $fileExtensionCounts = getFieldValueCounts($arrayJson, 'file_extension');
    $myArray['file_extension'] = $fileExtensionCounts;
    $libraryNameCounts = getFieldValueCounts($arrayJson, 'library_name');
    $myArray['library_name'] = $libraryNameCounts;
    $processesTerminatedCounts = getFieldValueCounts($arrayJson, 'processes_terminated');
    $myArray['processes_terminated'] = $processesTerminatedCounts;
    $processesCreatedCounts = getFieldValueCounts($arrayJson, 'processes_created');
    $myArray['processes_created'] = $processesCreatedCounts;
    $tagsCounts = getFieldValueCounts($arrayJson, 'tags');
    $myArray['tags'] = $tagsCounts;
    $verdictsCounts = getFieldValueCounts($arrayJson, 'verdicts');
    $myArray['verdicts'] = $verdictsCounts;
    return json_encode($myArray);
}

// functions for show_similarities (rectangles) screen:

// returns a value for a specified object and key
function getSubValuesByKey($obj, $keyName) {
    $subValues = array();
    if(isset($obj->{$keyName})) {
        $value = $obj->{$keyName};
        if(is_array($value)) {
            foreach($value as $subValue) {
                if(is_object($subValue)) {
                    $subValues = array_merge($subValues, get_object_vars($subValue));
                }
                else {
                    $subValues[] = $subValue;
                }
            }
        }
        else if(is_object($value)) {
            $subValues = get_object_vars($value);
        }
        else if(is_string($value)) {
            $subValues = explode(',', $value);
        }
        else {
            $subValues[] = $value;
        }
    }
    return $subValues;
}

// returns all values of a specified object
function getObjectKeyValuePairs($obj) {
    $keyValuePairs = array();
    foreach($obj as $key => $value) {
        if($key === '_id' || $key === 'md5' || $key === 'file_extension' || $key === 'file_size') {
            continue; 
        }
        $subValues = getSubValuesByKey($obj, $key);
        if(count($subValues) > 1 || (count($subValues) == 1 && !empty($subValues[0]))) {
            $keyValuePairs[$key] = $subValues;
        }
    }
    return $keyValuePairs;
}

// takes an array of objects, and returns an array of common values of each key
function getCommonKeyValuePairs($objects) {
    // Get key-value pairs for first object in array
    $commonPairs = getObjectKeyValuePairs($objects[0]);
    $objectsWithoutTheFirstMd5 = array_slice($objects, 1);

    // Compare key-value pairs of all objects in array
    foreach($objectsWithoutTheFirstMd5 as $object) {
        $pairs = getObjectKeyValuePairs($object);
        foreach($commonPairs as $key => $values) {
            if(array_key_exists($key, $pairs)) {
                
                // trim whitespace from all the values
                $newValues = array();
                $newPairs = array();
                foreach($values as $string){
                    array_push($newValues, trim($string));
                }
                foreach($pairs[$key] as $string){
                    array_push($newPairs, trim($string));
                }

                $commonValues = array_intersect($newValues, $newPairs);
                if(!empty($commonValues)) {
                    $commonPairs[$key] = $commonValues;
                } else {
                    unset($commonPairs[$key]);
                }
            } else {
                unset($commonPairs[$key]);
            }
        }

        foreach ($commonPairs as $key => $values) {
            $commonPairs[$key] = array_values($values);
        }
    }
    return $commonPairs;
}

// change to format of the common object, so that it could later be encoded to a json string
function getCommonObject($objects) {
    $commonPairs = getCommonKeyValuePairs($objects);
    $commonObj = new stdClass();

    foreach ($commonPairs as $key => $values) {
        $key = ucwords(str_replace('_', ' ', $key));
        
        if (is_array($values)) {
            $values = array_map(function($value) {
                // Remove non-printable characters from string
                return preg_replace('/[^\P{C}\n]+/u', '', $value);
            }, $values);
        }

        if (count($values) == 1) {
            $commonObj->$key = isset($values[0]) ? $values[0] : null;
        } else {
            $commonObj->$key = array_values($values); 
        }
    }
    return $commonObj;
}








   













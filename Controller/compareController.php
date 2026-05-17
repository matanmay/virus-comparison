<?php
#This function gets two json Arrays and return the equals
function compareEquals($array1, $array2) {
    $equals = array();
    foreach ($array1 as $key => $value) {
        if (array_key_exists($key, $array2)) {
            $second_value = $array2[$key];
            if (is_array($value) && is_array($second_value)) {
                $equals[$key] = compareEquals($value, $second_value);
            } else {
                if ($value == $second_value) {
                    $equals[$key] = $value;
                }
            }
        }
    }
    return $equals;
}
#This function gets two json Arrays and return the diffrents
function compareDiffrents($array1, $array2) {
    $differences = array();
    foreach ($array1 as $key => $value) {
        if (!array_key_exists($key, $array2)) {
            $differences[$key] = array(
                'first' => $value,
                'second' => null
            );
        } else {
            $second_value = $array2[$key];
            if (is_array($value) && is_array($second_value)) {
                $differences[$key] = compareDiffrents($value, $second_value);
            } else {
                if ($value != $second_value) {
                    $differences[$key] = array(
                        'first' => $value,
                        'second' => $second_value
                    );
                }
            }
        }
    }
    foreach ($array2 as $key => $value) {
        if (!array_key_exists($key, $array1)) {
            $differences[$key] = array(
                'first' => null,
                'second' => $value
            );
        }
    }
    return $differences;
}


?>
<?php

function recursive_utf8_encode($input){
    if (is_array($input)) {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = recursive_utf8_encode($value);
            } elseif (is_string($value)) {
                $value = utf8_encode($value);
            }
        }
        unset($value);
    } elseif (is_string($input)) {
        $input = utf8_encode($input);
    }
    return $input;
}

function devolver_respuesta($respuesta) {
    //$respuesta = recursive_utf8_encode($respuesta);
    $json = json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    header('Content-Type: application/json');
    echo $json;
}

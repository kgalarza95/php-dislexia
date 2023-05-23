<?php

require_once 'util/log.php';

$clave = "ugdisle";
$patron = 'nopqrstuvwxyzabcdefghijklm';
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));

$reemplazos = array(
    "a" => "z",
    "A" => "Z",
    "b" => "y",
    "B" => "Y",
    "c" => "x",
    "C" => "X",
    "d" => "w",
    "D" => "W",
    "e" => "v",
    "E" => "V",
    "f" => "u",
    "F" => "U",
    "g" => "t",
    "G" => "T",
    "h" => "s",
    "H" => "S",
    "i" => "r",
    "I" => "R",
    "j" => "q",
    "J" => "Q",
    "k" => "p",
    "K" => "P",
    "l" => "o",
    "L" => "O",
    "m" => "n",
    "M" => "N",
    "n" => "m",
    "N" => "M",
    "o" => "l",
    "O" => "L",
    "p" => "k",
    "P" => "K",
    "q" => "j",
    "Q" => "J",
    "r" => "i",
    "R" => "I",
    "s" => "h",
    "S" => "H",
    "t" => "g",
    "T" => "G",
    "u" => "f",
    "U" => "F",
    "v" => "e",
    "V" => "E",
    "w" => "d",
    "W" => "D",
    "x" => "c",
    "X" => "C",
    "y" => "b",
    "Y" => "B",
    "z" => "a",
    "Z" => "A",
    "0" => "9",
    "1" => "8",
    "2" => "7",
    "3" => "6",
    "4" => "5",
    "5" => "4",
    "6" => "3",
    "7" => "2",
    "8" => "1",
    "9" => "0"
);


function recursive_utf8_encode($input)
{
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

function devolver_respuesta($respuesta)
{
    //$respuesta = recursive_utf8_encode($respuesta);
    $json = json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    header('Content-Type: application/json');
    echo $json;
    writeLog("Respuesta: " .  $json);
}


function encrypt($text, $pattern)
{
    $result = '';
    for ($i = 0, $len = strlen($text); $i < $len; $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $char = strtolower($char);
            $new_char = $pattern[ord($char) - ord('a')];
            if (ctype_upper($text[$i])) {
                $new_char = strtoupper($new_char);
            }
            $result .= $new_char;
        } else {
            $result .= $char;
        }
    }
    return $result;
}

function decrypt($text, $pattern)
{
    $result = '';
    for ($i = 0, $len = strlen($text); $i < $len; $i++) {
        $char = $text[$i];
        if (ctype_alpha($char)) {
            $char = strtolower($char);
            $new_char = chr(strpos($pattern, $char) + ord('a'));
            if (ctype_upper($text[$i])) {
                $new_char = strtoupper($new_char);
            }
            $result .= $new_char;
        } else {
            $result .= $char;
        }
    }
    return $result;
}


function encriptar($palabra, $reemplazos)
{
    /*
    Cambia las letras en una palabra según un array de reemplazos.
    
    Parameters:
    $palabra (string): la palabra original.
    $reemplazos (array): un array que contiene las letras a reemplazar y sus correspondientes reemplazos.
    
    Returns:
    string: la palabra con las letras reemplazadas.
    */
    $nueva_palabra = "";
    for ($i = 0; $i < strlen($palabra); $i++) {
        $letra = $palabra[$i];
        if (isset($reemplazos[$letra])) {
            $nueva_palabra .= $reemplazos[$letra];
        } else {
            $nueva_palabra .= $letra;
        }
    }
    return $nueva_palabra;
}

function desencriptar($palabra, $reemplazos)
{
    /*
    Revierte los cambios de las letras en una palabra según un array de reemplazos.
    
    Parameters:
    $palabra (string): la palabra con las letras reemplazadas.
    $reemplazos (array): un array que contiene las letras originales y sus correspondientes reemplazos.
    
    Returns:
    string: la palabra en su estado original.
    */
    $nueva_palabra = "";
    for ($i = 0; $i < strlen($palabra); $i++) {
        $letra = $palabra[$i];
        $encontrado = false;
        foreach ($reemplazos as $original => $reemplazo) {
            if ($letra === $reemplazo) {
                $nueva_palabra .= $original;
                $encontrado = true;
                break;
            }
        }
        if (!$encontrado) {
            $nueva_palabra .= $letra;
        }
    }
    return $nueva_palabra;
}

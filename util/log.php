<?php

/*  function writeLog($message, $isError = false) {
    $ruta_absoluta = __DIR__;
    $logFile = $ruta_absoluta."/log/log.txt";
    $timestamp = date("Y-m-d H:i:s");

    // Construir el formato del registro
    $logEntry = "[" . $timestamp . "] " . ($isError ? "[ERROR] " : "[OK] ") . $message . "\n";

    echo "ruta ". $logFile.  "\n";
    // Escribir el registro en el archivo de log
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}  */

 function writeLog($message, $isError = false)
{
    //echo "escribir en archivo \n";
    try {
        //echo " $message: " .$message." $message \n" ;
        $ruta_absoluta = __DIR__;
        $logDirectory = $ruta_absoluta . "/log/log";
        $logFile = $logDirectory . date("Y-m-d") . ".log";
        $timestamp = date("Y-m-d H:i:s");

        // Construir el formato del registro
        $logEntry = "[" . $timestamp . "] " . ($isError ? "[ERROR] " : "[OK] ") . $message . "\n";

        // Verificar si es un nuevo día y crear un archivo de log nuevo si es necesario
        if (!file_exists($logFile)) {
            if (!is_dir($logDirectory)) {
                mkdir($logDirectory, 0755, true);
            }
            touch($logFile);
        }

        // Escribir el registro en el archivo de log
        $fileHandle = fopen($logFile, 'a');
        fwrite($fileHandle, $logEntry);
        fclose($fileHandle);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
} 

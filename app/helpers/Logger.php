<?php

namespace App\Helpers;

/**
 * Clase para manejar el registro de logs en la aplicación
 */
class Logger
{
    // Niveles de log
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const DEBUG = 'DEBUG';

    // Directorio donde se guardarán los logs
    private static $logDir = 'c:/xampp/htdocs/timeControl/storage/logs/';

    /**
     * Escribe un mensaje en el archivo de log
     * 
     * @param string $message Mensaje a registrar
     * @param string $level Nivel del mensaje (ERROR, WARNING, INFO, DEBUG)
     * @param array $context Datos adicionales para el log
     * @return bool Éxito de la operación
     */
    public static function log($message, $level = self::ERROR, $context = [])
    {
        // Crear directorio si no existe
        if (!is_dir(self::$logDir)) {
            if (!mkdir(self::$logDir, 0755, true)) {
                // Si no se puede crear, usar directorio temporal
                self::$logDir = sys_get_temp_dir() . '/timecontrol/';
                if (!is_dir(self::$logDir)) {
                    mkdir(self::$logDir, 0755, true);
                }
            }
        }

        // Formatear fecha y hora
        $date = date('Y-m-d H:i:s');

        // Formatear contexto como JSON si existe
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';

        // Formar línea de log
        $logLine = "[$date] [$level] $message$contextStr" . PHP_EOL;

        // Nombre del archivo según fecha y nivel
        $filename = self::$logDir . date('Y-m-d') . '-' . strtolower($level) . '.log';

        // Escribir log
        return file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Registra un error
     * 
     * @param string $message Mensaje de error
     * @param array $context Contexto del error
     * @return bool Éxito de la operación
     */
    public static function error($message, $context = [])
    {
        return self::log($message, self::ERROR, $context);
    }

    /**
     * Registra una advertencia
     * 
     * @param string $message Mensaje de advertencia
     * @param array $context Contexto de la advertencia
     * @return bool Éxito de la operación
     */
    public static function warning($message, $context = [])
    {
        return self::log($message, self::WARNING, $context);
    }

    /**
     * Registra información
     * 
     * @param string $message Mensaje informativo
     * @param array $context Contexto de la información
     * @return bool Éxito de la operación
     */
    public static function info($message, $context = [])
    {
        return self::log($message, self::INFO, $context);
    }

    /**
     * Registra mensaje de depuración
     * 
     * @param string $message Mensaje de depuración
     * @param array $context Contexto de depuración
     * @return bool Éxito de la operación
     */
    public static function debug($message, $context = [])
    {
        return self::log($message, self::DEBUG, $context);
    }

    /**
     * Registra una excepción
     * 
     * @param \Exception|\Throwable $exception Excepción a registrar
     * @param array $context Contexto adicional
     * @return bool Éxito de la operación
     */
    public static function exception($exception, $context = [])
    {
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        $context['trace'] = $exception->getTraceAsString();

        return self::error($exception->getMessage(), $context);
    }
}

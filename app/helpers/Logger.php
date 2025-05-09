<?php

namespace App\Helpers;

/**
 * Clase optimizada para manejar el registro de logs en la aplicación
 */
class Logger
{
    // Niveles de log
    public const ERROR = 'ERROR';
    public const WARNING = 'WARNING';
    public const INFO = 'INFO';
    public const DEBUG = 'DEBUG';

    // Directorio donde se guardarán los logs
    private static string $logDir = 'c:/xampp/htdocs/timeControl/storage/logs/';
    
    // Cache para el directorio (evitar verificaciones repetidas)
    private static bool $dirChecked = false;
    
    // Zona horaria por defecto
    private static string $timezone = 'America/Santo_Domingo';

    /**
     * Escribe un mensaje en el archivo de log
     * 
     * @param string $message Mensaje a registrar
     * @param string $level Nivel del mensaje (ERROR, WARNING, INFO, DEBUG)
     * @param array $context Datos adicionales para el log
     * @return bool|int Éxito de la operación (bytes escritos o false)
     */
    public static function log(string $message, string $level = self::ERROR, array $context = [])
    {
        // Verificar el directorio solo una vez por ejecución
        if (!self::$dirChecked) {
            self::ensureLogDirectoryExists();
        }

        // Obtener timestamp con zona horaria correcta
        $date = self::getFormattedDateTime();
        
        // Optimizar la creación de la línea de log
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[$date] [$level] $message$contextStr" . PHP_EOL;
        
        // Nombre del archivo con formato optimizado
        $filename = self::getLogFilename($level);
        
        // Escribir log con manejo optimizado de archivos
        return file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Asegura que el directorio de logs exista
     * 
     * @return void
     */
    private static function ensureLogDirectoryExists(): void
    {
        if (!is_dir(self::$logDir)) {
            if (!@mkdir(self::$logDir, 0755, true)) {
                // Si no se puede crear, usar directorio temporal
                self::$logDir = rtrim(sys_get_temp_dir(), '/\\') . '/timecontrol/';
                if (!is_dir(self::$logDir)) {
                    @mkdir(self::$logDir, 0755, true);
                }
            }
        }
        self::$dirChecked = true;
    }
    
    /**
     * Obtiene la fecha y hora formateadas
     * 
     * @return string
     */
    private static function getFormattedDateTime(): string
    {
        static $timezone = null;
        
        if ($timezone === null) {
            date_default_timezone_set(self::$timezone);
            $timezone = new \DateTimeZone(self::$timezone);
        }
        
        return (new \DateTime('now', $timezone))->format('Y-m-d H:i:s');
    }
    
    /**
     * Obtiene el nombre del archivo de log
     * 
     * @param string $level Nivel del log
     * @return string
     */
    private static function getLogFilename(string $level): string
    {
        static $dateForFilename = null;
        
        if ($dateForFilename === null || $dateForFilename !== date('Y-m-d')) {
            $dateForFilename = date('Y-m-d');
        }
        
        return self::$logDir . $dateForFilename . '-' . strtolower($level) . '.log';
    }

    /**
     * Registra un error
     * 
     * @param string $message Mensaje de error
     * @param array $context Contexto del error
     * @return bool|int Éxito de la operación
     */
    public static function error(string $message, array $context = [])
    {
        return self::log($message, self::ERROR, $context);
    }

    /**
     * Registra una advertencia
     * 
     * @param string $message Mensaje de advertencia
     * @param array $context Contexto de la advertencia
     * @return bool|int Éxito de la operación
     */
    public static function warning(string $message, array $context = [])
    {
        return self::log($message, self::WARNING, $context);
    }

    /**
     * Registra información
     * 
     * @param string $message Mensaje informativo
     * @param array $context Contexto de la información
     * @return bool|int Éxito de la operación
     */
    public static function info(string $message, array $context = [])
    {
        return self::log($message, self::INFO, $context);
    }

    /**
     * Registra mensaje de depuración
     * 
     * @param string $message Mensaje de depuración
     * @param array $context Contexto de depuración
     * @return bool|int Éxito de la operación
     */
    public static function debug(string $message, array $context = [])
    {
        return self::log($message, self::DEBUG, $context);
    }

    /**
     * Registra una excepción
     * 
     * @param \Throwable $exception Excepción a registrar
     * @param array $context Contexto adicional
     * @return bool|int Éxito de la operación
     */
    public static function exception(\Throwable $exception, array $context = [])
    {
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        $context['trace'] = $exception->getTraceAsString();

        return self::error($exception->getMessage(), $context);
    }
    
    /**
     * Cambia el directorio de logs
     * 
     * @param string $directory Nuevo directorio de logs
     * @return void
     */
    public static function setLogDirectory(string $directory): void
    {
        self::$logDir = rtrim($directory, '/\\') . '/';
        self::$dirChecked = false; // Resetear verificación
    }
    
    /**
     * Cambia la zona horaria
     * 
     * @param string $timezone Zona horaria
     * @return void
     */
    public static function setTimezone(string $timezone): void
    {
        self::$timezone = $timezone;
    }
}
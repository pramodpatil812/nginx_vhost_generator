<?php
namespace Vhosts\Nginx;

/**
 * Contains utility functions for file handling.
 * @author Pramod Patil <pramodnitkmca@gmail.com>
 */
class FileUtil
{
    /**
     * Check if file exists and is a regular file.
     * @param string $filename Name of the file
     * @return boolean True, if file exists and a regular file, false otherwise.
     */
    public static function isFileExists($filename)
    {
        return is_file($filename);
    }

    /**
     * Check if file exists and is a directory.
     * @param string $filename Name of the file.
     * @return boolean True, if file exists and a directory, false otherwise.
     */
    public static function isDirectory($filename)
    {
        return is_dir($filename);
    }
    
    /**
     * Check if file exists and is a directory.
     * @param string $filename Name of the file to be read.
     * @return string/boolean file content as a string if successful, false otherwise.
     */
    public static function readFile($filename)
    {
        return file_get_contents($filename);
    }

    /**
     * Check if file exists and is a directory.
     * @param string $filename Name of the file.
     * @param string $string Content to be written into file.
     * @return boolean True, if content written successfully, false otherwise.
     */
    public static function writeToFile($filename, $string)
    {
        return file_put_contents($filename, $string);
    }
}

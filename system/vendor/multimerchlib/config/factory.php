<?php

namespace MultiMerch\Config;

use InvalidArgumentException, RuntimeException;

abstract class Factory
{
    /**
     * Read a config from a file.
     *
     * @param  string  $filename
     * @param  bool $returnConfigObject
     * @param  bool $useIncludePath
     * @return array|\MultiMerch\Config\Config
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public static function fromFile($filename, $returnConfigObject = false, $useIncludePath = false)
    {
        $filepath = $filename;
        if (!file_exists($filename)) {
            if (!$useIncludePath) {
                throw new RuntimeException(sprintf(
                    'Filename "%s" cannot be found relative to the working directory',
                    $filename
                ));
            }

            $fromIncludePath = stream_resolve_include_path($filename);
            if (!$fromIncludePath) {
                throw new RuntimeException(sprintf(
                    'Filename "%s" cannot be found relative to the working directory or the include_path ("%s")',
                    $filename,
                    get_include_path()
                ));
            }
            $filepath = $fromIncludePath;
        }

        $pathinfo = pathinfo($filepath);

        if (!isset($pathinfo['extension'])) {
            throw new RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $filename
            ));
        }

        $extension = strtolower($pathinfo['extension']);

        if ($extension === 'php') {
            if (!is_file($filepath) || !is_readable($filepath)) {
                throw new RuntimeException(sprintf(
                    "File '%s' doesn't exist or not readable",
                    $filename
                ));
            }

            $config = include $filepath;
        } else {
            throw new RuntimeException(sprintf(
                'Unsupported config file extension: .%s',
                $pathinfo['extension']
            ));
        }

        return ($returnConfigObject) ? new Config($config) : $config;
    }

    /**
     * Read configuration from multiple files and merge them.
     *
     * @param  array   $files
     * @param  bool $returnConfigObject
     * @param  bool $useIncludePath
     * @return array|Config
     */
    public static function fromFiles(array $files, $returnConfigObject = false, $useIncludePath = false)
    {
        $config = array();

        foreach ($files as $file) {
            $config = \MultiMerch\Stdlib\ArrayUtils::merge($config, static::fromFile($file, false, $useIncludePath));
        }

        return ($returnConfigObject) ? new Config($config) : $config;
    }
}
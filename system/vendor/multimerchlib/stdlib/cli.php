<?php

namespace MultiMerch\Stdlib;

/**
 * Basic functionality to work with cli output
 *
 * Class CliUtils
 * @package MultiMerch\Stdlib
 */
class CliUtils
{
    protected function newLine()
    {
        return PHP_EOL;
    }

    protected function getCommentStartLine()
    {
        return '/**' . self::newLine();
    }

    protected function getCommentLine($s = '')
    {
        if ($s) {
            $s = ' ' . $s;
        }
        return ' *' . $s . self::newLine();
    }

    protected function getCommentEndLine()
    {
        return ' */' . self::newLine();
    }

    protected function printSuccess($msg = '')
    {
        if (php_sapi_name() === 'cli') {
            echo "\e[32m" . $msg . "\e[0m" . PHP_EOL;
        } else {
            echo '<p>' . $msg . '</p>';
        }
    }

    protected function printInfo($msg = '')
    {
        if (php_sapi_name() === 'cli') {
            echo "\e[36m" . $msg . "\e[0m" . PHP_EOL;
        } else {
            echo '<p>' . $msg . '</p>';
        }
    }

    protected function printWarning($msg = '')
    {
        if (php_sapi_name() === 'cli') {
            echo "\e[33m" . $msg . "\e[0m" . PHP_EOL;
        } else {
            echo '<p>' . $msg . '</p>';
        }
    }

    protected function printError($msg = '')
    {
        if (php_sapi_name() === 'cli') {
            echo "\e[31m" . $msg . "\e[0m" . PHP_EOL;
        } else {
            echo '<p>' . $msg . '</p>';
        }
    }
}
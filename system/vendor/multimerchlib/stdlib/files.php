<?php

namespace MultiMerch\Stdlib;

trait Files
{
    public function copyFile($sourceFile, $destinationFile)
    {
        if (!file_exists($sourceFile)) {
            throw new \Exception('Error during copying file, file does not exists, ' . $sourceFile);
        }
        $destinationDir = rtrim($destinationFile, basename($destinationFile));
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }
        if (file_exists($destinationFile)) {
            unlink($destinationFile);
        }
        return copy($sourceFile, $destinationFile);
    }

    public function copyDir($sourceDir, $destinationDir, $pattern = '*')
    {
        if (is_dir($sourceDir)) {
            $sourceDir = $this->addSlash($sourceDir);
            $destinationDir = $this->addSlash($destinationDir);
            foreach (glob($sourceDir . $pattern) as $filename) {
                if (is_dir($filename)) {
                    $dirname = basename($filename);
                    $this->copyDir($filename, $destinationDir . $dirname);
                } else {
                    $this->copyFile($filename, $destinationDir . basename($filename));
                }
            }
        } else {
            $this->copyFile($sourceDir, $destinationDir);
        }
    }

    public function copyFileSYS($sourceFile, $destinationDir)
    {
        $destinationDir = $this->addSlash($destinationDir);
        return shell_exec('cp ' . escapeshellcmd($sourceFile) . ' ' . escapeshellcmd($destinationDir));
    }

    public function copyDirSYS($sourceDir, $destinationDir)
    {
        $sourceDir = $this->addSlash($sourceDir);
        $destinationDir = $this->addSlash($destinationDir);
        return shell_exec('cp -r ' . escapeshellcmd($sourceDir) . ' ' . escapeshellcmd($destinationDir));
    }

    private function addSlash($dir)
    {
        return rtrim($dir, '/') . '/';
    }
}
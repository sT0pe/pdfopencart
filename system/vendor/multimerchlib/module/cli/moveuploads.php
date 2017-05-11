<?php

$conf = require __DIR__ . '/../config/config.php';

$OC_DIR = $conf['PATH']['OC_DIR'];
define('PATH_OC_DIR', $OC_DIR);

$PUBLIC_DIR = $conf['PATH']['PUBLIC_DIR'];
define('PATH_PUBLIC_DIR', $PUBLIC_DIR);

$MM_MODULE_DIR = $conf['PATH']['MM_MODULE_DIR'];
define('PATH_MM_MODULE_DIR', $MM_MODULE_DIR);

require_once $OC_DIR . '/multimerchlib/stdlib/cli.php';
require_once $OC_DIR . '/multimerchlib/stdlib/files.php';

class MoveUploads extends \MultiMerch\Stdlib\CliUtils
{
    use \MultiMerch\Stdlib\Files;

    public function moveMMUploads($target = null)
    {
        $this->printInfo('Starting copy multimerch_upload files');

        if ($target == 'public' || $target == 'system' || $target == 'vqmod' || $target == 'tpl') {
            $this->printWarning('Selected target: ' . $target);
        }

        // copy multimerch module into opencart files

        // public
        if ($target == 'public' || $target === null) {
            $source = realpath(PATH_MM_MODULE_DIR . '/admin/view/image/');
            $dest = PATH_PUBLIC_DIR . '/admin/view/image/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/admin/view/javascript/');
            $dest = PATH_PUBLIC_DIR . '/admin/view/javascript/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/admin/view/stylesheet/');
            $dest = PATH_PUBLIC_DIR . '/admin/view/stylesheet/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/catalog/view/javascript/');
            $dest = PATH_PUBLIC_DIR . '/catalog/view/javascript/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/image/');
            $dest = PATH_PUBLIC_DIR . '/image/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);
        }

        // tpl
        if ($target == 'tpl' || $target === null) {
            $source = realpath(PATH_MM_MODULE_DIR . '/admin/view/template/');
            $dest = PATH_OC_DIR . '/admin/view/template/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/catalog/view/theme/');
            $dest = PATH_OC_DIR . '/catalog/view/theme/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);
        }


        // system
        if ($target == 'system' || $target === null) {
            $source = realpath(PATH_MM_MODULE_DIR . '/admin/controller/');
            $dest = PATH_OC_DIR . '/admin/controller/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/admin/language/');
            $dest = PATH_OC_DIR . '/admin/language/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/admin/model/');
            $dest = PATH_OC_DIR . '/admin/model/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/catalog/controller/');
            $dest = PATH_OC_DIR . '/catalog/controller/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/catalog/language/');
            $dest = PATH_OC_DIR . '/catalog/language/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/catalog/model/');
            $dest = PATH_OC_DIR . '/catalog/model/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);

            $source = realpath(PATH_MM_MODULE_DIR . '/system/');
            $dest = PATH_OC_DIR . '/system/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);
        }

        // vqmod
        if ($target == 'vqmod' || $target == 'system' || $target === null) {
            $source = realpath(PATH_MM_MODULE_DIR . '/vqmod/');
            $dest = PATH_OC_DIR . '/vqmod/';
            $this->printSuccess('Copying: ' . $source . ' -> ' . $dest);
            $this->copyDir($source, $dest);
        }

        $this->printInfo('Finish');
    }
}

$options = getopt('t:', array('target:'));
$target = isset($options['t']) ? $options['t'] : (isset($options['target']) ? $options['target'] : null);

$copy = new MoveUploads();
$copy->moveMMUploads($target);

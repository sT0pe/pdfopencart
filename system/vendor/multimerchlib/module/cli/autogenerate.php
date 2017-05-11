<?php

$_SERVER['SERVER_PORT'] = 80; // workaround for startup.php

// skip using MultiMerch class map autoloader, load original OpenCart class
define('MM_DISABLE_EXTENDED_AUTOLOADER', 1);

$conf = require __DIR__ . '/../config/config.php';

$MM_LIB_DIR = $conf['PATH']['MM_LIB_DIR'];
define('PATH_MM_LIB_DIR', $MM_LIB_DIR);

$OC_DIR = $conf['PATH']['OC_DIR'];
define('PATH_OC_DIR', $OC_DIR);

// register autoloaders
require $MM_LIB_DIR . '/module/init_multimerch.php';
require $OC_DIR . '/config.php';
require $OC_DIR . '/system/startup.php';

$classmap = require __DIR__ . '/../config/autoload_classmap.php';

require_once $MM_LIB_DIR . '/stdlib/cli.php';

// Will be removed after complete moving from VQMod
if (file_exists(PATH_OC_DIR . '/vqmod/vqmod.php')) {
    require_once(PATH_OC_DIR . '/vqmod/vqmod.php');
    VQMod::bootup();
}

class AutoGenFiles extends \MultiMerch\Stdlib\CliUtils
{
    function generate(array $classmap)
    {
        $this->printInfo('Starting generating classes');
        foreach ($classmap as $ns => $path) {
            $classArr = explode('\\', $ns);
            $class = array_pop($classArr);
            if (strpos($class, 'oc_') === 0) {
                $className = substr($class, 3);
                $classArr[] = $className;
                $classNameOrig = implode('\\', $classArr);
                $reflection = new ReflectionClass(ucfirst($classNameOrig));
                $file = $reflection->getFileName();
                if (class_exists('VQMod')) {
                    // to be removed
                    $file = VQMod::modCheck($file);
                }
                $data = file($file);
                foreach ($data as $k => $line) {
                    if (preg_match('/^(abstract |final )?class ' . ucfirst($className) . '/m', trim($line))) {
                        $line = str_replace(array('final'), '', $line);
                        $line = str_replace(ucfirst($className), $class, $line);
                        $data[$k] = ltrim($line);
                    }
                    if (preg_match('/^private \$(.)+/m', trim($line))) {
                        $line = str_replace('private $', 'protected $', $line);
                        $data[$k] = $line;
                    }
                }
                $dirname = dirname($path);
                if (!is_dir($dirname)) {
                    mkdir($dirname, 0755, true);
                }
                file_put_contents($path, $data);
                $this->printSuccess('Created: ' . $path);
            }
        }
        $this->printInfo('Finish');
    }
}

$cl = new AutoGenFiles();
$cl->generate($classmap);


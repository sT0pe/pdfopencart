<?php

// Load classes with MultiMerch\ namespace from multimerch/lib
spl_autoload_register(function ($class) {
    if (strpos($class, '\\') === false) {
        return false; // no namespace used
    }
    $class = str_replace('\\', '/', strtolower($class));
    if (strpos($class, 'multimerch') !== 0) {
        return false; // not a MultiMerch\ class
    }

    $class = mb_substr($class, mb_strlen('multimerch/'));
    $file = __DIR__ . '/../' . $class . '.php';

    if (is_file($file)) {
        //include_once($file);
	    include_once(\VQMod::modCheck(modification($file)));
        return true;
    } else {
        return false;
    }
}, true, true);

// Register MultiMerch Loaders
spl_autoload_register(function ($class) {
    /** @var \Registry $registry */
    global $registry; // workaround to get access to the index.php $registry object

    static $msLoader = null;
    if (is_null($msLoader) && $registry instanceof \Registry) {
        require_once(DIR_SYSTEM . 'library/msloader.php');
        $msLoader = MsLoader::getInstance();
        $msLoader->setRegistry($registry);
        $registry->set('MsLoader', $msLoader);
    }

    // init extended class autoloader
    if (defined('MM_DISABLE_EXTENDED_AUTOLOADER')) {
        return false; // skip if defined constant
    }
    static $classMap = null;
    if (is_null($classMap)) {
        $classMap = include __DIR__ . '/config/autoload_classmap.php';
    }
    $class = strtolower($class);
    $file = isset($classMap[$class]) ? $classMap[$class] : null;
    if ($file && is_file($file)) {
        //include_once($file);
	    include_once(\VQMod::modCheck(modification($file)));
        return true;
    } else {
        return false;
    }
}, true, true);

// system/engine autoloader
//spl_autoload_register(function ($class) {
//    $class = str_replace('\\', '/', strtolower($class));
//    $file = DIR_SYSTEM . 'engine/' . $class . '.php';
//
//    if (is_file($file)) {
//        include_once($file);
//        return true;
//    } else {
//        return false;
//    }
//});

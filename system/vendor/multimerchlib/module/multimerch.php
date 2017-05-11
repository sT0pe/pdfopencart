<?php

namespace MultiMerch\Module;

use Registry;

class MultiMerch
{
    /** @var Registry */
    protected $registry;

    /** @var \MultiMerch\ServiceLocator\ServiceLocator */
    protected $serviceLocator;

    public function __construct(Registry $registry)
    {
        define('MULTIMERCH_MODULE_DIR', __DIR__);
        define('MULTIMERCH_OC_ROOT_DIR', realpath(MULTIMERCH_MODULE_DIR . '/../../../../'));

        $this->registry = $registry;
        $confFiles = array();

        array_push($confFiles, realpath(MULTIMERCH_MODULE_DIR . '/config/multimerch.php'));
        // add possibility override config with upload/config/multimerch.config.php
        $userDefinedConfig = realpath(MULTIMERCH_OC_ROOT_DIR . '/config/multimerch.config.php');
        if ($userDefinedConfig) {
            array_push($confFiles, $userDefinedConfig);
        }
        $MultiMerchConfig = \MultiMerch\Config\Factory::fromFiles($confFiles, true);
        $this->serviceLocator = new \MultiMerch\ServiceLocator\ServiceLocator($this, $MultiMerchConfig);
    }

    /**
     * @return \MultiMerch\ServiceLocator\ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @return string Theme name
     */
    public function getViewTheme()
    {
		$theme = $this->getConfigOC((defined('VERSION') && VERSION >= 2.3 ? 'theme_default_directory' : 'config_template'), 'default');
        return $theme;
    }

    /**
     * @return string Theme name
     */
    public function formatMoney($money, $currency = null)
    {
        if (!$currency) {
            $currency = $this->getConfigOC('config_currency');
        }
        $money = $this->getRegistry()->get('currency')->format($money, $currency);
        return $money;
    }

    /**
     * Get OpenCart config
     * @see \Config
     * 
     * @param $key
     * @return mixed
     */
    public function getConfigOC($key, $default = null)
    {
        $value = $this->getRegistry()->get('config')->get($key);
        if (is_null($value) && !is_null($default)) {
            $value = $default;
        }
        return $value;
    }

    public function getNotificationEmail()
    {
        $email = $this->getConfigOC('msconf_notification_email');
        if (!$email) {
            $email = $this->getConfigOC('config_email');
        }
        return $email;
    }
}
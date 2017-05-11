<?php

/**
 * Create service instances based on settings in configuration files
 */
namespace MultiMerch\ServiceLocator;

use \MultiMerch\Config\Config;

class ServiceLocator implements ServiceLocatorInterface
{
    /**
     * Lookup for shared class instances
     *
     * @var array
     */
    protected $instances = array();

    /** @var Config */
    protected $config;

    /** @var \MultiMerch\Module\MultiMerch */
    protected $multiMerchModule;

    public function __construct(\MultiMerch\Module\MultiMerch $multiMerchModule, Config $config)
    {
        $this->multiMerchModule = $multiMerchModule;
        $config = $config->get('servicelocator', new Config(array()));
        $this->config = $config;
    }

    public function get($name, $shareInstance = true)
    {
        $factoriesConfig = $this->config->get('factories', new Config(array()));
        $instancesConfig = $this->config->get('instances', new Config(array()));
        $cname = \MultiMerch\Stdlib\CName::canonicalizeName($name);
        $instance = null;
        $createNew = false;
        if (!$shareInstance) {
            $createNew = true;
        } else {
            if (!isset($this->instances[$cname])) {
                $createNew = true;
            } else {
                $instance = $this->instances[$cname];
            }
        }
        if ($createNew) {
            if ($factoriesConfig->offsetExists($cname)) {
                $factory = $factoriesConfig->get($cname);
                if (!method_exists($factory, 'create')) {
                    throw new \MultiMerch\ServiceLocator\Exception\ServiceLocatorException('Factory ' . $factory . ' should has static method \'create\'');
                }
                $instance = call_user_func(array($factory, 'create'), $this->getRegistry());
            } else {
                if ($instancesConfig->offsetExists($cname)) {
                    $instancesClass = $instancesConfig->get($cname);
                    $instance = new $instancesClass;
                } else {
                    throw new \MultiMerch\ServiceLocator\Exception\ServiceLocatorException('Could not find service: ' . $name);
                }
            }

            if ($instance instanceof ServiceLocatorAwareInterface) {
                $instance->setServiceLocator($this);
            }
            if ($shareInstance) {
                $this->instances[$cname] = $instance;
            }
        }
        return $instance;
    }

    /**
     * If locator has service instance
     *
     * @param array|string $name
     * @return bool
     */
    public function has($name)
    {
        $cname = \MultiMerch\Stdlib\CName::canonicalizeName($name);
        return isset($this->instances[$cname]);
    }

    /**
     * @return \Registry
     */
    public function getRegistry()
    {
        return $this->multiMerchModule->getRegistry();
    }

    /**
     * @return \MultiMerch\Module\MultiMerch
     */
    public function getMultiMerchModule()
    {
        return $this->multiMerchModule;
    }
}
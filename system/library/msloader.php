<?php

class MsLoader
{
    public $appVer = "8.7.2";
    public $dbVer = "2.4.0.1";
	public $langVer = "1.0.7.11";

    /**
     * Lookup for shared class instances
     *
     * @var array
     */
    public $instances = array();

    /** @var Registry */
    protected $registry;

    /** @var MsLoader */
    private static $instance;

    private function __construct()
    {
        spl_autoload_register(array('MsLoader', '_autoloadLibrary'));
        spl_autoload_register(array('MsLoader', '_autoloadController'));
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __get($class)
    {
        return $this->load($class);
    }

    public function setRegistry(Registry $registry)
    {
        $this->registry = $registry;
        return $this;
    }

    public function getRegistry()
    {
        return $this->registry;
    }

	/**
	 * Load MultiMerch class with namespace support
	 *
	 * @param $className
	 * @param bool $shareInstance Share existing instance or create new
	 * @return Object
	 */
	public function load($className, $shareInstance = true)
	{
		$cname = \MultiMerch\Stdlib\CName::canonicalizeName($className);
		if (!$shareInstance) {
			$instance = new $className($this->registry);
		} else {
			if (isset($this->instances[$cname])) {
				$instance = $this->instances[$cname];
			} else {
				$instance = new $className($this->registry);
				$this->instances[$cname] = $instance;
			}
		}

		return $instance;
	}

	private static function _autoloadLibrary($class)
	{
		$file = DIR_SYSTEM . 'library/' . strtolower($class) . '.php';
		if (file_exists($file)) {
			require_once(VQMod::modCheck($file));
		} else {
			$file = DIR_SYSTEM . 'library/multimerch/' . strtolower($class) . '.php';
			if (file_exists($file)) {
				require_once(VQMod::modCheck($file));
			}
		}
	}

	private static function _autoloadController($class)
	{
		preg_match_all('/((?:^|[A-Z])[a-z]+)/', $class, $matches);

		if (isset($matches[0][1]) && isset($matches[0][2])) {
			$file = DIR_APPLICATION . 'controller/' . strtolower($matches[0][1]) . '/' . strtolower($matches[0][2]) . '.php';
			if (file_exists($file)) {
				require_once(VQMod::modCheck($file));
			}
		}
	}
}

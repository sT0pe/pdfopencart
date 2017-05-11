<?php

class Loader extends OC_Loader
{
    /** @var \Registry */
    protected $registry;

    public function __construct(\Registry $registry)
    {
        parent::__construct($registry);
        $this->registry = $registry;
    }

    /**
     * Proxy to registry
     * @see \Registry::get()
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->registry->get($name);
    }

    public function __set($name, $value)
    {
        $this->registry->set($name, $value);
        return $this;
    }

    /**
     * @return \Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    public function view($route, $data = array())
    {
        $route = preg_replace('#^default\/template[\/]?#', '', $route);
        /** @see \MultiMerch\Module\MultiMerch::getViewTheme */
        $defTpl = MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme() . '/template';
        $route = preg_replace('#^' . preg_quote($defTpl, '#') . '[\/]?#', '', $route);
        $data['session'] = $this->getRegistry()->get('session');
        return parent::view($route, $data);
    }
}

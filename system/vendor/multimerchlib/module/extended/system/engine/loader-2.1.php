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

    public function view($template, $data = array())
    {
        $isAdmin = defined('IS_ADMIN_DIR');
        static $extendedDir;
        if (is_null($extendedDir)) {
            $extendedDir = realpath(__DIR__ . '/../extended/' . ($isAdmin ? 'admin/view/theme/' : 'catalog/view/theme/'));
            $extendedDir .= '/';
        }

        $file = $extendedDir . $template;
        $data['template'] = $template;
        if (file_exists($file)) {
            extract($data);
            ob_start();
            require($file);
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        } else {
            return parent::view($template, $data);
        }
    }

    public function originalView($template, $data = array())
    {
        return parent::view($template, $data);
    }
}

<?php

namespace MultiMerch\View\Renderer;

use ArrayAccess;
use MultiMerch\View\Variables;
use MultiMerch\ServiceLocator\ServiceLocatorAwareTrait;
use MultiMerch\ServiceLocator\ServiceLocatorAwareInterface;

class PhpRenderer implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var string Rendered content
     */
    private $__content = '';

    /**
     * Template being rendered
     *
     * @var null|string
     */
    private $__template = null;

    /**
     * @var ArrayAccess|array ArrayAccess or associative array representing available variables
     */
    private $__vars;

    /**
     * @var array Temporary variable stack; used when variables passed to render()
     */
    private $__varsCache = array();

    /**
     * @var array User defined filters to filter rendered output
     */
    private $__filterChain = array();

    /**
     * Set variable storage
     *
     * Expects either an array, or an object implementing ArrayAccess.
     *
     * @param  array|ArrayAccess $variables
     * @return PhpRenderer
     * @throws \InvalidArgumentException
     */
    public function setVars($variables)
    {
        if (!is_array($variables) && !$variables instanceof ArrayAccess) {
            throw new \InvalidArgumentException(sprintf(
                'Expected array or ArrayAccess object; received "%s"',
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        // Enforce a Variables container
        if (!$variables instanceof Variables) {
            $variablesAsArray = array();
            foreach ($variables as $key => $value) {
                $variablesAsArray[$key] = $value;
            }
            $variables = new Variables($variablesAsArray);
        }

        $this->__vars = $variables;
        return $this;
    }

    /**
     * Get a single variable, or all variables
     *
     * @param  mixed $key
     * @return mixed
     */
    public function vars($key = null)
    {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }

        if (null === $key) {
            return $this->__vars;
        }
        return $this->__vars[$key];
    }

    /**
     * Get a single variable
     *
     * @param  mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }

        return $this->__vars[$key];
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $vars = $this->vars();
        return $vars[$name];
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $vars = $this->vars();
        $vars[$name] = $value;
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $vars = $this->vars();
        return isset($vars[$name]);
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $vars = $this->vars();
        if (!isset($vars[$name])) {
            return;
        }
        unset($vars[$name]);
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param  string $name Either the template to use
     *
     * @param  null|array|\Traversable $values Values to use when rendering. If none
     *                                provided, uses those in the composed
     *                                variables container.
     * @return string The script output.
     * @throws \RuntimeException if the template cannot be rendered
     * @throws \UnexpectedValueException if unable to render
     * @throws \Exception if rendering error
     */
    public function render($name, $values = null)
    {
        $name = $this->resolveTemplate($name);
        if (!$name) {
            throw new \UnexpectedValueException(sprintf(
                'Resolver could not resolve path to "%s"',
                __METHOD__,
                $name
            ));
        }

        $this->__template = $name;
        unset($name);

        $this->__varsCache[] = $this->vars();

        if (null !== $values) {
            $this->setVars($values);
        }
        unset($values);

        // extract all assigned vars (pre-escaped), but not 'this'.
        // assigns to a double-underscored variable, to prevent naming collisions
        $__vars = $this->vars()->getArrayCopy();
        if (array_key_exists('this', $__vars)) {
            unset($__vars['this']);
        }
        extract($__vars);
        unset($__vars); // remove $__vars from local scope

        $this->__file = realpath($this->__template);
        if (!$this->__file) {
            throw new \RuntimeException(sprintf(
                '%s: Unable to render template "%s"',
                __METHOD__,
                $this->__template
            ));
        }
        try {
            ob_start();
            $includeReturn = include $this->__file;
            $this->__content = ob_get_clean();
        } catch (\Exception $ex) {
            ob_end_clean();
            throw $ex;
        }
        if ($includeReturn === false && empty($this->__content)) {
            throw new \UnexpectedValueException(sprintf(
                '%s: Unable to render template "%s"; file include failed',
                __METHOD__,
                $this->__file
            ));
        }

        $this->setVars(array_pop($this->__varsCache));

        return $this->filterOutput($this->__content); // filter output
    }

    /**
     * Render another template inside a template
     *
     * @param $name
     * @param array|null $values
     * @return string
     * @throws \Exception
     */
    public function partial($name, $values = null)
    {
        $renderer = new PhpRenderer();
        return $renderer->render($name, $values);
    }

    /**
     * Resolve template in user defined or in module defined space
     *
     * @param string $name Path to template
     * @return string|false
     */
    public function resolveTemplate($name)
    {
        $orig_name = $name;
        $theme = $this->getServiceLocator()->getMultiMerchModule()->getViewTheme();
        $name = str_replace('{theme}', $theme, $name);
        $path = realpath(MULTIMERCH_OC_ROOT_DIR . '/' . ltrim($name, '/'));
        if (!$path) {
            $path = realpath(MULTIMERCH_MODULE_DIR . '/' . ltrim($name, '/'));
        }
        if (!$path && strpos($orig_name, '{theme}')) {
            $name = str_replace('{theme}', 'default', $orig_name);
            return $this->resolveTemplate($name);
        }
        return $path;
    }

    public function translate($key)
    {
        static $translator;
        if (is_null($translator)) {
            $translator = $this->getServiceLocator()->get('Translator');
        }
        return $translator->get($key);
    }

    public function getMultiMerchModule()
    {
        return $this->getServiceLocator()->getMultiMerchModule();
    }

    /**
     * Make sure View variables are cloned when the view is cloned.
     *
     * @return PhpRenderer
     */
    public function __clone()
    {
        $this->__vars = clone $this->vars();
    }

    /**
     * Add output filter to the chain
     *
     * @param $filter
     * @return $this
     */
    public function registerFilter($filter)
    {
        if (!is_callable($filter)) {
            throw new \UnexpectedValueException(sprintf(
                '%s: is not callable',
                __METHOD__,
                $filter
            ));
        }
        $this->__filterChain[] = $filter;
        return $this;
    }

    /**
     * Clean filters chain
     *
     * @return $this
     */
    public function cleanFilters()
    {
        $this->__filterChain[] = array();
        return $this;
    }

    /**
     * Filter rendered output with registered filters
     *
     * @param string $output
     * @return mixed
     */
    public function filterOutput($output)
    {
        foreach ($this->__filterChain as $filter) {
            $output = call_user_func($filter, $output);
        }
        return $output;
    }
}
<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Http\View;

use Blivy\Exception\TemplateException;
use Blivy\Exception\TemplateNotFoundException;

class View
{
    /**
     * @var array
     */
    protected $properties;

    /**
     * @var string
     */
    protected $tpl;

    /**
     * @var string
     */
    protected $tpl_path;

    /**
     * View constructor.
     * @param $tpl
     */
    public function __construct($tpl)
    {
        $this->tpl = $tpl;
        $this->properties = [];
        $this->tpl_path = templatePath($tpl);

        return $this;
    }

    /**
     * ### Renders the template
     *
     * @return string
     * @throws TemplateNotFoundException
     */
    public function render()
    {
        set_error_handler([$this, 'renderErrorHandler']);
        ob_start();

        if (file_exists($this->tpl_path)) {
            include($this->tpl_path);
        } else {
            throw new TemplateNotFoundException($this->tpl);
        }

        return ob_get_clean();
    }

    /**
     * ### Renders template and echoes it
     *
     * @return string
     * @throws TemplateNotFoundException
     */
    public function make()
    {
        $rendered = $this->render();
        echo $rendered;
        return $rendered;
    }

    /**
     * ### Includes an extending template
     *
     * @param $tpl
     */
    public function take($tpl)
    {
        $path = templatePath($tpl);
        include $path;
    }

    /**
     * ### Sets a variable
     *
     * @param $k
     * @param $v
     * @return $this
     */
    public function set($k, $v)
    {
        $this->$k = $v;
        return $this;
    }

    /**
     * ### Gets a set variable
     *
     * @param $k
     * @return mixed
     */
    public function get($k)
    {
        return $this->$k;
    }

    /**
     * ### Handles uncaught errors in the templates
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @throws TemplateException
     */
    private function renderErrorHandler($errno, $errstr, $errfile, $errline)
    {
        ob_clean();
        ob_end_flush();

        $message = $errstr . ' in ' . $errfile . ', Line ' . $errline;
        throw new TemplateException($message);
    }
}
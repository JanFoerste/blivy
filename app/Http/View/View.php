<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Http\View;

use Manager\Exception\TemplateException;
use Manager\Exception\TemplateNotFoundException;

class View
{
    protected $properties;
    protected $tpl;
    protected $tpl_path;

    public function __construct($tpl)
    {
        $this->tpl = $tpl;
        $this->properties = [];
        $this->tpl_path = templatePath($tpl);

        return $this;
    }

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

    public function startBuffer()
    {
        ob_start();
    }

    public function make()
    {
        $rendered = $this->render();
        echo $rendered;
        return $rendered;
    }

    public function take($tpl)
    {
        $path = templatePath($tpl);
        include $path;
    }

    public function set($k, $v)
    {
        $this->$k = $v;
        return $this;
    }

    public function get($k)
    {
        return $this->$k;
    }

    private function renderErrorHandler($errno, $errstr, $errfile, $errline)
    {
        ob_clean();
        ob_end_flush();

        $message = $errstr . ' in ' . $errfile . ', Line ' . $errline;
        throw new TemplateException($message);
    }
}
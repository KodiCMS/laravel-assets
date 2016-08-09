<?php

namespace KodiCMS\Assets\Traits;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use KodiCMS\Assets\Html;
use stdClass;

trait Vars
{
    /**
     * @var array
     */
    protected $vars = [];

    /**
     * The namespace to nest JS vars under.
     *
     * @var string
     */
    protected $namespace = 'window';

    /**
     * @param string|array $key
     * @param mixed        $value
     *
     * @throws Exception
     *
     * @return $this
     */
    public function putVars($key, $value = null)
    {
        if (is_array($key)) {
            $variables = $key;
        } elseif (!is_null($value)) {
            $variables = [$key => $value];
        } else {
            throw new Exception('Try Assets::putVars(["foo" => "bar"]');
        }

        // First, we have to translate the variables
        // to something JS-friendly.
        $this->buildJavaScriptSyntax($variables);

        return $this;
    }

    /**
     * Remove a javascript vars.
     *
     * @return mixed Empty array or void
     */
    public function removeVars()
    {
        return $this->vars = [];
    }

    /**
     * @return string
     */
    public function renderVars()
    {
        return (new Html())->vars($this->buildNamespaceDeclaration().implode(PHP_EOL, $this->vars));
    }

    /**
     * Translate the array of PHP vars to
     * the expected JavaScript syntax.
     *
     * @param array $vars
     *
     * @return array
     */
    protected function buildJavaScriptSyntax(array $vars)
    {
        foreach ($vars as $key => $value) {
            $this->vars[] = $this->buildVariableInitialization($key, $value);
        }
    }

    /**
     * Create the namespace to which all vars are nested.
     *
     * @return string
     */
    protected function buildNamespaceDeclaration()
    {
        if ($this->namespace == 'window') {
            return '';
        }

        return "window.{$this->namespace} = window.{$this->namespace} || {};";
    }

    /**
     * Translate a single PHP var to JS.
     *
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    protected function buildVariableInitialization($key, $value)
    {
        return "{$this->namespace}.{$key} = {$this->optimizeValueForJavaScript($value)};";
    }

    /**
     * Format a value for JavaScript.
     *
     * @param string $value
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function optimizeValueForJavaScript($value)
    {
        // For every transformable type, let's see if
        // it needs to be transformed for JS-use.

        $types = [
            'String',
            'Array',
            'Object',
            'Numeric',
            'Boolean',
            'Null',
        ];

        foreach ($types as $transformer) {
            $js = $this->{"transform{$transformer}"}($value);

            if (!is_null($js)) {
                return $js;
            }
        }
    }

    /**
     * Transform a string.
     *
     * @param string $value
     *
     * @return string
     */
    protected function transformString($value)
    {
        if (is_string($value)) {
            $value = str_replace(['\\', "'"], ['\\\\', "\'"], $value);

            return "'{$value}'";
        }
    }

    /**
     * Transform an array.
     *
     * @param array $value
     *
     * @return string
     */
    protected function transformArray($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
    }

    /**
     * Transform a numeric value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function transformNumeric($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
    }

    /**
     * Transform a boolean.
     *
     * @param bool $value
     *
     * @return string
     */
    protected function transformBoolean($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
    }

    /**
     * @param object $value
     *
     * @throws Exception
     *
     * @return string
     */
    protected function transformObject($value)
    {
        if (!is_object($value)) {
            return;
        }

        // If a toJson() method exists, we'll assume that
        // the object can cast itself automatically.
        if (method_exists($value, 'toJson')) {
            return $value->toJson();
        }

        if ($value instanceof JsonSerializable || $value instanceof stdClass || $value instanceof Arrayable) {
            return json_encode($value);
        }

        // Otherwise, if the object doesn't even have a
        // __toString() method, we can't proceed.
        if (!method_exists($value, '__toString')) {
            throw new Exception('Cannot transform this object to JavaScript.');
        }

        return "'{$value}'";
    }

    /**
     * Transform "null.".
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function transformNull($value)
    {
        if (is_null($value)) {
            return 'null';
        }
    }
}

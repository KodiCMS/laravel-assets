<?php

namespace KodiCMS\Assets\Traits;

use KodiCMS\Assets\Contracts\AssetElementInterface;
use KodiCMS\Assets\Css;

trait Styles
{
    /**
     * @var AssetElementInterface[] CSS assets
     */
    protected $styles = [];

    /**
     * CSS wrapper.
     *
     * Gets or sets CSS assets
     *
     * @param string       $handle     Asset name.
     * @param string       $src        Asset source
     * @param array|string $dependency Dependencies
     * @param array        $attributes Attributes for the <link /> element
     *
     * @return AssetElementInterface Setting returns asset array, getting returns asset HTML
     */
    public function addCss($handle = null, $src = null, $dependency = null, array $attributes = [])
    {
        // Set default media attribute
        if (!isset($attributes['media'])) {
            $attributes['media'] = 'all';
        }

        return $this->styles[$handle] = new Css($handle, $src, $dependency, $attributes);
    }

    /**
     * @param string      $filename   [default: css/all.css]
     * @param null|string $dependency
     * @param array|null  $attributes
     *
     * @return $this
     */
    public function addCssElixir($filename = 'css/all.css', $dependency = null, array $attributes = [])
    {
        return $this->addCss($filename, elixir($filename), $dependency, $attributes);
    }

    /**
     * @param string      $filename   [default: css/all.css]
     * @param null|string $dependency
     * @param array|null  $attributes
     *
     * @return $this
     */
    public function addCssMix($filename = 'css/all.css', $dependency = null, array $attributes = [])
    {
        return $this->addCss($filename, mix($filename), $dependency, $attributes);
    }

    /**
     * Remove a CSS asset, or all.
     *
     * @param string|null $handle Asset name, or `NULL` to remove all
     *
     * @return mixed Empty array or void
     */
    public function removeCss($handle = null)
    {
        if (is_null($handle)) {
            return $this->styles = [];
        }

        unset($this->styles[$handle]);
    }

    /**
     * Get a single CSS asset.
     *
     * @param string $handle Asset name
     *
     * @return string Asset HTML
     */
    public function getCss($handle)
    {
        return (string) array_get($this->styles, $handle);
    }

    /**
     * Get all CSS assets, sorted by dependencies.
     *
     * @return string Asset HTML
     */
    public function renderStyles()
    {
        $this->loadPackageCss();

        if (empty($this->styles)) {
            return PHP_EOL;
        }

        $assets = [];

        foreach ($this->sort($this->styles) as $handle => $data) {
            $assets[] = $this->getCss($handle);
        }

        return implode(PHP_EOL, $assets);
    }

    /**
     * @deprecated
     *
     * @return string
     */
    public function getCssList()
    {
        return $this->renderStyles();
    }
}

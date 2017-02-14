<?php

namespace KodiCMS\Assets\Traits;

use KodiCMS\Assets\Contracts\AssetElementInterface;
use KodiCMS\Assets\Javascript;

trait Scripts
{
    /**
     * @var AssetElementInterface[] Javascript assets
     */
    protected $scripts = [];

    /**
     * Javascript wrapper.
     *
     * Gets or sets javascript assets
     *
     * @param bool|string  $handle
     * @param string       $src        Asset source
     * @param array|string $dependency Dependencies
     * @param bool         $footer     Whether to show in header or footer
     *
     * @return AssetElementInterface Setting returns asset array, getting returns asset HTML
     */
    public function addJs($handle = false, $src = null, $dependency = null, $footer = false)
    {
        return $this->scripts[$handle] = new Javascript($handle, $src, $dependency, $footer);
    }

    /**
     * @param string      $filename   [default: js/app.js]
     * @param null|string $dependency
     * @param bool        $footer
     *
     * @return $this
     */
    public function addJsElixir($filename = 'js/app.js', $dependency = null, $footer = false)
    {
        return $this->addJs($filename, elixir($filename), $dependency, $footer);
    }

    /**
     * Get a single javascript asset.
     *
     * @param string $handle Asset name
     *
     * @return string Asset HTML
     */
    public function getJs($handle)
    {
        return (string) array_get($this->scripts, $handle);
    }

    /**
     * Remove a javascript asset, or all.
     *
     * @param string|null $handle Remove all if `NULL`, section if `TRUE` or `FALSE`, asset if `string`
     *
     * @return mixed Empty array or void
     */
    public function removeJs($handle = null)
    {
        if (is_null($handle)) {
            return $this->scripts = [];
        }

        if (is_bool($handle)) {
            foreach ($this->scripts as $i => $javaScript) {
                if ($javaScript->isFooter() === $handle) {
                    unset($this->scripts[$i]);
                }
            }

            return;
        }

        unset($this->scripts[$handle]);
    }

    /**
     * @deprecated
     *
     * @param bool $footer
     *
     * @return string
     */
    public function getJsList($footer = false)
    {
        return $this->renderScripts($footer);
    }

    /**
     * Get all javascript assets of section (header or footer).
     *
     * @param bool $footer FALSE for head, TRUE for footer
     *
     * @return string Asset HTML
     */
    public function renderScripts($footer = false)
    {
        $this->loadPackageJs();

        if (empty($this->scripts)) {
            return PHP_EOL;
        }

        /** @var JavaScript[] $filteredScripts */
        $filteredScripts = [];

        foreach ($this->scripts as $file) {
            if ($file->isFooter() === $footer) {
                $filteredScripts[$file->getHandle()] = $file;
            }
        }

        if (empty($filteredScripts)) {
            return false;
        }

        $sorted = [];

        foreach ($this->sort($filteredScripts, $this->scripts) as $file) {
            $sorted[] = $file;
        }

        return implode(PHP_EOL, $sorted);
    }
}

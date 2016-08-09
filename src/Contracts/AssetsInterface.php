<?php

namespace KodiCMS\Assets\Contracts;

use Illuminate\Contracts\Support\Renderable;

interface AssetsInterface extends Renderable
{
    /**
     * @return PackageManagerInterface
     */
    public function packageManager();

    /**
     * @param string|array $names
     *
     * @return $this
     */
    public function loadPackage($names);

    /**
     * @return array
     */
    public function loadedPackages();

    /**
     * @return $this
     */
    public function removePackages();

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
    public function addCss($handle = null, $src = null, $dependency = null, array $attributes = []);

    /**
     * Get a single CSS asset.
     *
     * @param string $handle Asset name
     *
     * @return string Asset HTML
     */
    public function getCss($handle);

    /**
     * Get all CSS assets, sorted by dependencies.
     *
     * @return string Asset HTML
     */
    public function renderStyles();

    /**
     * Remove a CSS asset, or all.
     *
     * @param string|null $handle Asset name, or `NULL` to remove all
     *
     * @return mixed Empty array or void
     */
    public function removeCss($handle = null);

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
    public function addJs($handle = false, $src = null, $dependency = null, $footer = false);

    /**
     * Get a single javascript asset.
     *
     * @param string $handle Asset name
     *
     * @return string Asset HTML
     */
    public function getJs($handle);

    /**
     * Get all javascript assets of section (header or footer).
     *
     * @param bool $footer FALSE for head, TRUE for footer
     *
     * @return string Asset HTML
     */
    public function renderScripts($footer = false);

    /**
     * Remove a javascript asset, or all.
     *
     * @param string|null $handle Remove all if `NULL`, section if `TRUE` or `FALSE`, asset if `string`
     *
     * @return mixed Empty array or void
     */
    public function removeJs($handle = null);

    /**
     * Group wrapper.
     *
     * @param string $group   Group name
     * @param string $handle  Asset name
     * @param string $content Asset content
     *
     * @return $this
     */
    public function group($group, $handle = null, $content = null);

    /**
     * Get a single group asset.
     *
     * @param string $group  Group name
     * @param string $handle Asset name
     *
     * @return string|null Asset content
     */
    public function getGroup($group, $handle);

    /**
     * Get all of a groups assets, sorted by dependencies.
     *
     * @param string $group Group name
     *
     * @return string Assets content
     */
    public function renderGroup($group);

    /**
     * Remove a group asset, all of a groups assets, or all group assets.
     *
     * @param string $group  Group name
     * @param string $handle Asset name
     *
     * @return mixed Empty array or void
     */
    public function removeGroup($group = null, $handle = null);

    /**
     * @param string|array $key
     * @param mixed        $value
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function putVars($key, $value = null);

    /**
     * Remove a javascript vars.
     *
     * @return mixed Empty array or void
     */
    public function removeVars();

    /**
     * @return string
     */
    public function renderVars();

    /**
     * @return $this
     */
    public function clear();
}

<?php
namespace KodiCMS\Assets\Contracts;

use KodiCMS\Assets\Asset;
use KodiCMS\Assets\Assets;
use KodiCMS\Assets\Attributes;
use KodiCMS\Assets\Dependencies;
use KodiCMS\Assets\Group;
use KodiCMS\Assets\Remove;
use KodiCMS\Assets\Whether;

interface AssetsInterface
{
    /**
     * @param string|array $names
     *
     * @return bool
     */
    public function loadPackage($names);

    /**
     * CSS wrapper.
     *
     * Gets or sets CSS assets
     *
     * @param   string   Asset name.
     * @param   string   Asset source
     * @param   mixed    Dependencies
     * @param   array    Attributes for the <link /> element
     *
     * @return mixed Setting returns asset array, getting returns asset HTML
     */
    public function addCss($handle = null, $src = null, $dependency = null, array $attributes = []);

    /**
     * Get a single CSS asset.
     *
     * @param   string   Asset name
     *
     * @return string Asset HTML
     */
    public function getCss($handle);

    /**
     * Get all CSS assets, sorted by dependencies.
     *
     * @return string Asset HTML
     */
    public function getCssList();

    /**
     * Remove a CSS asset, or all.
     *
     * @param   mixed   Asset name, or `NULL` to remove all
     *
     * @return mixed Empty array or void
     */
    public function removeCss($handle = null);

    /**
     * Javascript wrapper.
     *
     * Gets or sets javascript assets
     *
     * @param bool|string $handle
     * @param               string   Asset source
     * @param               mixed    Dependencies
     * @param               bool     Whether to show in header or footer
     *
     * @return mixed Setting returns asset array, getting returns asset HTML
     */
    public function addJs($handle = false, $src = null, $dependency = null, $footer = false);

    /**
     * Get a single javascript asset.
     *
     * @param   string   Asset name
     *
     * @return string Asset HTML
     */
    public function getJs($handle);

    /**
     * Get all javascript assets of section (header or footer).
     *
     * @param   bool   FALSE for head, TRUE for footer
     *
     * @return string Asset HTML
     */
    public function getJsList($footer = false);

    /**
     * Remove a javascript asset, or all.
     *
     * @param   mixed   Remove all if `NULL`, section if `TRUE` or `FALSE`, asset if `string`
     *
     * @return mixed Empty array or void
     */
    public function removeJs($handle = null);

    /**
     * Group wrapper.
     *
     * @param   string   Group name
     * @param   string   Asset name
     * @param   string   Asset content
     * @param   mixed    Dependencies
     *
     * @return mixed Setting returns asset array, getting returns asset content
     */
    public function group($group, $handle = null, $content = null, $dependency = null);

    /**
     * Get a single group asset.
     *
     * @param   string   Group name
     * @param   string   Asset name
     *
     * @return string Asset content
     */
    public function getGroup($group, $handle);

    /**
     * Get all of a groups assets, sorted by dependencies.
     *
     * @param  string   Group name
     *
     * @return string Assets content
     */
    public function allGroup($group);

    /**
     * Remove a group asset, all of a groups assets, or all group assets.
     *
     * @param   string   Group name
     * @param   string   Asset name
     *
     * @return mixed Empty array or void
     */
    public function removeGroup($group = null, $handle = null);

    /**
     * @return $this
     */
    public function removePackages();

    /**
     * @return $this
     */
    public function clear();

    /**
     * @return string
     */
    public function render();
}
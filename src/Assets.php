<?php

namespace KodiCMS\Assets;

use KodiCMS\Assets\Contracts\AssetElementInterface;
use KodiCMS\Assets\Contracts\AssetsInterface;
use KodiCMS\Assets\Contracts\PackageInterface;
use KodiCMS\Assets\Contracts\PackageManagerInterface;

class Assets implements AssetsInterface
{
    /**
     * @var PackageInterface[]
     */
    protected $packages = [];

    /**
     * @var AssetElementInterface[] CSS assets
     */
    protected $css = [];

    /**
     * @var AssetElementInterface[] Javascript assets
     */
    protected $js = [];

    /**
     * @var array Other asset groups (meta data, links, etc...)
     */
    protected $groups = [];

    /**
     * @var bool
     */
    protected $includeDependency = false;

    /**
     * @var PackageManagerInterface
     */
    protected $manager;

    /**
     * Assets constructor.
     *
     * @param PackageManagerInterface $manager
     */
    public function __construct(PackageManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return PackageManagerInterface
     */
    public function packageManager()
    {
        return $this->manager;
    }

    /**
     * @param string|array $names
     *
     * @return $this
     */
    public function loadPackage($names)
    {
        $names = is_array($names) ? $names : func_get_args();

        foreach ($names as $name) {
            if (!array_key_exists($name, $this->packages)) {

                /** @var PackageInterface $package */
                if (!is_null($package = $this->manager->load($name))) {
                    $this->packages[$name] = $package;

                    if ($package->hasDependencies()) {
                        foreach ($package->getDependencies() as $dependency) {
                            $this->loadPackage($dependency);
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function loadedPackages()
    {
        return array_keys($this->packages);
    }

    /**
     * @return $this
     */
    public function removePackages()
    {
        $this->packages = [];

        return $this;
    }

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

        return $this->css[$handle] = new Css($handle, $src, $dependency, $attributes);
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
     * Get a single CSS asset.
     *
     * @param string $handle Asset name
     *
     * @return string Asset HTML
     */
    public function getCss($handle)
    {
        return (string) array_get($this->css, $handle);
    }

    /**
     * Get all CSS assets, sorted by dependencies.
     *
     * @return string Asset HTML
     */
    public function getCssList()
    {
        $this->loadPackageCss();

        if (empty($this->css)) {
            return PHP_EOL;
        }

        foreach ($this->sort($this->css) as $handle => $data) {
            $assets[] = $this->getCss($handle);
        }

        return implode(PHP_EOL, $assets);
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
            return $this->css = [];
        }

        unset($this->css[$handle]);
    }

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
        return $this->js[$handle] = new Javascript($handle, $src, $dependency, $footer);
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
        return (string) array_get($this->js, $handle);
    }

    /**
     * Get all javascript assets of section (header or footer).
     *
     * @param bool $footer FALSE for head, TRUE for footer
     *
     * @return string Asset HTML
     */
    public function getJsList($footer = false)
    {
        $this->loadPackageJs();

        if (empty($this->js)) {
            return PHP_EOL;
        }

        /** @var JavaScript[] $assets */
        $assets = [];

        foreach ($this->js as $javaScript) {
            if ($javaScript->isFooter() === $footer) {
                $assets[$javaScript->getHandle()] = $javaScript;
            }
        }

        if (empty($assets)) {
            return false;
        }

        foreach ($this->sort($assets) as $javaScript) {
            $sorted[] = $this->getJs($javaScript->getHandle());
        }

        return implode(PHP_EOL, $sorted);
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
            return $this->js = [];
        }

        if (is_bool($handle)) {
            foreach ($this->js as $i => $javaScript) {
                if ($javaScript->isFooter() === $handle) {
                    unset($this->js[$i]);
                }
            }

            return;
        }

        unset($this->js[$handle]);
    }

    /**
     * Group wrapper.
     *
     * @param string $group   Group name
     * @param string $handle  Asset name
     * @param string $content Asset content
     *
     * @return $this
     */
    public function group($group, $handle = null, $content = null)
    {
        $this->groups[$group][$handle] = $content;

        return $this;
    }

    /**
     * Get a single group asset.
     *
     * @param string $group  Group name
     * @param string $handle Asset name
     *
     * @return string|null Asset content
     */
    public function getGroup($group, $handle)
    {
        return array_get($this->groups, $group.'.'.$handle);
    }

    /**
     * Get all of a groups assets, sorted by dependencies.
     *
     * @param string $group Group name
     *
     * @return string Assets content
     */
    public function allGroup($group)
    {
        if (!isset($this->groups[$group])) {
            return PHP_EOL;
        }

        foreach ($this->groups[$group] as $handle => $data) {
            $assets[] = $this->getGroup($group, $handle);
        }

        return implode(PHP_EOL, $assets);
    }

    /**
     * Remove a group asset, all of a groups assets, or all group assets.
     *
     * @param string $group  Group name
     * @param string $handle Asset name
     *
     * @return mixed Empty array or void
     */
    public function removeGroup($group = null, $handle = null)
    {
        if (is_null($group)) {
            return $this->groups = [];
        }

        if (is_null($handle)) {
            unset($this->groups[$group]);

            return;
        }

        unset($this->groups[$group][$handle]);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->removeCss();
        $this->removeJs();
        $this->removeGroup();
        $this->removePackages();

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->getCssList().PHP_EOL.$this->getJsList();
    }

    protected function loadPackageJs()
    {
        foreach ($this->packages as $package) {
            foreach ($package->getJs($this->includeDependency) as $javaScript) {
                $this->js[$javaScript->getHandle()] = $javaScript;
            }
        }
    }

    protected function loadPackageCss()
    {
        foreach ($this->packages as $package) {
            foreach ($package->getCss() as $css) {
                $this->css[$css->getHandle()] = $css;
            }
        }
    }

    /**
     * Sorts assets based on dependencies.
     *
     * @param AssetElementInterface[] $assets Array of assets
     *
     * @return AssetElementInterface[] Sorted array of assets
     */
    protected function sort($assets)
    {
        $original = $assets;
        $sorted = [];

        while (count($assets) > 0) {
            foreach ($assets as $handle => $asset) {
                // No dependencies anymore, add it to sorted
                if (!$asset->hasDependency()) {
                    $sorted[$handle] = $asset;
                    unset($assets[$handle]);
                } else {
                    foreach ($asset->getDependency() as $dep) {
                        // Remove dependency if doesn't exist, if its dependent on itself,
                        // or if the dependent is dependent on it
                        if (!isset($original[$dep]) or $dep === $handle or (isset($assets[$dep]) and $assets[$dep]->hasDependency($handle))) {
                            $assets[$handle]->removeDependency($dep);
                            continue;
                        }

                        // This dependency hasn't been sorted yet
                        if (!isset($sorted[$dep])) {
                            continue;
                        }

                        // This dependency is taken care of, remove from list
                        $assets[$handle]->removeDependency($dep);
                    }
                }
            }
        }

        return $sorted;
    }
}

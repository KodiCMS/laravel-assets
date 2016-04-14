<?php

namespace KodiCMS\Assets;

use KodiCMS\Assets\Contracts\AssetElementInterface;

class Assets
{
    /**
     * @var Package[]
     */
    protected $packages = [];

    /**
     * @var Css[] CSS assets
     */
    protected $css = [];

    /**
     * @var JavaScript[] Javascript assets
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
     * @param string|array $names
     *
     * @return bool
     */
    public function loadPackage($names)
    {
        $names = is_array($names) ? $names : func_get_args();

        foreach ($names as $name) {
            if (! array_key_exists($name, $this->packages)) {

                /** @var Package $package */
                if (! is_null($package = app('assets.packages')->load($name))) {
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
    public function addCss($handle = null, $src = null, $dependency = null, array $attributes = [])
    {
        // Set default media attribute
        if (! isset($attributes['media'])) {
            $attributes['media'] = 'all';
        }

        return $this->css[$handle] = new Css($handle, $src, $dependency, $attributes);
    }

    /**
     * Get a single CSS asset.
     *
     * @param   string   Asset name
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
            return '';
        }

        foreach ($this->sort($this->css) as $handle => $data) {
            $assets[] = $this->getCss($handle);
        }

        return implode('', $assets);
    }

    /**
     * Remove a CSS asset, or all.
     *
     * @param   mixed   Asset name, or `NULL` to remove all
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
     * @param bool|string   $handle
     * @param               string   Asset source
     * @param               mixed    Dependencies
     * @param               bool     Whether to show in header or footer
     *
     * @return mixed Setting returns asset array, getting returns asset HTML
     */
    public function addJs($handle = false, $src = null, $dependency = null, $footer = false)
    {
        return $this->js[$handle] = new Javascript($handle, $src, $dependency, $footer);
    }

    /**
     * Get a single javascript asset.
     *
     * @param   string   Asset name
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
     * @param   bool   FALSE for head, TRUE for footer
     *
     * @return string Asset HTML
     */
    public function getJsList($footer = false)
    {
        $this->loadPackageJs();

        if (empty($this->js)) {
            return '';
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

        return implode('', $sorted);
    }

    /**
     * Remove a javascript asset, or all.
     *
     * @param   mixed   Remove all if `NULL`, section if `TRUE` or `FALSE`, asset if `string`
     *
     * @return mixed Empty array or void
     */
    public function removeJs($handle = null)
    {
        if (is_null($handle)) {
            return $this->js = [];
        }

        if (is_bool($handle)) {
            foreach ($this->js as $javaScript) {
                if ($javaScript->isFooter() === $handle) {
                    unset($this->js[$handle]);
                }
            }

            return;
        }

        unset($this->js[$handle]);
    }

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
    public function group($group, $handle = null, $content = null, $dependency = null)
    {
        return $this->groups[$group][$handle] = ['content' => $content, 'deps' => (array) $dependency];
    }

    /**
     * Get a single group asset.
     *
     * @param   string   Group name
     * @param   string   Asset name
     *
     * @return string Asset content
     */
    public function getGroup($group, $handle)
    {
        if (! isset($this->groups[$group]) or ! isset($this->groups[$group][$handle])) {
            return false;
        }

        return $this->groups[$group][$handle]['content'];
    }

    /**
     * Get all of a groups assets, sorted by dependencies.
     *
     * @param  string   Group name
     *
     * @return string Assets content
     */
    public function allGroup($group)
    {
        if (! isset($this->groups[$group])) {
            return '';
        }

        foreach ($this->groups[$group] as $handle => $data) {
            $assets[] = $this->getGroup($group, $handle);
        }

        return implode('', $assets);
    }

    /**
     * Remove a group asset, all of a groups assets, or all group assets.
     *
     * @param   string   Group name
     * @param   string   Asset name
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
    public function removePackages()
    {
        $this->packages = [];

        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->removeCss();
        $this->removeJs();
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
                if (! $asset->hasDependency()) {
                    $sorted[$handle] = $asset;
                    unset($assets[$handle]);
                } else {
                    foreach ($asset->getDependency() as $dep) {
                        // Remove dependency if doesn't exist, if its dependent on itself,
                        // or if the dependent is dependent on it
                        if (! isset($original[$dep]) or $dep === $handle or (isset($assets[$dep]) and $assets[$dep]->hasDependency($handle))) {
                            $assets[$handle]->removeDependency($dep);
                            continue;
                        }

                        // This dependency hasn't been sorted yet
                        if (! isset($sorted[$dep])) {
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

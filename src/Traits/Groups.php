<?php

namespace KodiCMS\Assets\Traits;

trait Groups
{
    /**
     * @var array Other asset groups (meta data, links, etc...)
     */
    protected $groups = [];

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
     * Get all of a groups assets, sorted by dependencies.
     *
     * @param string $group Group name
     *
     * @return string Assets content
     */
    public function renderGroup($group)
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
     * @deprecated
     *
     * @param string $group Group name
     *
     * @return string|void
     */
    public function allGroup($group)
    {
        return $this->renderGroup($group);
    }
}

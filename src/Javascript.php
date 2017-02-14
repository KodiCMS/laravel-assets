<?php

namespace KodiCMS\Assets;

use KodiCMS\Assets\Contracts\AssetElementInterface;

class Javascript extends AssetElement
{
    /**
     * @var bool
     */
    protected $footer = false;

    /**
     * @param string $handle
     * @param string $src
     * @param array  $dependency
     * @param bool   $footer
     * @param array  $attributes
     */
    public function __construct($handle, $src, $dependency = [], $footer = false, array $attributes = [])
    {
        parent::__construct($handle, $src, $dependency, $attributes);
        $this->setFooter($footer);
    }

    /**
     * @param AssetElementInterface $element
     *
     * @return void
     */
    public function resolveDependency(AssetElementInterface $element)
    {
        if ($element->isFooter()) {
            $this->setFooter(true);
        }

        parent::resolveDependency($element);
    }

    /**
     * @param bool $footer
     */
    public function setFooter($footer)
    {
        $this->footer = (bool) $footer;
    }

    /**
     * @return bool
     */
    public function isFooter()
    {
        return $this->footer;
    }

    /**
     * @return string
     */
    public function render()
    {
        return (new Html())->script($this->getSrc(), $this->getAttributes());
    }
}

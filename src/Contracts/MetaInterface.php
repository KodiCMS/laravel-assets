<?php
namespace KodiCMS\Assets\Contracts;

use KodiCMS\Assets\Meta;

interface MetaInterface
{
    /**
     * @param MetaDataInterface $data
     *
     * @return $this
     */
    public function setMetaData(MetaDataInterface $data);

    /**
     * @param string $title
     *
     * @return mixed
     */
    public function setTitle($title);

    /**
     * @param string $description
     *
     * @return Meta
     */
    public function setMetaDescription($description);

    /**
     * @param string|array $keywords
     *
     * @return Meta
     */
    public function setMetaKeywords($keywords);

    /**
     * @param string $robots
     *
     * @return Meta
     */
    public function setMetaRobots($robots);

    /**
     * @param SocialMediaTagsInterface $socialTags
     *
     * @return Meta
     */
    public function addSocialTags(SocialMediaTagsInterface $socialTags);

    /**
     * @param array $attributes
     * @param null|string $group
     *
     * @return $this
     */
    public function addMeta(array $attributes, $group = null);

    /**
     * @param string $filename [default: css/all.css]
     * @param null|string $dependency
     * @param array|null $attributes
     *
     * @return $this
     */
    public function addCssElixir($filename = 'css/all.css', $dependency = null, array $attributes = []);

    /**
     * @param string $handle
     * @param string $src
     * @param null|string $dependency
     * @param null|array $attributes
     *
     * @return $this
     */
    public function addCss($handle, $src, $dependency = null, array $attributes = []);

    /**
     * @param null|string $handle
     *
     * @return $this
     */
    public function removeCss($handle = null);

    /**
     * @param string $filename [default: js/app.js]
     * @param null|string $dependency
     * @param bool $footer
     *
     * @return $this
     */
    public function addJsElixir($filename = 'js/app.js', $dependency = null, $footer = false);

    /**
     * @param string $handle
     * @param string $src
     * @param null|string $dependency
     * @param bool $footer
     *
     * @return $this
     */
    public function AddJs($handle, $src, $dependency = null, $footer = false);

    /**
     * @param null|string $handle
     *
     * @return $this
     */
    public function removeJs($handle = null);

    /**
     * Указание favicon.
     *
     * @param string $url
     * @param string $rel
     *
     * @return $this
     */
    public function setFavicon($url, $rel = 'shortcut icon');

    /**
     * @param string|array $names
     *
     * @return $this
     */
    public function loadPackage($names);

    /**
     * @param string $handle
     * @param string $content
     * @param array $params
     * @param null|string $dependency
     *
     * @return $this
     */
    public function addToGroup($handle, $content, $params = [], $dependency = null);

    /**
     * @param string|null $handle
     *
     * @return $this
     */
    public function removeFromGroup($handle = null);

    /**
     * @return string
     */
    public function render();
}
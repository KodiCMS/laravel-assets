<?php

namespace KodiCMS\Assets\Contracts;

use Illuminate\Contracts\Support\Renderable;

interface MetaInterface extends Renderable
{
    /**
     * @return AssetsInterface
     */
    public function assets();

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
     * @return $this
     */
    public function setMetaDescription($description);

    /**
     * @param string|array $keywords
     *
     * @return $this
     */
    public function setMetaKeywords($keywords);

    /**
     * @param string $robots
     *
     * @return $this
     */
    public function setMetaRobots($robots);

    /**
     * @param SocialMediaTagsInterface $socialTags
     *
     * @return $this
     */
    public function addSocialTags(SocialMediaTagsInterface $socialTags);

    /**
     * @param array       $attributes
     * @param null|string $group
     *
     * @return $this
     */
    public function addMeta(array $attributes, $group = null);

    /**
     * @param string $url
     * @param string $rel
     * @param string $type
     *
     * @return $this
     */
    public function setFavicon($url, $rel = 'shortcut icon', $type = 'image/x-icon');

    /**
     * @param string            $handle
     * @param string            $content
     * @param array             $params
     * @param null|string|array $dependency
     *
     * @return $this
     */
    public function addTagToGroup($handle, $content, $params = [], $dependency = null);

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

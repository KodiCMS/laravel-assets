<?php

namespace KodiCMS\Assets\Contracts;

interface SocialMediaTagsInterface
{
    /**
     * @return string
     */
    public function getOgTitle();

    /**
     * @return string
     */
    public function getOgDescription();

    /**
     * @return string
     */
    public function getOgImage();

    /**
     * @return string
     */
    public function getOgUrl();

    /**
     * @return string
     */
    public function getOgType();

    /**
     * @return string
     */
    public function getOgPublishedTime();

    /**
     * @return string
     */
    public function getOgModifiedTime();

    /**
     * @return string
     */
    public function getOgTags();
}

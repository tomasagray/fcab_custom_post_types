<?php

namespace fcab\view;

use WP_Post;

interface Page
{
    public function getUrl(): string;

    public function getTemplate(): string;

    public function getTitle(): string;

    public function setTitle($title): Page;

    public function setContent($content): Page;

    public function setTemplate($template): Page;

    /**
     * Get a WP_Post build using virtual Page object
     *
     * @return WP_Post
     */
    public function asWpPost(): WP_Post;
}

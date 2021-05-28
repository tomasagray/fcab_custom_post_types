<?php


namespace fcab\view;


use SplObjectStorage;
use WP;
use WP_Post;

abstract class PageController implements Controller
{

    protected SplObjectStorage $pages;
    protected TemplateLoader $loader;
    protected Page $matched;

    /**
     * @inheritDoc
     */
    public function dispatch(bool $bool, WP $wp): bool
    {
        // todo - fix this
        $requestMatched = $this->checkRequest(); // warning!!! - modifies $this->matched
        $isPageType = $this->isPageType();
        if ($requestMatched && $isPageType) {
            $this->loader->init($this->matched);
            $wp->virtual_page = $this->matched;
            do_action('parse_request', $wp);
            $this->setupQuery();
            do_action('wp', $wp);
            $this->loader->load();
            $this->handleExit();
        }
        return $bool;
    }

    /**
     * @inheritDoc
     */
    public function addPage(Page $page): Page
    {
        $this->pages->attach($page);
        return $page;
    }

    public function handleExit(): void
    {
        exit();
    }

    abstract public function isPageType(): bool;

    protected function checkRequest(): bool
    {
        $this->pages->rewind();
        $path =  add_query_arg(NULL, NULL);
        while ($this->pages->valid()) {
            $url_pattern = trim($this->pages->current()->getUrl(), '/');
            $url_pattern = "/$url_pattern/i"; // format as regex
            $url_matches = preg_match($url_pattern, $path);
            if ($url_matches === 1) {
                $this->matched = $this->pages->current();
                return true;
            }
            $this->pages->next();
        }
        return false;
    }

    protected function getPathInfo()
    {
        $home_path = parse_url(home_url(), PHP_URL_PATH);
        return preg_replace("#^/?$home_path/#", '/', esc_url(add_query_arg(array())));
    }

    protected function setupQuery(): void
    {
        global $wp_query;
        $wp_query->init();
        $wp_query->is_page = TRUE;
        $wp_query->is_singular = TRUE;
        $wp_query->is_home = FALSE;
        $wp_query->found_posts = 1;
        $wp_query->post_count = 1;
        $wp_query->max_num_pages = 1;
        $posts = (array)apply_filters(
            'the_posts', array($this->matched->asWpPost()), $wp_query
        );
        $post = $posts[0];
        $wp_query->posts = $posts;
        $wp_query->post = $post;
        $wp_query->queried_object = $post;
        $GLOBALS['post'] = $post;
        $wp_query->virtual_page = $post instanceof WP_Post && isset($post->is_virtual)
            ? $this->matched
            : NULL;
    }

}

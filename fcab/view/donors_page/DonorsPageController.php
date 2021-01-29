<?php


namespace fcab\view\donors_page;


use fcab\view\Page;
use fcab\view\TemplateLoader;
use fcab\view\Controller;
use SplObjectStorage;
use WP;
use WP_Post;

const FCAB_DONOR_PAGES = 'fcab_donor_pages';

class DonorsPageController implements Controller
{

    private SplObjectStorage $pages;
    private TemplateLoader $loader;
    private Page $matched;

    public function __construct(TemplateLoader $loader)
    {
        $this->pages = new SplObjectStorage();
        $this->loader = $loader;
    }

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        do_action(FCAB_DONOR_PAGES, $this);
    }

    /**
     * @inheritDoc
     */
    public function addPage(Page $page): Page
    {
        $this->pages->attach($page);
        return $page;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(bool $bool, WP $wp): bool
    {
        if ($this->checkRequest() && $this->matched instanceof DonorsPage) {
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

    private function checkRequest(): bool
    {
        $this->pages->rewind();
        $path = trim($this->getPathInfo(), '/');
        while ($this->pages->valid()) {
            if (trim($this->pages->current()->getUrl(), '/') === $path) {
                $this->matched = $this->pages->current();
                return TRUE;
            }
            $this->pages->next();
        }
        return false;
    }

    private function getPathInfo()
    {
        $home_path = parse_url(home_url(), PHP_URL_PATH);
        return preg_replace("#^/?{$home_path}/#", '/', esc_url(add_query_arg(array())));
    }

    private function setupQuery(): void
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

    public function handleExit(): void
    {
        exit();
    }
}

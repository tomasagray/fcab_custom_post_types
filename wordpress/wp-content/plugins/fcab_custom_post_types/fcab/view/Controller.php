<?php


namespace fcab\view;


use WP;

interface Controller
{
    /**
     * Init the controller, fires the hook that allows consumer to add pages
     */
    public function init(): void;

    /**
     * Register a page object in the controller
     *
     * @param Page $page
     * @return Page
     */
    public function addPage(Page $page): Page;

    /**
     * Run on 'do_parse_request' and if the request is for one of the registered pages
     * setup global variables, fire core hooks, requires page template and exit.
     *
     * @param boolean $bool The boolean flag value passed by 'do_parse_request'
     * @param WP $wp The global wp object passed by 'do_parse_request'
     * @return bool Boolean flag, returned to WP
     */
    public function dispatch(bool $bool, WP $wp): bool;
}

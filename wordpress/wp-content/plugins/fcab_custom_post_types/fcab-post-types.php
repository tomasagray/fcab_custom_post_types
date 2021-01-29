<?php

namespace fcab;

use fcab\view\Controller;
use fcab\view\donors_page\DonorsPage;
use fcab\view\donors_page\DonorsPageController;
use fcab\view\donors_page\DonorsPageTemplateLoader;
use WP_Post;
use WP_Query;
use const fcab\view\donors_page\FCAB_DONOR_PAGES;

/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 0.2
 * Author: Tomás Gray
 **/


require_once 'fcab\model\FCABDonor.php';
require_once 'fcab\model\FCABDonorMetaBox.php';
require_once 'fcab\view\Page.php';
require_once 'fcab\view\Controller.php';
require_once 'fcab\view\TemplateLoader.php';
require_once 'fcab\view\donors_page\DonorsPage.php';
require_once 'fcab\view\donors_page\DonorsPageController.php';
require_once 'fcab\view\donors_page\DonorsPageTemplateLoader.php';

const DOMAIN = 'fcab_cpt';
const DONATION_FIELD_NAME = 'fcab_cpt_donor_total_donations';




// ============ CONTROLLER = ============================

$controller = new DonorsPageController(new DonorsPageTemplateLoader());
add_action('init', [$controller, 'init']);
add_filter('do_parse_request', [$controller, 'dispatch'], PHP_INT_MAX, 2);
add_action('loop_end', function (WP_Query $query) {
    if (isset($query->virtual_page) && !empty($query->virtual_page)) {
        $query->virtual_page = null;
    }
});

/**
 * @param WP_Query $wp_query
 * @param WP_Post $post
 * @return bool
 */
function isVirtualPage(WP_Query $wp_query, WP_Post $post): bool
{
    return isset($wp_query->virtual_page, $post->is_virtual)
        && $wp_query->is_page && $wp_query->virtual_page instanceof DonorsPage;
}

add_filter('the_permalink', function ($link) {
    global $post, $wp_query;
    if ($post->is_virtual && isVirtualPage($wp_query, $post)) {
        $link = home_url($wp_query->virtual_page->getUrl());
    }
    return $link;
});

add_action(FCAB_DONOR_PAGES, function (Controller $controller) {

    $controller->addPage(new DonorsPage('/donations'))
        ->setTitle('Donations')
        ->setTemplate('donors_page_template.php');
});

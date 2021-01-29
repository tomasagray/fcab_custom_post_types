<?php
namespace fcab;


use fcab\view\donors_page\DonorsPage;
use WP_Post;
use WP_Query;

/**
 * Helper function
 *
 * @param WP_Query $wp_query The loop query
 * @param WP_Post $post The post
 * @return bool It is or it isn't
 */
function isVirtualPage(WP_Query $wp_query, WP_Post $post): bool
{
    return isset($wp_query->virtual_page, $post->is_virtual)
        && $wp_query->is_page && $wp_query->virtual_page instanceof DonorsPage;
}

// Main hooks
add_filter('the_permalink', function ($link) {
    global $post, $wp_query;
    if ($post->is_virtual && isVirtualPage($wp_query, $post)) {
        $link = home_url($wp_query->virtual_page->getUrl());
    }
    return $link;
});
add_action('loop_end', function (WP_Query $query) {
    if (isset($query->virtual_page) && !empty($query->virtual_page)) {
        $query->virtual_page = null;
    }
});

// Hook sub-controllers
require_once 'fcab/view/donors_page/init-donors-controller.php';



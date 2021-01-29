<?php
namespace fcab\view\donors_page;

use fcab\view\Controller;

// Create page controller & template loader
$controller = new DonorsPageController(new DonorsPageTemplateLoader());
add_action('init', [$controller, 'init']);
add_filter('do_parse_request', [$controller, 'dispatch'], PHP_INT_MAX, 2);
add_action(FCAB_DONOR_PAGES, function (Controller $controller) {
    $controller->addPage(new DonorsPage('/donations'))
        ->setTitle('Donations')
        ->setTemplate('donors_page_template.php');
});

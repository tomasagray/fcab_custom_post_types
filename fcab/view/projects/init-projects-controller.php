<?php
namespace fcab\view\projects;

use fcab\view\Controller;
use fcab\view\PageTemplateloader;


function add_project_stylesheet(): void
{
    $plugins_url = plugins_url('project_page.css', __FILE__);
    wp_enqueue_style('fcab_project_stylesheet', $plugins_url);
}

// Create page controller & template loader
$controller = new ProjectsPageController(new PageTemplateLoader());
add_action('init', [$controller, 'init']);
add_action('wp_enqueue_scripts', 'fcab\view\projects\add_project_stylesheet');
add_filter('do_parse_request', [$controller, 'dispatch'], PHP_INT_MAX, 2);
add_action(FCAB_PROJECT_PAGES, function (Controller $controller) {
    // Projects Overview
    $controller->addPage(new ProjectsPage('(\/projects[-\w?&=%]*)$'))
        ->setTitle('Projects')
        ->setTemplate('projects/projects_page_template.php');
    // Project Detail page
    $controller->addPage(new ProjectsPage('[^wp\-content]\/projects\/[-._,\w]*'))
        ->setTitle('Project')
        ->setTemplate('projects/project_page_template.php');
});

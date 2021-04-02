<?php

namespace fcab;


/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 1.1
 * Author: Tomás Gray
 **/


const DOMAIN = 'fcab_cpt';
// Options
const FCAB_CPT_OPTIONS = 'fcab_cpt_options';
const FCAB_CPT_DONATION_PAGE_ID = 'fcab_cpt_donation_page_id';

require_once 'fcab/view/Page.php';
require_once 'fcab/view/CustomPage.php';
require_once 'fcab/view/Controller.php';
require_once 'fcab/view/PageController.php';
require_once 'fcab/view/TemplateLoader.php';
require_once 'fcab/view/PageTemplateLoader.php';
// Donors
require_once 'fcab/model/FCABDonor.php';
require_once 'fcab/model/FCABDonorMetaBox.php';
require_once 'fcab/view/donors/DonorsPage.php';
require_once 'fcab/view/donors/DonorsPageController.php';
// Projects
require_once 'fcab/model/FCABProject.php';
require_once 'fcab/view/projects/ProjectsPage.php';
require_once 'fcab/view/projects/ProjectsPageController.php';

require_once 'init-controllers.php';
require_once 'fcab/functions.php';


/**
 * Create plugin settings
 */
function create_settings()
{
    register_setting(FCAB_CPT_OPTIONS, FCAB_CPT_DONATION_PAGE_ID);
}
add_action('admin_init', 'fcab\create_settings');

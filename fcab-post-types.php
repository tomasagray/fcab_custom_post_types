<?php

namespace fcab;


use fcab\model\FCABDonor;

/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 0.2.1
 * Requires at least: 5.0
 * Tested up to: 5.8.1
 * Requires PHP: 7.4
 * Author: Tomás Gray
 **/


const DOMAIN = 'fcab_cpt';
// Options
const FCAB_CPT_OPTIONS = 'fcab_cpt_options';

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
require_once 'fcab/view/customfields/FCABDonationFields.php';
// Programs, Projects & Activities
require_once 'fcab/model/FCABProject.php';
require_once 'fcab/view/projects/ProjectsPage.php';
require_once 'fcab/view/projects/ProjectsPageController.php';
require_once 'fcab/model/FCABProgram.php';
require_once 'fcab/model/FCABActivity.php';
// Volunteers
require_once 'fcab/model/FCABVolunteer.php';

require_once 'init-controllers.php';
require_once 'init-custom-fields.php';
require_once 'fcab/settings.php';

require_once 'fcab/Log.php';

register_activation_hook( __FILE__, [ FCABDonor::class, 'create_donations_page' ] );

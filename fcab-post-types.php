<?php

namespace fcab;


/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 0.9a
 * Author: Tomás Gray
 **/


require_once 'fcab/model/FCABDonor.php';
require_once 'fcab/model/FCABDonorMetaBox.php';
require_once 'fcab/view/Page.php';
require_once 'fcab/view/Controller.php';
require_once 'fcab/view/TemplateLoader.php';
require_once 'fcab/view/donors_page/DonorsPage.php';
require_once 'fcab/view/donors_page/DonorsPageController.php';
require_once 'fcab/view/donors_page/DonorsPageTemplateLoader.php';

const DOMAIN = 'fcab_cpt';
const DONATION_FIELD_NAME = 'fcab_cpt_donor_total_donations';

require_once 'init-controllers.php';

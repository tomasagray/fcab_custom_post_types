<?php
namespace fcab;

/**
 * Plugin Name: FCAB Post Types
 * Description: Custom post types for the FCAB WordPress website
 * Version: 0.1
 * Author: Tomás Gray
 **/

require 'fcab\model\FCABDonor.php';
require 'fcab\model\FCABDonorMetaBox.php';

const DOMAIN = 'fcab_cpt';
const DONATION_FIELD_NAME = 'fcab_cpt_donor_total_donations';

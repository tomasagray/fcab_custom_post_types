<?php


function fcab_custom_post_types_options()
{
    add_options_page(
        'FCAB Custom Post Types Options',
        'FCAB Custom Post Types',
        'manage_options',
        'fcab_custom_post_types',
        'fcab_cpt_options_page');
}

function fcab_cpt_options_page()
{
    ?>
    <div>
        <h1>FCAB Options</h1>
    </div>
    <?php
}

add_action('admin_menu', 'fcab_custom_post_types_options');

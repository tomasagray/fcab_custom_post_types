<?php

use fcab\model\FCABProject;

$terms = get_terms(['taxonomy' => FCABProject::TAGS]);
//var_dump($terms);

$q_args = [
    'post_type' => FCABProject::POST_TYPE,
    'post_status' => 'publish',
    'posts_per_page' => -1,
];
// Get donors
$loop = new WP_Query($q_args);

get_header();

?>
    <h1 class="project-heading">Our Projects</h1>
    <div class="project-card-container">
        <?php
        $projects = $loop->get_posts();
        if (count($projects) > 0):
            foreach ($projects as $project):
                $link = get_post_permalink($project->ID);
                $thumb = get_the_post_thumbnail_url($project->ID);
                echo '<div class="project-card">';
                if ($thumb !== false) {
                    echo '<div class="project-card-image" style="background-image: url(\'' . $thumb . '\');">';
                    echo '</div>';
                }
                ?>
                <div class="project-card-description">
                    <h3><?php echo $project->post_title; ?></h3>
                    <p><?php echo $project->post_content; ?></p>
                    <a href="<?php echo get_post_permalink($project->ID); ?>" class="project-link">Learn more</a>
                </div>
                <?php
                echo '</div>';
            endforeach;
        else:
            echo '<p>There are currently no projects. Please check back soon.</p>';
        endif;
        ?>
    </div>
<?php
get_footer();

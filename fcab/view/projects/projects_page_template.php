<?php

use fcab\model\FCABProject;

function get_query_url(array $param): string
{
    $url = $_SERVER['REQUEST_URI'];
    if (strpos($url, '?')) {
        if (strpos($url, 'tag=')) {
            $new_param = 'tag=' . $param['tag'];
            $url = preg_replace('/tag=[\w]+/', $new_param, $url);
        } else {
            $url .= '&tag=' . $param['tag'];
        }
    } else {
        $url .= '?tag=' . $param['tag'];
    }
    return $url;
}

/**
 * @param $current_tag
 * @param array $q_args
 */
function get_tag_arg($current_tag, array &$q_args): void
{
    $q_args['tax_query'] = array([
        'taxonomy' => FCABProject::TAGS,
        'terms' => array($current_tag),
        'field' => 'name'
    ]);
}


$current_tag = null;
$terms = get_terms(['taxonomy' => FCABProject::TAGS]);

$q_args = [
    'post_type' => FCABProject::POST_TYPE,
    'post_status' => 'publish',
    'posts_per_page' => -1,
];
if (isset($_GET['tag'])) {
    $tag_param = $_GET['tag'];
    foreach ($terms as $term) {
        if ($term->name === $tag_param) {
            $current_tag = $term;
        }
    }
}
if ($current_tag !== null) {
    get_tag_arg($current_tag->name, $q_args);
}

// Get donors
$loop = new WP_Query($q_args);

get_header();

?>
    <h1 class="project-heading">Our Projects</h1>

<?php
echo '<div class="project-tags-container">';
foreach ($terms as $term) {
    $filter_url = get_query_url(['tag' => $term->name]);
    $parent_list = get_term_parents_list(
        $term->term_id,
        FCABProject::TAGS,
        ['separator' => '--', 'link' => false]
    );
    $term_parents = explode('--', $parent_list);
    if (!(in_array('Other', $term_parents, true))) {
        echo '<a class="project-sort-tag" href="' . $filter_url . '">' . $term->name . '</a>';
    }
}
echo '<a class="project-sort-tag" href="' . get_query_url(['tag' => 'Other']) . '">Other</a>';
echo '</div>';

if ($current_tag !== null) {
    echo '<div class="project-tag-data">';
    echo '<h3 class="project-tag-title">' . $current_tag->name . '</h3>';
    echo '<p class="project-tag-description">' . $current_tag->description . '</p>';
    echo '</div>';
}
?>
    <div class="project-card-container">
        <?php
        $projects = $loop->get_posts();
        if (count($projects) > 0):
            foreach ($projects as $project):
                if ($project->post_status === 'publish'):
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
                endif;
            endforeach;
        else:
            echo '<p>There are currently no projects. Please check back soon.</p>';
        endif;
        ?>
    </div>
<?php
get_footer();

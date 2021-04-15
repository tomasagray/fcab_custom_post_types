<?php

use fcab\model\FCABProject;

function get_query_url(array $param): string
{
    $url = $_SERVER['REQUEST_URI'];
    if (strpos($url, '?')) {
        if (strpos($url, 'tag=')) {
            $new_param = 'project-tag=' . $param['tag'];
            $url = preg_replace('/project-tag=[\w]+/', $new_param, $url);
        } else {
            $url .= '&project-tag=' . $param['tag'];
        }
    } else {
        $url .= '?project-tag=' . $param['tag'];
    }
    return $url;
}

/**
 * @param array $terms
 * @return array
 */
function get_query_args(array $terms): array
{
    global $current_tag;
    $q_args = [
        'post_type' => FCABProject::POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ];
    if (isset($_POST['project-tag'])) {
        $tag_param = $_POST['project-tag'];
        foreach ($terms as $term) {
            if ($term->name === $tag_param) {
                $current_tag = $term;
            }
        }
    }
    if ($current_tag !== null) {
        $q_args['tax_query'] = array([
            'taxonomy' => FCABProject::TAGS,
            'terms' => $current_tag,
            'field' => 'name'
        ]);
    }
    return $q_args;
}


$current_tag = null;
$terms = get_terms(['taxonomy' => FCABProject::TAGS]);
$q_args = get_query_args($terms);
// Get donors
$loop = new WP_Query($q_args);


get_header();
?>
    <h1 class="project-heading">Our Projects</h1>
    <script type="text/javascript">
        function submitTagFilter($tagName) {
            // Set hidden input
            $('#project-tag').val($tagName);
            // Submit form
            $('#filter-tags').submit();
        }
    </script>

<?php
echo '<div class="project-tags-container">';
echo '<form name="filter-tags" id="filter-tags" method="POST" action="' . $_SERVER['REQUEST_URI'] . '">';
echo '<input type="hidden" name="project-tag" id="project-tag" />';
foreach ($terms as $term) {
    $filter_url = get_query_url(['tag' => $term->name]);
    $parent_list = get_term_parents_list(
        $term->term_id,
        FCABProject::TAGS,
        ['separator' => '--', 'link' => false]
    );
    $term_parents = explode('--', $parent_list);
    if (!(in_array('Other', $term_parents, true))) {
        echo '<a class="project-sort-tag" href="Javascript:submitTagFilter(\'' . $term->name . '\');">' . $term->name . '</a>';
    }
}
echo '<a class="project-sort-tag" href="Javascript:submitTagFilter(\'Other\');">Other</a>';
echo '</form>';
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

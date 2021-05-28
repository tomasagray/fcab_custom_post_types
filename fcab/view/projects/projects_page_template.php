<?php

use fcab\model\FCABProject;

const PROJECTS_DISPLAYED = 6;


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
 * @param $current_tag
 * @return array
 */
function get_query_args($current_tag): array
{
    $q_args = [
        'post_type' => FCABProject::POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => PROJECTS_DISPLAYED,
        'order' => 'DESC',
        'orderby' => 'date'
    ];
    if ($current_tag !== null) {
        $q_args['tax_query'] = array([
            'taxonomy' => FCABProject::TAGS,
            'terms' => $current_tag,
            'field' => 'name'
        ]);
    }
    return $q_args;
}

/**
 * @param array $terms
 * @return mixed
 */
function get_current_tag(array $terms)
{
    $current_tag = null;
    if (isset($_POST['project-tag'])) {
        $tag_param = $_POST['project-tag'];
        foreach ($terms as $term) {
            if ($term->name === $tag_param) {
                $current_tag = $term;
            }
        }
    }
    return $current_tag;
}

/**
 * @param $terms
 * @param null $current_term
 */
function print_tags_menu($terms, $current_term = null): void
{
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
            echo '<a class="project-sort-tag';
            if ($term === $current_term) {
                echo ' current';
            }
            echo '" href="Javascript:submitTagFilter(\'' . $term->name . '\');">';
            echo $term->name . '</a>';
        }
    }
    echo '<a class="project-sort-tag" href="Javascript:submitTagFilter(\'Other\');">Other</a>';
    echo '</form>';
    echo '</div>';
}


$terms = get_terms(['taxonomy' => FCABProject::TAGS]);
$current_tag = get_current_tag($terms);
$q_args = get_query_args($current_tag);
$loop = new WP_Query($q_args);

get_header();
?>

    <div class="content-box">
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
        print_tags_menu($terms, $current_tag);

        if ($current_tag !== null) {
            echo '<div class="project-tag-data">';
            echo '<h2 class="project-tag-title">' . $current_tag->name . '</h2>';
            echo '<p class="project-tag-description">' . $current_tag->description . '</p>';
            echo '</div>';
        }
        ?>

        <h2>Current Projects</h2>

        <?php
        $projects = $loop->get_posts();
        if ($loop->have_posts()): ?>
            <div class="project-card-container">
                <?php while ($loop->have_posts()): $loop->the_post();
                    $project_id = get_the_ID();
                    if (get_post_status($project_id) === 'publish'):
                        $thumb = get_the_post_thumbnail_url(get_the_ID());
                        echo '<div class="project-card">';
                        if ($thumb !== false) {
                            echo '<div class="project-card-image" style="background-image: url(\'' . $thumb . '\');">';
                            echo '</div>';
                        }
                        ?>
                        <div class="project-card-description">
                            <h3 class="project-title"><?php the_title(); ?></h3>
                            <p class="project-description">
                                <?php echo wp_strip_all_tags(wp_trim_excerpt('', $project_id), true); ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" class="project-link">Learn more</a>
                        </div>
                        <?php
                        echo '</div>';
                    endif;
                endwhile; ?>
            </div>
            <?php if (get_previous_posts_link()): ?>
                <div class="nav-previous alignleft"><?php previous_posts_link('Prev.'); ?></div>
            <?php endif; ?>
            <div class="nav-next alignright"><?php next_posts_link('Next'); ?></div>

        <?php else:
            echo '<p>There are currently no projects. Please check back soon.</p>';
        endif;
        ?>
    </div>
<?php
get_footer();

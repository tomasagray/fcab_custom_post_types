<?php

use fcab\model\FCABProject;

get_header();

$post_id = url_to_postid($_SERVER['REQUEST_URI']);
$post = get_post($post_id);
$tags = wp_get_post_terms($post_id, FCABProject::TAGS);
?>
    <div class="project-container">
        <div class="project-heading">
            <h1><?php echo $post->post_title; ?></h1>
            <?php
            if (count($tags) > 0):
                echo '<div class="tag-container">';
                foreach ($tags as $tag):
                    ?>
                    <span class="project-tag"
                          style="background-color: peachpuff; color: sienna; padding: 5px; margin-right: 10px;">
                <?php echo $tag->name; ?>
            </span>
                <?php
                endforeach;
                echo '</div>';
            endif;
            ?>
        </div>

        <div class="project-content">
            <?php
            $thumb = get_the_post_thumbnail_url($post_id);
            if ($thumb !== false) {
                echo '<img src="' . $thumb . '" alt="Project featured image" class="project-featured-image" class="project-featured-image"/>';
            }
            $content = apply_filters('the_content', $post->post_content);
            echo $content;
            ?>
        </div>
    </div>
<?php
get_footer();

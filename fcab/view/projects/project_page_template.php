<?php

use fcab\model\FCABProject;

get_header();

$post_id = url_to_postid($_SERVER['REQUEST_URI']);
$post = get_post($post_id);
if ($post === null || $post->post_status !== 'publish') {
    echo '<p>This project does not exist.</p>';
    exit(0);
}
$tags = wp_get_post_terms($post_id, FCABProject::TAGS);
?>
    <style>
        h2,
        h3,
        h4,
        h5 {
            color: forestgreen !important;
        }
    </style>
    <div class="content-box">
        <div class="project-container">
            <div class="project-heading">
                <h1><?php echo $post->post_title; ?></h1>
                <?php if (count($tags) > 0): ?>
                    <div class="project-tag-container">
                        <?php foreach ($tags as $tag): ?>
                            <span class="project-tag">
                        <?php echo $tag->name; ?>
                    </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
    </div>
<?php
get_footer();

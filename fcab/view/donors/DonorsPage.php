<?php


namespace fcab\view\donors;


use fcab\view\CustomPage;
use fcab\view\Page;
use WP_Post;

class DonorsPage extends CustomPage
{
    public function __construct($url, $title = 'Untitled Donor Page', $template = 'page.php')
    {
        $this->url = filter_var($url, FILTER_SANITIZE_URL);
        $this->setTitle($title);
        $this->setTemplate($template);
    }
}

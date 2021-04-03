<?php


namespace fcab\view\projects;


use fcab\view\CustomPage;


class ProjectsPage extends CustomPage
{

    public function __construct($url, $title = 'Untitled Project Page', $template = 'page.php')
    {
        $this->url = filter_var($url, FILTER_SANITIZE_URL);
        $this->setTitle($title);
        $this->setTemplate($template);
    }
}

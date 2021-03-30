<?php


namespace fcab\view\projects;


use fcab\view\PageController;

const FCAB_PROJECT_PAGES = 'fcab_project_pages';

class ProjectsPageController extends PageController
{

    public function init(): void
    {
        do_action(FCAB_PROJECT_PAGES, $this);
    }

    public function isPageType(): bool
    {
        return isset($this->matched) && $this->matched !== null && $this->matched instanceof ProjectsPage;
    }
}

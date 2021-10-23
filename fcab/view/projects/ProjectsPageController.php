<?php


namespace fcab\view\projects;


use fcab\view\PageController;
use fcab\view\TemplateLoader;
use SplObjectStorage;

const FCAB_PROJECT_PAGES = 'fcab_project_pages';

class ProjectsPageController extends PageController
{
    public function __construct(TemplateLoader $loader)
    {
        $this->pages = new SplObjectStorage();
        $this->loader = $loader;
    }

    public function init(): void
    {
        do_action(FCAB_PROJECT_PAGES, $this);
    }

    public function isPageType(): bool
    {
        return isset($this->matched) && $this->matched instanceof ProjectsPage;
    }
}

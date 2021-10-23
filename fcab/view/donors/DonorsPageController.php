<?php


namespace fcab\view\donors;


use fcab\view\PageController;
use fcab\view\TemplateLoader;
use SplObjectStorage;

const FCAB_DONOR_PAGES = 'fcab_donor_pages';

class DonorsPageController extends PageController
{

    public function __construct(TemplateLoader $loader)
    {
        $this->pages = new SplObjectStorage();
        $this->loader = $loader;
    }

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        do_action(FCAB_DONOR_PAGES, $this);
    }

    /**
     * @return bool
     */
    public function isPageType(): bool
    {
        return isset($this->matched) && $this->matched instanceof DonorsPage;
    }
}

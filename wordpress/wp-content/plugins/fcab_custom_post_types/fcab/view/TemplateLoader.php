<?php


namespace fcab\view;


interface TemplateLoader
{
    /**
     * Setup loader for a page objects
     *
     * @param Page $page matched virtual page
     */
    public function init(Page $page): void;

    /**
     * Trigger core and custom hooks to filter templates,
     * then load the found template.
     */
    public function load(): void;
}

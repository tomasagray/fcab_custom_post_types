<?php


namespace fcab\view;


class PageTemplateLoader implements TemplateLoader
{

    private Page $page;
    private array $templates;

    /**
     * @inheritDoc
     */
    public function init(Page $page): void
    {
        $this->templates = wp_parse_args(['page.php', 'index.php'], (array)$page->getTemplate());
        $this->page = $page;
    }

    /**
     * @inheritDoc
     */
    public function load(): void
    {
        do_action('template_redirect');
        $template = locate_template(array_filter($this->templates));
        $filtered = apply_filters('template_include', $template);
        if (empty($filtered) || file_exists($filtered)) {
            $template = $filtered;
        }
        if (!empty($template) && file_exists($template)) {
            require_once $template;
        }
    }
}

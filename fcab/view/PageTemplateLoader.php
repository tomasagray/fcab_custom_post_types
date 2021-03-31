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
        $this->templates = wp_parse_args((array)$page->getTemplate(), ['page.php', 'index.php']);
        $this->page = $page;
    }

    /**
     * @inheritDoc
     */
    public function load(): void
    {
        // todo - re-enable this
//        var_dump($this->templates);
//        $template = locate_template(array_filter($this->templates));
//        var_dump($template);
        do_action('template_redirect');
        $template = $this->page->getTemplate();
        require_once $template;
//        $filtered = apply_filters('template_include', $template);
//        var_dump($filtered);
//        if (empty($filtered) || file_exists($filtered)) {
//            $template = $filtered;
//        }
//        if (!empty($template) && file_exists($template)) {
//            require_once $template;
//        }
//        echo "Template: {$template}";
    }
}

<?php


namespace fcab\view\donors_page;


use fcab\view\Page;
use fcab\view\TemplateLoader;

class DonorsPageTemplateLoader implements TemplateLoader
{

    private Page $page;

    /**
     * @inheritDoc
     */
    public function init(Page $page): void
    {
//        $templates = wp_parse_args((array)$page->getTemplate(), ['page.php', 'index.php']);
        $this->page = $page;
    }

    /**
     * @inheritDoc
     */
    public function load(): void
    {
        do_action('template_redirect');
        $template = $this->page->getTemplate();
        require_once $template;
        // todo - re-enable this
//        $template = locate_template(array_filter($this->templates));
//        var_dump($template);
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

<?php

class admin_setting_linkbutton extends admin_setting
{
    private $href;

    /**
     * @var mixed|null
     */
    private $target;

    public function __construct($name, $visibleName, $href, $target = null)
    {
        $this->href   = $href;
        $this->target = $target ?? '_self';

        parent::__construct($name, $visibleName, '', '');
    }

    /**
     * Always returns true.
     *
     * @return bool Always returns true
     */
    public function get_setting(): bool
    {
        return true;
    }

    /**
     *
     * Never write settings.
     *
     * @param $data
     *
     * @return string
     */
    public function write_setting($data): string
    {
        return '';
    }

    /**
     * Always returns true
     *
     * @return bool Always returns true
     */
    public function get_defaultsetting(): bool
    {
        return true;
    }

    /**
     * Returns an HTML string
     *
     * @return string Returns an HTML string
     * @throws coding_exception
     */
    public function output_html($data, $query = ''): string
    {
        global $OUTPUT;

        $context         = new stdClass();
        $context->title  = $this->visiblename;
        $context->href   = $this->href;
        $context->target = $this->target;

        return $OUTPUT->render_from_template('block_anderspink/settings_linkbutton', $context);
    }
}
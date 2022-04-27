<?php

class admin_setting_flexible_table extends admin_setting
{
    protected string $tableUrl;
    protected array  $tableHeaders;
    protected array  $tableData;

    /**
     * @param  string  $name
     * @param  string  $tableUrl
     * @param  array  $tableHeaders
     * @param  array  $tableData
     */
    public function __construct(string $name, string $tableUrl, array $tableHeaders, array $tableData)
    {
        $this->tableData    = $tableData;
        $this->tableUrl     = $tableUrl;
        $this->tableHeaders = $tableHeaders;

        parent::__construct($name, '', '', '');
    }

    /**
     * @return bool
     */
    public function get_setting(): bool
    {
        return true;
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function write_setting($data): string
    {
        return '';
    }

    /**
     * @return bool
     */
    public function get_defaultsetting(): bool
    {
        return true;
    }

    /**
     * @param $data
     * @param $query
     *
     * @return false|string
     * @throws coding_exception
     */
    public function output_html($data, $query = ''): string
    {
        global $OUTPUT;

        $deleteButton = html_writer::link('', $OUTPUT->pix_icon('t/delete', get_string('delete')));

        ob_start();

        $table = new flexible_table('anderspink_settings');
        $table->define_baseurl($this->tableUrl);
        $table->define_headers($this->tableHeaders);
        $table->define_columns(['col1', 'col2', 'col3']);
        $table->setup();

        foreach ($this->tableData as $data) {
            $editButtonUrl = new moodle_url(
                '/blocks/anderspink/settings/edit.php',
                ['id' => $data['id'], 'sectionname' => 'blocksettinganderspink']
            );

            $deleteButtonUrl = new moodle_url(
                '/blocks/anderspink/settings/delete.php',
                      ['id' => $data['id'], 'sectionname' => 'blocksettinganderspink']
            );

            $editButton = html_writer::link(
                $editButtonUrl->out(false),
                $OUTPUT->pix_icon('t/edit', get_string('edit'))
            );

            $deleteButton = \html_writer::link(
                $deleteButtonUrl->out(false),
                $OUTPUT->pix_icon('t/delete', get_string('delete'))
            );

            $table->add_data([$data['teamname'], $data['apikey'], $editButton . $deleteButton]);
        }

        $table->finish_html();

        $output = ob_get_clean();
        ob_end_clean();

        $context         = new stdClass();
        $context->output = $output;

        return $OUTPUT->render_from_template('block_anderspink/setting_felxible_table', $context);
    }
}

<?php

global $CFG;

use block_anderspink\entity\block_anderspink_apikey;
use block_anderspink\entity\block_anderspink_audiences;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Version details
 *
 * @package    block_anderspink
 * @copyright  2016 onwards Anders Pink Ltd <info@anderspink.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_anderspink_edit_form extends block_edit_form
{
    /**
     * @param $mform
     *
     * @return void
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function specific_definition($mform)
    {
        global $PAGE;

        $PAGE->requires->js('/blocks/anderspink/js/mod_form.js');

        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $radioarray   = [];
        $radioarray[] = $mform->createElement(
            'radio',
            'config_viewtype',
            '',
            get_string('view_type_unmixed', 'block_anderspink'),
            'unmixed'
        );
        $radioarray[] = $mform->createElement(
            'radio',
            'config_viewtype',
            '',
            get_string('view_type_mixed', 'block_anderspink'),
            'mixed'
        );

        $mform->addGroup($radioarray, 'radioviewtype', get_string('view_type_label', 'block_anderspink'), [' '], false);

        $mform->addHelpButton('radioviewtype', 'radioviewtype', 'block_anderspink');
        $mform->setDefault('config_viewtype', 'unmixed');

        $briefingTimes = [
            'auto'     => get_string('select:option:default:time_period', 'block_anderspink'),
            '24-hours' => get_string('select:option:24h:time_period', 'block_anderspink'),
            '3-days'   => get_string('select:option:3days:time_period', 'block_anderspink'),
            '1-week'   => get_string('select:option:1week:time_period', 'block_anderspink'),
            '1-month'  => get_string('select:option:1month:time_period', 'block_anderspink'),
            '3-months' => get_string('select:option:3month:time_period', 'block_anderspink'),
        ];

        $mform->addElement(
            'select',
            'config_mixed_briefing_time',
            get_string('globalbriefingselecttime', 'block_anderspink'),
            $briefingTimes,
            []
        );
        $mform->setDefault('config_briefing_time', 'auto');
        $mform->hideIf('config_mixed_briefing_time', 'config_viewtype', 'eq', 'unmixed');
        $mform->addHelpButton('config_mixed_briefing_time', 'config_mixed_briefing_time', 'block_anderspink');

        $radioarray   = [];
        $radioarray[] = $mform->createElement(
            'radio',
            'config_image',
            '',
            get_string('sideimage', 'block_anderspink'),
            'side'
        );
        $radioarray[] = $mform->createElement(
            'radio',
            'config_image',
            '',
            get_string('topimage', 'block_anderspink'),
            'top'
        );
        $mform->addGroup($radioarray, 'radioar', 'Article image position', [' '], false);
        $mform->setDefault('config_image', 'side');

        $radioarray   = [];
        $radioarray[] = $mform->createElement(
            'radio',
            'config_column',
            '',
            get_string('onecolumn', 'block_anderspink'),
            1
        );

        $radioarray[] = $mform->createElement(
            'radio',
            'config_column',
            '',
            get_string('twocolumns', 'block_anderspink'),
            2
        );

        $mform->addGroup($radioarray, 'radioar', 'Number of columns', [' '], false,);
        $mform->setDefault('config_column', 1);
        $mform->setType('config_column', PARAM_INT);

        $mform->addElement(
            'advcheckbox',
            'config_filter_imageless',
            get_string('filterimagelessarticles', 'block_anderspink'),
            '',
            ['group' => 1],
            [0, 1,]
        );

        $mform->setDefault('config_filter_imageless', 0);
        $mform->addHelpButton('config_filter_imageless', 'filterimagelessarticles', 'block_anderspink');
        $mform->addElement('advcheckbox',
                           'config_content_preview',
                           get_string('showcontentpreview', 'block_anderspink'),
                           '',
                           ['group' => 1],
                           [0, 1,]);
        $mform->setDefault('config_content_preview', 0);
        $mform->addHelpButton('config_content_preview', 'showcontentpreview', 'block_anderspink');
        $mform->addElement('advcheckbox', 'config_comment', get_string('showcomment', 'block_anderspink'), '', ['group' => 1],
                           [0, 1,]);

        $mform->addElement('text', 'config_limit', get_string('numberofarticles', 'block_anderspink'));
        $mform->setDefault('config_limit', 5);
        $mform->setType('config_limit', PARAM_INT);

        $mform->setDefault('config_comment', 0);
        $mform->addHelpButton('config_comment', 'showcomment', 'block_anderspink');
        $mform->addElement(
            'html',
            '
            <script type="text/javascript">
                YUI().use("node", function (Y) {

                    function handleSourceVisibility(source) {
                        if (source === "briefing") {
                            Y.one("#source_section_briefing").show();
                            Y.one("#source_section_board").hide();
                        } else {
                            Y.one("#source_section_briefing").hide();
                            Y.one("#source_section_board").show();
                        }
                    }

                    // handle the visibility on load
                    var selectedValue = Y.one("input[name=config_source]:checked").get("value");
                    if (!selectedValue) {
                        selectedValue = "briefing";
                    }
                    handleSourceVisibility(selectedValue);

                    // listen to when the radio buttons are toggled
                    Y.all("input[name=config_source]").on("change", function (e) {
                        handleSourceVisibility(e.currentTarget.get("value"));
                    });
                });
            </script>
        '
        );

        // Briefing Section
        $newAudienceUrl = new moodle_url('/blocks/anderspink/new.php', [
            'course'          => $this->page->course->id,
            'instance'        => $this->block->instance->id,
            'return'          => $_SERVER['DOCUMENT_URI'],
            'dashboardid'     => $this->page->url->param('id')
        ]);

        $addNewBtn = \html_writer::link($newAudienceUrl->out(false), get_string('btn_add_briefings', 'block_anderspink'), ['class' => 'btn btn-primary']);

        $mform->addElement('header', 'briefingsection', get_string('section_briefings', 'block_anderspink'));
        $mform->addElement('html', $addNewBtn);
        $mform->addElement('html', $this->build_briefing_table());
    }

    /**
     * @return false|string
     * @throws coding_exception
     * @throws moodle_exception
     */
    private function build_briefing_table()
    {
        global $OUTPUT;

        $dashboardId  = $this->page->url->param('id');
        $courseId     = $this->page->course->id;
        $blockId      = $this->block->instance->id;

        $records = block_anderspink_audiences::repository()->where(
            'instance',
            $this->block->instance->id
        )->get();

        ob_start();
        $tableHeaders = [
            get_string('table_header_briefings', 'block_anderspink'),
            get_string('table_header_audience', 'block_anderspink'),
            get_string('table_header_team', 'block_anderspink'),
            get_string('table_header_edit', 'block_anderspink'),
        ];

        $urlParams = ['course' => $courseId, 'instance' => $blockId, 'return' => $_SERVER['DOCUMENT_URI'], 'dashboardid' => $dashboardId];

        $url       = new moodle_url('/totara/dashboard/index.php', ['id' => $courseId, 'bui_editid' => $blockId]);
        $editUrl   = new moodle_url('/blocks/anderspink/update.php', $urlParams);
        $deleteUrl = new moodle_url('/blocks/anderspink/delete.php', $urlParams);

        $table = new flexible_table('briefings');
        $table->define_baseurl($url);
        $table->define_headers($tableHeaders);
        $table->define_columns(['col1', 'col2', 'col3', 'col4', 'col5']);
        $table->setup();

        foreach ($records as $record) {
            $audiences = \core\entity\cohort::repository()->where_in('id', explode(',', $record->audience))->get()->map(
                function ($value) {
                    return $value->name;
                }
            )->to_array();

            if (empty($record->team)) {
                $record->team = 0;
            }

            $apiEntry = block_anderspink_apikey::repository()->find($record->team);

            $editUrl->param('id', $record->id);
            $deleteUrl->param('id', $record->id);

            $editIcon   = \html_writer::link($editUrl->out(false), $OUTPUT->pix_icon('t/edit', get_string('edit')));
            $deleteIcon = \html_writer::link($deleteUrl->out(false), $OUTPUT->pix_icon('t/delete', get_string('delete')));

            $table->add_data([$record->name, implode(', ', $audiences), $apiEntry->teamname, $editIcon . $deleteIcon]);
        }

        $table->finish_html();
        $output = ob_get_clean();

        ob_end_clean();

        return $output;
    }
}

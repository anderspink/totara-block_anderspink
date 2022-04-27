<?php

namespace block_anderspink\forms;

global $CFG;

require_once($CFG->libdir . '/formslib.php');

use block_anderspink\entity\block_anderspink_audiences;
use block_anderspink\local\ApiHelper;
use coding_exception;
use moodle_exception;
use moodle_url;
use MoodleQuickForm;

class BriefingForm extends \moodleform
{
    /**
     * @return void
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function definition()
    {
        global $PAGE;

        $PAGE->requires->js('/blocks/anderspink/js/mod_form.js');
        $PAGE->requires->js_call_amd('core/form-autocomplete', 'enhance',
            [
                '#id_config_audience',
                false,
                '',
                get_string('select2:placeholder:audience', 'block_anderspink'),
                false,
                true,
                '',
            ]
        );

        $mform = &$this->_form;
        $teams = ApiHelper::get_teams();

        if (empty($teams)) {
            throw new moodle_exception('exception:no_teams', 'block_anderspink');
        }

        $apiData   = ApiHelper::get_api_boards_and_briefings(array_key_first($teams));
        $audiences = ApiHelper::get_system_audiences();

        $mform->addElement('hidden', 'course', $mform->_attributes['course']);
        $mform->addElement('hidden', 'instance', $mform->_attributes['instance']);
        $mform->addElement('hidden', 'dashboardid', $mform->_attributes['dashboardid']);
        $mform->addElement('hidden', 'name');
        $mform->addElement('hidden', 'return', $this->_customdata['return']);

        $mform->setType('course', PARAM_INT);
        $mform->setType('instance', PARAM_INT);
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $radioArray   = [];
        $radioArray[] = $mform->createElement(
            'radio',
            'config_source',
            '',
            get_string('showbriefing', 'block_anderspink'),
            'briefing',
            ['data-target' => 'briefing']
        );
        $radioArray[] = $mform->createElement(
            'radio',
            'config_source',
            '',
            get_string('showsavedboard', 'block_anderspink'),
            'board',
            ['data-target' => 'board']
        );
        $mform->addGroup($radioArray, 'radioar', '', [' '], false);
        $mform->setDefault('config_source', 'briefing');

        $mform->addElement(
            'select',
            'config_team',
            get_string('label:team', 'block_anderspink'),
            $teams,
            []
        );

        $mform->addElement(
            'select',
            'config_briefing',
            get_string('briefingselect', 'block_anderspink'),
            $apiData['briefings'],
            []
        );

        $mform->addElement(
            'select',
            'config_board',
            get_string('boardselect', 'block_anderspink'),
            $apiData['boards'],
            []
        );

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
            'config_briefing_time',
            get_string('briefingselecttime', 'block_anderspink'),
            $briefingTimes,
            []
        );

        $mform->setDefault('config_briefing_time', 'auto');
        $mform->addHelpButton('config_briefing_time', 'briefingselecttime', 'block_anderspink');

        $mform->addElement(
            'select',
            'config_audience',
            get_string('label:audiences', 'block_anderspink'),
            $audiences['audiences'],
            ['multiple' => true]
        );

        $buttonGroup   = [];
        $buttonGroup[] = $mform->createElement('submit', 'submitbutton', get_string('btn:save', 'block_anderspink'));
        $buttonGroup[] = $mform->createElement('cancel');

        $mform->addGroup($buttonGroup, 'buttongroup', '', [' '], false);

        $mform->hideIf('config_briefing', 'config_source', 'eq', 'board');
        $mform->hideIf('config_board', 'config_source', 'eq', 'briefing');
        $mform->hideIf('config_briefing_time', 'config_source', 'eq', 'board');

        $mform->disabledIf('config_briefing', 'config_team', 'checked');
        $mform->disabledIf('config_board', 'config_team', 'checked');

        [$selectedId, $selectedType] = $this->setDefaults($mform);

        $PAGE->requires->js_call_amd('block_anderspink/form', 'init', ['selectedId' => $selectedId, 'selectedType' => $selectedType]);
    }

    /**
     * @param  MoodleQuickForm  $mform
     *
     * @return array
     * @throws coding_exception
     */
    private function setDefaults(MoodleQuickForm &$mform): array
    {
        if ((int) $mform->_attributes['id'] <= 0) {
            return [];
        }

        /** @var block_anderspink_audiences $entity */
        $entity = block_anderspink_audiences::repository()->find($mform->_attributes['id']);

        $mform->addElement('hidden', 'id', $mform->_attributes['id']);
        $mform->setType('id', PARAM_INT);

        $mform->setDefault('config_source', $entity->type);
        $mform->setDefault('config_' . $entity->type, $entity->item);
        $mform->setDefault('config_briefing_time', $entity->time);
        $mform->setDefault('config_audience', explode(',', $entity->audience));
        $mform->setDefault('config_team', $entity->team);

        return [$entity->item, $entity->type];
    }
}

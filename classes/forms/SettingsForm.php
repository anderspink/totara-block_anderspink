<?php

namespace block_anderspink\forms;

global $CFG;

use block_anderspink\entity\block_anderspink_apikey;
use coding_exception;
use MoodleQuickForm;

require_once($CFG->libdir . '/formslib.php');

class SettingsForm extends \moodleform
{
    /**
     * @return void
     * @throws coding_exception
     */
    public function definition()
    {
        $mform = $this->_form;

        $mform->addElement('hidden', 'sectionname', $mform->_attributes['sectionname']);

        if (!empty($mform->_attributes['id'])) {
            $this->setUpdateDefaults($mform);
        }

        $mform->setType('section', PARAM_TEXT);

        $mform->addElement('header', 'general', get_string('settings:form:general', 'block_anderspink'));
        $mform->addElement('text', 'settings_teamname', get_string('settings:form:teamname', 'block_anderspink'));
        $mform->addElement('textarea',
                           'settings_apikey',
                           get_string('settings:form:apikey', 'block_anderspink'),
                           ['rows' => 1, 'cols' => 50]);

        $mform->addHelpButton('settings_apikey', 'settings:form:apikey', 'block_anderspink');

        $mform->setType('settings_teamname', PARAM_TEXT);
        $mform->setType('settings_apikey', PARAM_TEXT);

        $buttonGroup   = [];
        $buttonGroup[] = $mform->createElement('submit', 'submitbutton', get_string('btn:save', 'block_anderspink'));
        $buttonGroup[] = $mform->createElement('cancel');

        $mform->addGroup($buttonGroup, 'buttongroup', '', [' '], false);
    }

    /**
     * @param $data
     * @param $files
     *
     * @return array
     * @throws coding_exception
     */
    public function validation($data, $files)
    {
        $errors   = parent::validation($data, $files);
        $apiKey   = $data['settings_apikey'];
        $teamname = $data['settings_teamname'];
        $id       = $data['id'];

        $entity = block_anderspink_apikey::repository()->where('apikey', $apiKey)->where('id', '!=', $id);

        if (!empty($entity) && $entity->exists()) {
            $errors['settings_apikey'] = get_string('config_apikey_duplicate', 'block_anderspink');
        }

        $entity = block_anderspink_apikey::repository()->where('teamname', $teamname);

        if (!empty($entity) && $entity->exists()) {
            $errors['settings_teamname'] = get_string('config_teamname_duplicate', 'block_anderspink');
        }

        if (empty($apiKey)) {
            $errors['settings_apikey'] = get_string('config_apikey_empty', 'block_anderspink');
        }

        if (empty($teamname)) {
            $errors['settings_teamname'] = get_string('config_teamname_empty', 'block_anderspink');
        }

        return $errors;
    }

    /**
     * @param  MoodleQuickForm  $mform
     *
     * @return void
     * @throws coding_exception
     */
    private function setUpdateDefaults(MoodleQuickForm &$mform): void
    {
        $mform->addElement('hidden', 'id', $mform->_attributes['id']);
        $mform->setType('id', PARAM_TEXT);

        /** @var block_anderspink_apikey $entity */
        $entity = block_anderspink_apikey::repository()->find((int)$mform->_attributes['id']);

        $mform->setDefault('settings_teamname', $entity->teamname);
        $mform->setDefault('settings_apikey', $entity->apikey);
    }
}

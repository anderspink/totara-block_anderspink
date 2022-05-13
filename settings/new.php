<?php

use block_anderspink\entity\block_anderspink_apikey;
use block_anderspink\forms\SettingsForm;
use core\notification;

require_once("../../../config.php");

global $PAGE, $OUTPUT;

$section = required_param('sectionname', PARAM_TEXT);

$context = context_system::instance();

require_login();
require_capability('block/anderspink:manageapikeys', $context);

$backUrl    = new moodle_url('/admin/settings.php', ['section' => $section]);
$currentUrl = new moodle_url('/blocks/anderspink/settings/new.php', ['sectionname' => $section]);

$form = new SettingsForm(null, null, 'post', '', $currentUrl->params());

if ($form->is_cancelled()) {
    redirect($backUrl->out(false));
}

if ($formData = $form->get_data()) {
    $blockAnderspinkApiKeyEntity           = new block_anderspink_apikey();
    $blockAnderspinkApiKeyEntity->teamname = $formData->settings_teamname;
    $blockAnderspinkApiKeyEntity->apikey   = $formData->settings_apikey;

    $blockAnderspinkApiKeyEntity->save();
    notification::success(get_string('success:saved', 'block_anderspink'));

    redirect($backUrl->out(false));
} else {
    $PAGE->set_url($currentUrl);

    echo $OUTPUT->header();
    echo $form->render();
    echo $OUTPUT->footer();
}


<?php

use block_anderspink\entity\block_anderspink_apikey;
use block_anderspink\forms\SettingsForm;
use core\notification;

require_once("../../../config.php");

global $PAGE, $OUTPUT;

$id      = required_param('id', PARAM_INT);
$section = required_param('sectionname', PARAM_TEXT);

require_login();

$backUrl    = new moodle_url('/admin/settings.php', ['section' => $section]);
$currentUrl = new moodle_url('/blocks/anderspink/settings/update.php', ['id' => $id, 'sectionname' => $section]);

$form = new SettingsForm(null, null, 'post', '', $currentUrl->params());

if ($form->is_cancelled()) {
    redirect($backUrl->out(false));
}

if ($formData = $form->get_data()) {
    $update = (object)[
        'teamname' => $formData->settings_teamname,
        'apikey'   => $formData->settings_apikey,
    ];

    block_anderspink_apikey::repository()
        ->where('id', $id)
        ->update($update);

    notification::success(get_string('success:saved', 'block_anderspink'));

    redirect($backUrl->out(false));
} else {
    $PAGE->set_url($currentUrl);

    echo $OUTPUT->header();
    echo $form->render();
    echo $OUTPUT->footer();
}
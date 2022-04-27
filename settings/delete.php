<?php

use block_anderspink\entity\block_anderspink_apikey;
use core\notification;

require_once("../../../config.php");

global $PAGE, $OUTPUT;

$id      = required_param('id', PARAM_INT);
$section = required_param('sectionname', PARAM_TEXT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

require_login();

$backUrl    = new moodle_url('/admin/settings.php', ['section' => $section]);
$currentUrl = new moodle_url('/blocks/anderspink/settings/delete.php', ['id' => $id, 'sectionname' => $section]);

if ($confirm) {
    try {
        block_anderspink_apikey::repository()->where('id', $id)->delete();
        notification::success(get_string('success:removed', 'block_anderspink'));
    } catch (\Exception $e) {
        notification::error(get_string('error:removed', 'block_anderspink'));
    }
    redirect($backUrl->out(false));
} else {
    $PAGE->set_url($currentUrl->out(false));
    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('block_anderspink/settings_confirm', [
        'id'          => $id,
        'sectionname' => $section,
        'sesskey'     => sesskey(),
    ]);
    echo $OUTPUT->footer();
}
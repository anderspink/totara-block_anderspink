<?php

use block_anderspink\entity\block_anderspink_audiences;
use core\notification;

require_once("../../config.php");

global $PAGE, $OUTPUT;

$id            = required_param('id', PARAM_INT);
$courseId      = required_param('course', PARAM_INT);
$blockInstance = required_param('instance', PARAM_INT);
$dashboardId   = required_param('dashboardid', PARAM_INT);
$confirm       = optional_param('confirm', false, PARAM_BOOL);
$returnUrl     = optional_param('return', '', PARAM_TEXT);

$context = context_block::instance($blockInstance);

require_login($courseId);
require_capability('block/anderspink:managebriefings', $context);

$goBackUrl = new moodle_url($returnUrl, ['id' => $dashboardId, 'bui_editid' => $blockInstance, 'sesskey' => sesskey()]);

if ($confirm) {
    try {
        block_anderspink_audiences::repository()->where('id', $id)->delete();
        notification::success(get_string('success:removed', 'block_anderspink'));
    } catch (\Exception $e) {
        notification::error(get_string('error:removed', 'block_anderspink'));
    }
    redirect($goBackUrl->out(false));
} else {
    $url = new moodle_url('/block/anderspink/delete.php', ['id' => $id, 'course' => $dashboardId, 'instance' => $blockInstance, 'dashboardid' => $dashboardId]);
    $PAGE->set_url($url->out(false));
    $PAGE->set_heading('New Audience');

    echo $OUTPUT->header();
    echo $OUTPUT->render_from_template('block_anderspink/confirm', [
        'id'          => $id,
        'course'      => $courseId,
        'instance'    => $blockInstance,
        'dashboardid' => $dashboardId,
        'sesskey'     => sesskey(),
        'return'      => $returnUrl,
    ]);
    echo $OUTPUT->footer();
}
<?php

use block_anderspink\entity\block_anderspink_audiences;
use block_anderspink\forms\BriefingForm;
use core\notification;

require_once("../../config.php");

global $PAGE, $OUTPUT;

$courseId              = required_param('course', PARAM_INT);
$blockInstance         = required_param('instance', PARAM_INT);
$dashboardId           = required_param('dashboardid', PARAM_INT);
$duplicateConfirmation = optional_param('confirm', false, PARAM_BOOL);
$returnUrl             = optional_param('return', '', PARAM_TEXT);

$context = context_block::instance($blockInstance);

require_login($courseId);

$goBackUrl  = new moodle_url($returnUrl, ['id' => $dashboardId, 'bui_editid' => $blockInstance, 'sesskey' => sesskey()]);
$currentUrl = new moodle_url('/blocks/anderspink/new.php', ['course' => $courseId, 'instance' => $blockInstance, 'dashboardid' => $dashboardId]);
$form       = new BriefingForm(null, ['return' => $returnUrl], 'post', '', $currentUrl->params());

if ($form->is_cancelled()) {
    redirect($goBackUrl->out());
}

$formData = $_POST;

//Hard post check as values may be overwritten by jquery ajax changing drop-downs dynamically
if (!empty($formData)) {
    $itemName      = $formData['config_' . $formData['config_source']];
    [$item, $name] = explode('|', $itemName);

    $existing = block_anderspink_audiences::repository()
        ->where('instance', $formData['instance'])
        ->where('item', $item);

    if ((!empty($existing) && $existing->exists()) && !$duplicateConfirmation) {
        $formData['post_url']      = '/blocks/anderspink/new.php';
        $formData['duplicate_msg'] = get_string('duplicate_confirmation', 'block_anderspink', ['name' => $name]);
        $formData['return']        = $returnUrl;

        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('block_anderspink/duplicate_confirmation', $formData);
        echo $OUTPUT->footer();
        exit;
    } else {
        $blockAudience           = new block_anderspink_audiences();
        $blockAudience->instance = $formData['instance'];
        $blockAudience->type     = $formData['config_source'];
        $blockAudience->item     = $item;
        $blockAudience->time     = $formData['config_briefing_time'] ?? 'auto';
        $blockAudience->name     = $name;
        $blockAudience->team     = $formData['config_team'];
        $blockAudience->audience = '';

        if (is_array($formData['config_audience'])) {
            $blockAudience->audience = implode(',', $formData['config_audience']);
        }

        $blockAudience->save();
        notification::success(get_string('success:saved', 'block_anderspink'));
        redirect($goBackUrl->out(false));
    }
}

$PAGE->set_url($currentUrl);
$PAGE->set_heading('New Audience');

echo $OUTPUT->header();
echo $form->render();
echo $OUTPUT->footer();


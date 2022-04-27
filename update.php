<?php

use block_anderspink\entity\block_anderspink_audiences;
use block_anderspink\forms\BriefingForm;
use core\notification;

require_once("../../config.php");

global $PAGE, $OUTPUT;

$id                    = required_param('id', PARAM_INT);
$courseId              = required_param('course', PARAM_INT);
$blockInstance         = required_param('instance', PARAM_INT);
$dashboardId           = required_param('dashboardid', PARAM_INT);
$duplicateConfirmation = optional_param('confirm', false, PARAM_BOOL);
$returnUrl             = optional_param('return', '', PARAM_TEXT);

$context = context_block::instance($blockInstance);

require_login($courseId);

$goBackUrl  = new moodle_url($returnUrl, ['id' => $dashboardId, 'bui_editid' => $blockInstance, 'sesskey' => sesskey()]);
$currentUrl = new moodle_url('/blocks/anderspink/update.php', ['id' => $id, 'course' => $courseId, 'instance' => $blockInstance, 'dashboardid' => $dashboardId]);
$form       = new BriefingForm(null, ['return' => $goBackUrl->out(false)], 'post', '', $currentUrl->params());

if ($form->is_cancelled()) {
    redirect($goBackUrl->out(false));
}

$formData = $_POST;
//Hard post check as values may be overwritten by jquery ajax changing drop-downs dynamically
if (!empty($formData)) {
    $itemName      = $formData['config_' . $formData['config_source']];
    [$item, $name] = explode('|', $itemName);

    $existing = block_anderspink_audiences::repository()
        ->where('instance', $formData['instance'])
        ->where('item', $item)
        ->where('id', '!=', $formData['id']);

    if ((!empty($existing) && $existing->exists()) && !$duplicateConfirmation) {
        $formData['post_url']      = '/blocks/anderspink/update.php';
        $formData['duplicate_msg'] = get_string('duplicate_confirmation', 'block_anderspink', ['name' => $name]);
        $formData['return']        = $returnUrl;

        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template('block_anderspink/duplicate_confirmation', $formData);
        echo $OUTPUT->footer();
        exit;
    } else {
        $updateData = (object)[
            'instance' => $formData['instance'],
            'type'     => $formData['config_source'],
            'item'     => $item,
            'time'     => $formData['config_briefing_time'] ?? 'auto',
            'name'     => $name,
            'audience' => '',
            'team'     => $formData['config_team'],
        ];

        if (is_array($formData['config_audience'])) {
            $updateData->audience = implode(',', $formData['config_audience']);
        }

        $blockAudience = block_anderspink_audiences::repository()->where('id', $formData['id'])->update($updateData);
        notification::success(get_string('success:saved', 'block_anderspink'));
        redirect($goBackUrl->out(false));
    }
}

$PAGE->set_url($currentUrl);
$PAGE->set_heading('Update Audience');

echo $OUTPUT->header();
echo $form->render();
echo $OUTPUT->footer();

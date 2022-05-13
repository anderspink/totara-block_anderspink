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
require_capability('block/anderspink:managebriefings', $context);

$goBackUrl  = new moodle_url($returnUrl, ['id' => $dashboardId, 'bui_editid' => $blockInstance, 'sesskey' => sesskey()]);
$currentUrl = new moodle_url('/blocks/anderspink/update.php', ['id' => $id, 'course' => $courseId, 'instance' => $blockInstance, 'dashboardid' => $dashboardId]);
$form       = new BriefingForm(null, ['return' => $goBackUrl->out(false)], 'post', '', $currentUrl->params());

if ($form->is_cancelled()) {
    redirect($goBackUrl->out(false));
}

$formData = $_POST;

//Hard post check as values may be overwritten by jquery ajax changing drop-downs dynamically
if (!empty($formData)) {
    $configSource       = clean_param($formData['config_source'], PARAM_TEXT);
    $configBriefingTime = clean_param($formData['config_briefing_time'], PARAM_RAW);
    $configTeam         = clean_param($formData['config_team'], PARAM_RAW);
    $itemName           = clean_param($formData['config_' . $configSource], PARAM_TEXT);
    $instance           = clean_param($formData['instance'], PARAM_INT);
    $id                 = clean_param($formData['id'], PARAM_INT);

    if (is_array($formData['config_audience'])) {
        $configAudience = clean_param_array($formData['config_audience'], PARAM_RAW);
    } else {
        $configAudience = clean_param($formData['config_audience'], PARAM_RAW);
    }

    [$item, $name] = explode('|', $itemName);

    $existing = block_anderspink_audiences::repository()
        ->where('instance', $instance)
        ->where('item', $item)
        ->where('id', '!=', $id);

    if ((!empty($existing) && $existing->exists()) && !$duplicateConfirmation) {
        echo $OUTPUT->header();
        echo $OUTPUT->render_from_template(
            'block_anderspink/duplicate_confirmation',
            (object)[
                'course'                  => $courseId,
                'dashboardid'             => $dashboardId,
                'name'                    => $name,
                'sesskey'                 => sesskey(),
                'config_source'           => $configSource,
                'config_briefing_time'    => $configBriefingTime,
                'config_team'             => $configTeam,
                'config_audience'         => $configAudience,
                'config_' . $configSource => $itemName,
                'instance'                => $instance,
                'id'                      => $id,
                'post_url'                => '/blocks/anderspink/update.php',
                'duplicate_msg'           => get_string('duplicate_confirmation', 'block_anderspink', ['name' => $name]),
                'return'                  => $returnUrl,
            ]
        );
        echo $OUTPUT->footer();
        exit;
    } else {
        $updateData = (object)[
            'instance' => $instance,
            'type'     => $configSource,
            'item'     => $item,
            'time'     => $configBriefingTime ?? 'auto',
            'name'     => $name,
            'audience' => '',
            'team'     => $configTeam,
        ];

        if (is_array($configAudience)) {
            $updateData->audience = implode(',', $configAudience);
        }

        $blockAudience = block_anderspink_audiences::repository()->where('id', $id)->update($updateData);
        notification::success(get_string('success:saved', 'block_anderspink'));
        redirect($goBackUrl->out(false));
    }
}

$PAGE->set_url($currentUrl);
$PAGE->set_heading('Update Audience');

echo $OUTPUT->header();
echo $form->render();
echo $OUTPUT->footer();

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details
 *
 * @package    block_anderspink
 * @copyright  2016 onwards Anders Pink Ltd <info@anderspink.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_anderspink\entity\block_anderspink_apikey;

defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE;

require_once($CFG->dirroot . '/blocks/anderspink/classes/forms/admin/admin_setting_linkbutton.php');
require_once($CFG->dirroot . '/blocks/anderspink/classes/forms/admin/admin_setting_flexible_table.php');

if ($PAGE->url->param('section') === 'blocksettinganderspink') {
    $PAGE->requires->css('/blocks/anderspink/styles/settings.css');
}

$newInstanceUrl = new moodle_url('/blocks/anderspink/settings/new.php', ['sectionname' => 'blocksettinganderspink']);
$tableUrl       = new moodle_url('/admin/settings.php', ['section' => 'blocksettinganderspink']);
$tableHeaders   = [
    get_string('settings_table_header_teamname', 'block_anderspink'),
    get_string('settings_table_header_apikey', 'block_anderspink'),
    get_string('settings_table_header_actions', 'block_anderspink'),
];

$data = block_anderspink_apikey::repository()->get()->to_array();

$settings->add(
    new admin_setting_heading(
        'sampleheader', get_string('headerconfig', 'block_anderspink'), ''
    )
);

$settings->add(
    new admin_setting_linkbutton('linkbutton', 'Add new', $newInstanceUrl->out(false))
);

$settings->add(
    new admin_setting_flexible_table('table_settings_block_anderspink', $tableUrl->out(false), $tableHeaders, $data)
);

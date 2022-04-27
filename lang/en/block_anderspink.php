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

$string['blockstring']                     = 'Block title';
$string['boardselect']                     = 'Saved folders';
$string['showbriefing']                    = 'Show a briefing';
$string['showsavedboard']                  = 'Show a saved folder';
$string['briefingselect']                  = 'Briefings';
$string['briefingselecttime']              = 'Briefing time period';
$string['globalbriefingselecttime']        = 'Global Briefing time period';
$string['config_mixed_briefing_time_help'] = 'This value will overwrite any individual briefing time periods set against individual entries';
$string['briefingselecttime_help']         = 'This is the time period that you will see the top articles from. Leaving on Auto is recommended as it will select a good time period based on how many new articles are coming in. E.g. if your briefing gets many fresh articles every day, Auto will show articles from the last 24 hours rather than 3 days.';
$string['descconfig']                      = 'Description of the config section';
$string['descfoo']                         = 'Global settings for the Anders Pink plugin';
$string['settings:form:apikey_help']       = "Enter the API key from your Anders Pink account or use our free account key which is <strong>WL4VDET6kcH29PDTG2RVF60Yqv76E39z</strong> to access our free  briefings. <br /><br />To find out more about how you can create custom briefings visit <a href='https://anderspink.com'>https://anderspink.com</a>.";
$string['headerconfig']                    = 'Main plugin settings';
$string['labelkey']                        = 'Anders Pink API key';
$string['anderspink:addinstance']          = 'Add an anderspink block';
$string['anderspink:myaddinstance']        = 'Add an anderspink block to my moodle';
$string['pluginname']                      = 'Anders Pink Learn';
$string['cachedef_apdata']                 = 'Anders Pink API result cache, for performance and reducing API calls';
$string['sideimage']                       = 'On the right (small)';
$string['topimage']                        = 'Above (large)';
$string['onecolumn']                       = 'One column';
$string['twocolumns']                      = 'Two columns';
$string['numberofarticles']                = 'Number of articles to show (min 1, max 30)';
$string['filterimagelessarticles']         = 'Only show articles that have an image';
$string['filterimagelessarticles_help']    = 'Sometimes we can\'t find an image for an article. By enabling this option you can filter out articles that have no image, so that your plugin looks a bit nicer.';
$string['showcontentpreview']              = 'Show previews of article content';
$string['showcontentpreview_help']         = 'This will display the first few paragraphs of the article body, if available.';
$string['showcomment']                     = 'Show article pinned comments';
$string['showcomment_help']                = 'This will display the pinned comment on an article (if set), otherwise it will show the latest comment that has been added. Article comments are those posted by your team inside the Anders Pink app at https://anderspink.com';
$string['select_briefing_placeholder']     = 'Select a briefing';
$string['select_board_placeholder']        = 'Select a saved board';
$string['section_briefings']               = 'Briefings';
$string['config_source_label']             = 'Display Source';
$string['btn_add_briefings']               = 'Add Briefings';

//mod_form/table
$string['table_header_briefings']      = 'Briefings / Saved Folders';
$string['table_header_no_of_articles'] = 'Number of articles to show';
$string['table_header_audience']       = 'Audiences';
$string['table_header_team']           = 'Team Name';
$string['table_header_edit']           = 'Edit';

//setting/table
$string['settings_table_header_teamname'] = 'Team Name';
$string['settings_table_header_apikey']   = 'API Key';
$string['settings_table_header_actions']  = 'Edit';

//settings/form
$string['settings:form:general']  = 'New API Setting';
$string['settings:form:teamname'] = 'Team Name';
$string['settings:form:apikey']   = 'API Key';

//Err
$string['error:no_api_key']             = 'No API key is set for the block, please set this in the global block settings for Anders Pink';
$string['error:failed_to_call_api']     = 'Failed to do API call: {$a->error}';
$string['error:form']                   = 'Form Error';
$string['error:form_missing_audience']  = 'Missing audience';
$string['error:form_ok']                = 'OK';
$string['error:form_content']           = 'Missing brefing or saved folder';
$string['error:duplicate']              = 'Duplicate Found';
$string['error:form_duplicate_msg']     = 'A duplicate entry has been found against this block instance.';
$string['error:removed']                = 'Something went wrong! Please try again or contact administrator';
$string['success:saved']                = 'A entry has been saved against this block instance.';
$string['success:removed']              = 'A entry has been removed';
$string['exception_wrong_view_type']    = 'Wrong view type selected. Please go back to settings and select correct view type';
$string['exception_wrong_setting_type'] = 'Wrong setting type selected. Please go back to settings and select correct type';
$string['exception:no_teams']           = 'No team found. Please add new team to the plugin settings';

//Modal/Form
$string['top_warning_message']                = 'Please ensure you have saved any changes on the block settings page before adding or updating a briefing. Any unsaved changes will be lost.';
$string['radio:show_briefing']                = 'Show a briefing';
$string['radio:show_boards']                  = 'Show a saved folder';
$string['label:briefing']                     = 'Briefings';
$string['select:placeholder_select_briefing'] = 'Select a briefing';
$string['label:board']                        = 'Saved folders';
$string['select:placeholder_select_board']    = 'Select a saved folder';
$string['label:time_period']                  = 'Time period';
$string['help:title:time_period']             = 'Help with time period';
$string['select:option:default:time_period']  = 'Auto (recommended)';
$string['select:option:24h:time_period']      = '24 Hours';
$string['select:option:1week:time_period']    = '1 Week';
$string['select:option:3days:time_period']    = '3 Days';
$string['select:option:3month:time_period']   = '3 Month';
$string['select:option:1month:time_period']   = '1 Month';
$string['label:no_of_articles']               = 'Number of articles to show (min 1, max 30)';
$string['label:audiences']                    = 'Audiences';
$string['label:team']                         = 'Team Name';
$string['btn:save']                           = 'Save';
$string['btn:close']                          = 'Close';
$string['select2:placeholder:audience']       = 'Select audiences';
$string['confirm_heading']                    = 'Confirm';
$string['confirm_msg']                        = 'Are you sure you want to delete the record ?';
$string['duplicate_msg']                      = 'Are you sure you want to add duplicate record ?';
$string['view_type_unmixed']                  = 'Separate';
$string['view_type_mixed']                    = 'Mixed';
$string['view_type_label']                    = 'View Type';
$string['radioviewtype']                      = 'View Type';
$string['radioviewtype_help']                 = '<p><strong>Separate:</strong> Displays each briefing separately, with a heading for each</p>
<p><strong>Mixed:</strong> Displays articles from each briefing in a combined list</p>';
$string['wrong_audience_no_display']          = 'There is currently no content to display in this block.';
$string['config_apikey_duplicate']            = 'You already have this API key saved. Please use a different one';
$string['config_teamname_duplicate']          = 'You already have this Team Name saved. Please use a different one';
$string['config_teamname_empty']              = 'Empty Team Name. Please provide a Team Name';
$string['config_apikey_empty']                = 'Empty API Key. Please provide a API Key';

//Block View
$string['please_configure_block_content'] = 'Please configure this block and choose a briefing to show.';
$string['please_configure_block_api_key'] = 'Please set the API key in the global Anders Pink block settings.';
$string['issue_loading_content']          = 'There was an issue loading the briefing/board: {$a->err}';
$string['issue_response_not_success']     = 'There was an API error: {$a->err}';

//confirmation
$string['duplicate_confirmation'] = 'You have already added the briefing <strong>{$a->name}</strong> to this block.<br><br> Click continue to add the briefing a second time, or go back to the block settings page to edit the settings for the existing briefing.';
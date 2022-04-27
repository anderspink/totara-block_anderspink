<?php

namespace block_anderspink\local;

global $CFG;

require_once($CFG->dirroot . '/cohort/lib.php');

use block_anderspink\entity\block_anderspink_apikey;
use coding_exception;
use Exception;
use moodle_exception;

class ApiHelper
{
    const HOST           = 'https://anderspink.com/api/v2';
    const STATUS_SUCCESS = 'success';

    /**
     * @return array[]
     * @throws coding_exception
     * @throws moodle_exception
     * @throws Exception
     */
    public static function get_api_boards_and_briefings(int $teamId = 0)
    {
        $briefings = [];
        $boards    = [];

        $apiKey = block_anderspink_apikey::repository()->where('id', $teamId)->one();

        if (empty($apiKey)) {
            throw new \moodle_exception('error:no_api_key', 'block_anderspink');
        }

        $briefingsResponse = download_file_content(
            self::HOST . '/briefings',
            ['X-Api-Key' => $apiKey->apikey],
            null,
            true
        );
        $boardsResponse    = download_file_content(self::HOST . '/boards', ['X-Api-Key' => $apiKey->apikey], null, true);

        $briefingsResponse = json_decode($briefingsResponse->results);
        $result            = self::check_json_last_error();

        if ($result) {
            throw new Exception($result);
        }

        $boardsResponse = json_decode($boardsResponse->results);
        $result         = self::check_json_last_error();

        if ($result) {
            throw new Exception($result);
        }

        if (empty($briefingsResponse)) {
            throw new Exception(
                get_string('error:failed_to_call_api', 'block_anderspink', ['error' => $briefingsResponse->error])
            );
        }

        if (empty($boardsResponse)) {
            throw new Exception(
                get_string('error:failed_to_call_api', 'block_anderspink', ['error' => $boardsResponse->error])
            );
        }

        if ($briefingsResponse->status !== self::STATUS_SUCCESS) {
            throw new Exception($briefings->message);
        }

        if ($boardsResponse->status !== self::STATUS_SUCCESS) {
            throw new Exception($boards->message);
        }

        foreach ($briefingsResponse->data->owned_briefings as $briefing) {
            $briefings[$briefing->id . '|' . $briefing->name] = $briefing->name;
        }

        foreach ($briefingsResponse->data->subscribed_briefings as $briefing) {
            $briefings[$briefing->id . '|' . $briefing->name] = $briefing->name;
        }

        foreach ($boardsResponse->data->owned_boards as $board) {
            $boards[$board->id . '|' . $board->name] = $board->name;
        }

        return [
            'boards'    => $boards,
            'briefings' => $briefings,
        ];
    }

    /**
     * @return array
     */
    public static function get_system_audiences(): array
    {
        $cohorts = cohort_get_all_cohorts();

        if (empty($cohorts)) {
            return [];
        }

        $parsedCohort = [];

        foreach ($cohorts['cohorts'] as $cohort) {
            $parsedCohort[$cohort->id] = $cohort->name;
        }

        return ['audiences' => $parsedCohort];
    }

    /**
     * @return array
     * @throws coding_exception
     */
    public static function get_teams(): array
    {
        $teams = block_anderspink_apikey::repository()->get();

        if (empty($teams)) {
            return [];
        }

        $parsedTeams = [];

        foreach ($teams as $team) {
            $parsedTeams[$team->id] = $team->teamname;
        }

        return $parsedTeams;
    }

    /**
     * @return false|string
     */
    private static function check_json_last_error()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return false;
            case JSON_ERROR_DEPTH:
                return ' - Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return ' - Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return ' - Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return ' - Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return ' - Unknown error';
        }
    }
}
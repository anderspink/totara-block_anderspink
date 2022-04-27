<?php

namespace block_anderspink\webapi\resolver\query;

use block_anderspink\local\ApiHelper;
use core\webapi\execution_context;
use core\webapi\middleware\require_login;
use core\webapi\query_resolver;
use core\webapi\resolver\has_middleware;

class get_apidata implements has_middleware, query_resolver
{
    /**
     * @param  array  $args
     * @param  execution_context  $ec
     *
     * @return array[]
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function resolve(array $args, execution_context $ec)
    {
        if ($args['teamid'] === 0) {
            return [
                'briefings' => [],
                'boards'    => []
            ];
        }

        $parsedBoards    = [];
        $parsedBriefings = [];
        $apiData         = ApiHelper::get_api_boards_and_briefings($args['teamid']);

        foreach ($apiData['boards'] as $key => $value) {
            $parsedBoards[] = [
                'id'   => $key,
                'name' => $value,
            ];
        }

        foreach ($apiData['briefings'] as $key => $value) {
            $parsedBriefings[] = [
                'id'   => $key,
                'name' => $value,
            ];
        }

        return [
            'briefings' => $parsedBriefings,
            'boards'    => $parsedBoards,
        ];
    }

    /**
     * @return string[]
     */
    public static function get_middleware(): array
    {
        return [
            require_login::class,
        ];
    }
}
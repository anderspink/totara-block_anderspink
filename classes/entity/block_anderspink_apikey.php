<?php

namespace block_anderspink\entity;

use core\orm\entity\entity;

/**
 * @property int $id
 * @property string $teamname
 * @property string $apikey
 */
final class block_anderspink_apikey extends entity
{
    const TABLE = 'block_anderspink_apikey';
}

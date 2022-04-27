<?php

namespace block_anderspink\entity;

use core\orm\entity\entity;

/**
 * @property int $id
 * @property int $instance
 * @property int $item
 * @property string $audience
 * @property string $type
 * @property string $time
 * @property string $name
 * @property int $team
 */
final class block_anderspink_audiences extends entity
{
    public const TABLE = 'block_anderspink_audiences';
}

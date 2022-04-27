<?php

defined('MOODLE_INTERNAL') || die();

/**
 * @param $oldversion
 * @return bool
 * @throws coding_exception
 * @throws ddl_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_block_anderspink_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2022031401) {
        $table = new xmldb_table('block_anderspink_audiences');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, true);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false);
        $table->add_field('item', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false);
        $table->add_field('audience', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, false);
        $table->add_field('type', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, false);
        $table->add_field('time', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, false);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        $table->add_index('block_idx', XMLDB_INDEX_NOTUNIQUE, ['instance']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2022031401, 'block', 'anderspink');
    }

    if ($oldversion < 2022031501) {
        $table = new xmldb_table('block_anderspink_audiences');
        $field = new xmldb_field('name', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, false);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2022031501, 'block', 'anderspink');
    }

    if ($oldversion < 2022031801) {
        $table = new xmldb_table('block_anderspink_audiences');
        $field = new xmldb_field('limit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false);

        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2022031801, 'block', 'anderspink');
    }

    if ($oldversion < 2022032101) {
        $table = new xmldb_table('block_anderspink_audiences');
        $field = new xmldb_field('audience', XMLDB_TYPE_TEXT);

        $dbman->change_field_notnull($table, $field);

        upgrade_plugin_savepoint(true, 2022032101, 'block', 'anderspink');
    }

    if ($oldversion < 2022032201) {
        $table = new xmldb_table('block_anderspink_apikey');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, true);
        $table->add_field('teamname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, false);
        $table->add_field('apikey', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, false);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2022032201, 'block', 'anderspink');
    }

    if ($oldversion < 2022032202) {
        $table = new xmldb_table('block_anderspink_audiences');
        $field = new xmldb_field('team', XMLDB_TYPE_INTEGER, '10', null, false, false);
        $index = new xmldb_index('block_team', XMLDB_INDEX_NOTUNIQUE, ['team']);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2022032202, 'block', 'anderspink');
    }

    return true;
}

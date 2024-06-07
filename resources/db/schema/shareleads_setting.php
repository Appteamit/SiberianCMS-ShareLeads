<?php

/**
 *
 * Schema definition for 'shareleads_setting'
 *
 * Last update: 2023-12-27
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['shareleads_setting'] = [
    'setting_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'help_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ],
];
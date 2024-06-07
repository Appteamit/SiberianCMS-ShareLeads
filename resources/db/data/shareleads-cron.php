<?php 
// Get current module
$module = new Installer_Model_Installer_Module();

$module->prepare('Shareleads');

// Install the cron job
Siberian_Feature::installCronjob(

    __('Shareleads cron job.'),

    'Shareleads_Model_Db_Table_Setting::runCron', // command

    -1, // minute

    -1, // hour

    -1, // month_day

    -1, // month

    -1, // week_day

    true, // is_active

    100, // priority

    false, // standalone (only for specific needs)

    $module->getId() // current module Id

);
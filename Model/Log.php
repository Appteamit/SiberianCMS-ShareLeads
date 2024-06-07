<?php

class Shareleads_Model_Log extends Core_Model_Default {

    public function __construct($datas = array()) {
        parent::__construct($datas);
        $this->_db_table = 'Shareleads_Model_Db_Table_Log';
    }

    public function findNewUsers($app_id) {
        return $this->getTable()->findNewUsers($app_id);
    }

}
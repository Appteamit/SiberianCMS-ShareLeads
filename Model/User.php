<?php

class Shareleads_Model_User extends Core_Model_Default {

    public function __construct($datas = array()) {
        parent::__construct($datas);
        $this->_db_table = 'Shareleads_Model_Db_Table_User';
    }

}
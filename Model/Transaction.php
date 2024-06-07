<?php

class Shareleads_Model_Transaction extends Core_Model_Default {
	public function __construct($datas = []) {
                parent::__construct($datas);
                $this->_db_table = 'Shareleads_Model_Db_Table_Transaction';
	}
    
    public function getBalance($agent_id, $app_id, $value_id) {
        return $this->getTable()->getBalance($agent_id, $app_id, $value_id);
    }
}
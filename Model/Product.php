<?php
class Shareleads_Model_Product extends Core_Model_Default {
	public function __construct($datas = []) {
                parent::__construct($datas);
                $this->_db_table = 'Shareleads_Model_Db_Table_Product';
	}
    // getShop
    public function getShop($data = []) {
        return $this->getTable()->getShop($data);
    }
    public function getMyShop($data = []) {
        return $this->getTable()->getMyShop($data);
    }
}
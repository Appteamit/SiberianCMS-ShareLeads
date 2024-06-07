<?php

class Shareleads_Model_Db_Table_Transaction extends Core_Model_Db_Table {
    protected $_name = "shareleads_transaction";
	protected $_primary = "transaction_id";

    public function getBalance($agent_id, $app_id, $value_id) {
        $query = "SELECT 
            COALESCE(SUM(CASE WHEN transaction_type = 'DEBIT' THEN points ELSE 0 END), 0) AS total_debit,
            COALESCE(SUM(CASE WHEN transaction_type = 'CREDIT' THEN points ELSE 0 END), 0) AS total_credit,
            COALESCE(SUM(CASE WHEN transaction_type = 'CREDIT' THEN points ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN transaction_type = 'DEBIT' THEN points ELSE 0 END), 0) AS balance_points
        FROM shareleads_transaction
        WHERE agent_id = $agent_id AND app_id = $app_id AND value_id = $value_id";
        $data = $this->_db->fetchRow($query);
        if(count($data)){
            return $data;
        }else{
            return [
                'balance_points'=> 0,
                'total_credit'=> 0,
                'total_debit'=> 0
            ];
        }
    }
}
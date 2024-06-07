<?php

class Shareleads_Model_Db_Table_Log extends Core_Model_Db_Table {

    protected $_name = "shareleads_log";
    protected $_primary = "log_id";
    
    public function findNewUsers($app_id) {
        $query = "SELECT u.* 
                    FROM shareleads_user u
                    LEFT JOIN shareleads_log l ON u.user_id = l.user_id
                    WHERE l.user_id IS NULL 
                        AND u.type_id = 0 
                        AND u.app_id = $app_id 
                        AND u.created_at >= NOW() - INTERVAL 5 MINUTE";
        return $this->_db->fetchAll($query);
    }

}
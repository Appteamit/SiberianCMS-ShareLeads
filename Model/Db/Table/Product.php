<?php

class Shareleads_Model_Db_Table_Product extends Core_Model_Db_Table {
    protected $_name = "shareleads_product";
	protected $_primary = "product_id";

    public function getShop($data = []) {
        $app_id = $data['app_id'];
        $value_id = $data['value_id'];
        
        // Base URL for images
        $baseUrl = $data['baseUrl']."/images/application/$app_id/features/shareleads/";
        // Backup URL for empty product_img
        $backupUrl= $data['baseUrl'] .'/app/local/modules/Shareleads/icons/placeholder.png';
        
        $query = "SELECT *,
                        CASE WHEN product_img IS NOT NULL AND product_img != '' THEN CONCAT('$baseUrl', product_img)
                            ELSE '$backupUrl'
                        END AS product_img
                FROM shareleads_product
                WHERE app_id = ?
                    AND value_id = ?
                    AND CURRENT_DATE BETWEEN STR_TO_DATE(product_start_at, '%d/%m/%Y') AND STR_TO_DATE(product_expire_at, '%d/%m/%Y')
                    AND product_status = 1";

        return $this->_db->fetchAll($query, [$app_id, $value_id]);
    }
    public function getMyShop($data = []) {
        $app_id = $data['app_id'];
        $value_id = $data['value_id'];
        $transaction_type = 'DEBIT';
        $agent_id = $data['agent_id'];
        // Base URL for images
        $baseUrl = $data['baseUrl']."/images/application/$app_id/features/shareleads/";
        // Backup URL for empty product_img
        $backupUrl= $data['baseUrl'] .'/app/local/modules/Shareleads/icons/placeholder.png';
        $query = "SELECT t.*, p.product_name,p.product_description,p.product_points,p.product_start_at,p.product_expire_at,p.product_id,
               CASE WHEN p.product_img IS NOT NULL AND p.product_img != '' THEN CONCAT('$baseUrl', p.product_img)
                    ELSE '$backupUrl'
               END AS product_img
        FROM shareleads_transaction t
        JOIN shareleads_product p ON t.contract_id_or_product_id = p.product_id
        WHERE t.app_id = $app_id
            AND t.value_id = $value_id
            AND t.transaction_type = '$transaction_type'
            AND t.agent_id = $agent_id";
        return $this->_db->fetchAll($query);
    }
}
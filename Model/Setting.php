<?php

class Shareleads_Model_Setting extends Core_Model_Default {

    public function __construct($datas = array()) {
        parent::__construct($datas);
        $this->_db_table = 'Shareleads_Model_Db_Table_Setting';
    }

	/**
     * @return mixed
     */
    public function getCronInfo() {
        return $this->getTable()->getCronInfo();
    }
    // Deep Link
    /**
    * @param $valueId
    * @return array
    */
    public function getInappStates($valueId)
    {
        $inAppStates = [
            [
                'state' => 'shareleads-view',
                'offline' => false,
                'params' => [
                    'value_id' => $valueId,
                ],
            ],
        ];
        return $inAppStates;
    }

}
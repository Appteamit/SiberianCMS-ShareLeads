<?php

class Shareleads_TestController extends Application_Controller_Default

{



    public function indexAction()
    {
        $this->loadPartials();
    }
    // This is the test method to send the data to the datatable
    // remember to add the first colum null for the sake of displaying the plus button
    public function datatabledataAction()
    {

        $data[] = [
            null,
            "1",
            "John",
            "Doe",
            "12345",
            "italy",
        ];

        $this->_sendJson(["data" => $data]);
    }
}
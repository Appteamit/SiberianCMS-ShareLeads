<?php  

class Shareleads_Public_CronController extends Shareleads_Controller_Default 

{ 

    public function runAction() 

    { 
        $notifications_responce = Shareleads_Model_Db_Table_Setting::runCron();
        if($notifications_responce){
            echo p__("Shareleads", "Cron executed successfully.");
        }        
        exit;  

    }

    public function croninfoAction() {

        $setting = new Shareleads_Model_Setting();
        $cron_info = $setting->getCronInfo();

        // dd($cron_info);
        $default = new Core_Model_Default();
        $url = $default->getBaseUrl()."/Shareleads/public_cron/run";

        $html = [
            "last_error" => $cron_info[0]['last_error'],
            "last_error_date" => ($cron_info[0]['last_error_date'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_error_date']))) : '',
            "last_trigger" => ($cron_info[0]['last_trigger'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_trigger']))) : '',
            "last_success" => ($cron_info[0]['last_success'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_success']))) : '',
            "last_fail" => ($cron_info[0]['last_fail'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['last_fail']))) : '',
            "created_at" => ($cron_info[0]['created_at'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['created_at']))) : '',
            "updated_at" => ($cron_info[0]['updated_at'] != '0000-00-00 00:00:00') ? str_replace('-', '/', date('d-m-Y H:i:s', strtotime($cron_info[0]['updated_at']))) : '',
            "info_message" => p__("shareleads", "The cron is set to run every day after %s minute and if you want to run it now just click on the link above. If you want you can add this link to your own server cron.", 1),
            "cron_message" => p__("shareleads", "Note: It is recommended to set up the cron for 1 minute interval time on your own server using this URL %s.", $url),
            "url" => $url,
        ];
        $this->_sendJson($html);
    }

    

} 
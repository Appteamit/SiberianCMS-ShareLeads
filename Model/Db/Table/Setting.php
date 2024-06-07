<?php

class Shareleads_Model_Db_Table_Setting extends Core_Model_Db_Table {

    protected $_name = "shareleads_setting";
    protected $_primary = "setting_id";

	/**
     * @return mixed
     */
    public function getCronInfo() {
        return $this->_db->fetchAll("SELECT * FROM cron WHERE name LIKE 'Shareleads cron job.'");
    }

    public static function runCron() {
        $cron = new Shareleads_Model_Db_Table_Setting();
        $cron->signUpNewUser();    
        // $cron->processNewEvent();
        return true;
    }
    public function signUpNewUser() {
        try{
            $apps = $this->getShareleadsApps(); 
            if (count($apps)) {
                foreach ($apps as $app) { 
                    $app_id = $app['app_id'];
                    // get all users
                    $users = (new Shareleads_Model_Log())->findNewUsers($app_id);
                    // dd($users, date('Y-m-d H:i:s'));
                    if(count($users)){
                        foreach ($users as $user) {
                            $value_id = $user['value_id'];
                            
                            $email = new Shareleads_Model_Notification();
                            $email_setting = $email->find([
                                'value_id'=>$value_id,
                                'app_id'=>$app_id,
                            ]);
                            $emailTemplate = [];
                            if(count($email_setting->getData())){
                                $emailTemplate  = $email_setting->getData();
                                if($emailTemplate['notification_is_client']==1){
                                    $template = array_merge($emailTemplate, $user);
                                    $template['email_text']= $emailTemplate['notification_client_text'];
                                    $template['email_object']= $emailTemplate['notification_client_title'];
                                    $application = (new Application_Model_Application())->find($app_id);
                                    $template['app_name'] = $application->getName();
                                    $responce = $this->_sendEmail($template);
                                    if($responce['success']){
                                        $tags = ['@@name@@','@@app_name@@'];
                                        $strings = [$template['user_surname'].' '.$template['user_name'], $template['app_name']];
                                        $email_body = str_replace($tags, $strings, $template["email_text"]);
                                        $email_subject = str_replace($tags, $strings, $template["email_object"]);
                                        $log = (new Shareleads_Model_Log())->setData([
                                            'app_id'=>$app_id,
                                            'value_id'=>$value_id,
                                            'user_id'=>$user['user_id'],
                                            'user_name'=>$user['user_name'],
                                            'user_surname'=>$user['user_surname'],
                                            'user_email'=>$user['user_email'],
                                            'notification_title'=>$email_subject,
                                            'notification_text'=>$email_body,
                                            'log_type'=>0,
                                        ])->save();
                                    }
                                }
                            }
                            
                        }
                    }
                }
            }
        }catch(Exception $e){
            print_r($e);
        }
        return true;
    }
     public function getShareleadsApps() {
        $apps = $this->_db->fetchAll("SELECT DISTINCT application_option_value.app_id
            FROM application_option, application_option_value
            WHERE application_option.option_id = application_option_value.option_id
            AND application_option.code = 'Shareleads'");
        return $apps;
    }
    private function _sendEmail($data) {
        try {
            $responce = false;
            if ($data) {
                
                $tags = ['@@name@@','@@app_name@@'];
                $strings = [$data['user_surname'].' '.$data['user_name'], $data['app_name']];
                $email_body = str_replace($tags, $strings, $data["email_text"]);
                $email_subject = str_replace($tags, $strings, $data["email_object"]);
                
                # @version 4.8.7 - SMTP
                $mail = new Siberian_Mail();
                // if (array_key_exists('email_ccn', $data) && !is_array($data['email_ccn'])) {
                //     $data['email_ccn'] = preg_split('/\s*,\s*/', $data['email_ccn'], -1, PREG_SPLIT_NO_EMPTY);
                //     $mail->addCc($data['email_ccn']);
                // }
                
                $mail->setBodyHtml($email_body);
                $mail->addTo($data['user_email']);
                $mail->setSubject($email_subject);
                if (!$mail->send()) {
                    throw new Exception("Error Processing Request", 1);
                } else {
                    $responce = [ 
                        'success'=>true
                    ];
                }
            }
        } catch (Exception $e) {
            $responce = [ 
                'success'=>false,
                'msg'=> $e->getMessage(),
            ];
        }
        return $responce;
    }



	

}
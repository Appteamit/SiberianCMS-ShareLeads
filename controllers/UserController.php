<?php

class Shareleads_UserController extends Application_Controller_Default {
    public function toggleStatusAction()
    {
        $datas= $this->getRequest()->getParams();
        if ($datas && is_array($datas) && array_key_exists("customer_id",$datas) && $datas['customer_id']) {
            // dd($datas);
            $user = (new Shareleads_Model_User())->find([
                'customer_id'   => $datas['customer_id'],
                'value_id'   => $datas['value_id'],
            ]);

            // dd($user->getData());
            if(count($user->getData())){
                if((int)$user->getUserIsAdmin() == 1){
                    $user->setUserIsAdmin(0);
                }else{
                    $user->setUserIsAdmin(1);
                }
                $user->save();
            }
            $data = [
                'is_admin'=> $user->getUserIsAdmin(),
                'success' => true,
                'message' => p__("shareleads",'User status udpated successfully.'),
                'message_loader' => 0,
                'message_button' => 0,
                'message_timeout' => 2
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred. Please try again later.')
            ];
        }

        $this->_sendJson($data);
    }
    public function generateUniqueString($length = 7) {
        $uniqueId = uniqid();
        $uniqueString = substr(md5($uniqueId), 0, $length);
        return $uniqueString;
    }
    public function getAgentAction()
    {
        if ($datas= $this->getRequest()->getParams()) {
            $application = $this->getApplication();
            $app_id = $application->getId();
            $agent = (new Shareleads_Model_User())->find([
                'user_id'=>$datas['agent_id'],
                'app_id'=>$app_id,
                'value_id'=>$datas['value_id']
            ]);
            if(empty($agent->getAgentInvitationCode()) || is_null($agent->getAgentInvitationCode())){
                $agent->setAgentInvitationCode($this->generateUniqueString());
                $agent->setField(true);
            }else{
                $agent->setField(false);
            }
            $data = [
                'success' => true,
                'agent' => $agent->getData(),
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred while deleting the push. Please try again later.')
            ];
        }
        $this->_sendJson($data);
    }

    public function getUsersAction() {
        if ($datas = $this->getRequest()->getQuery()) {
            // dd($datas);
            try { 
                $application = $this->getApplication();
                $appId = $application->getId();
                $datas['app_id'] = $appId; 
                $sb_users = (new Customer_Model_Customer())->findAll([
                    'app_id'    => $datas['app_id']
                ]);
                // dd($sb_users);
                foreach ($sb_users as $user) {
                    $check_user = (new Shareleads_Model_User())->find([
                        'app_id'    => $datas['app_id'], 
                        'value_id'  => $datas['value_id'],
                        'customer_id'   => $user->getId()
                    ]);
                    // user not avaiable then save data
                    if(!count($check_user->getData())){
                        // echo "save";
                        $saveuser = new Shareleads_Model_User();
                        $saveuser->setData([
                            'customer_id'   => $user->getId(),
                            'app_id'        => $datas['app_id'],
                            'value_id'      => $datas['value_id'],
                            'user_name'     => $user->getFirstname(),
                            'user_surname'  => $user->getLastname(),
                            'user_email'    => $user->getEmail(),
                            'user_phone'    => $user->getMobile(),
                            'created_at'    => $user->getCreatedAt(),
                            'updated_at'    => $user->getUpdatedAt(),
                        ])->save();
                        $saveuser->setCreatedAt($user->getCreatedAt())->save();
                    }else{
                        // update data
                        $saveuser = new Shareleads_Model_User();
                        $saveuser->setData([
                            'user_id'       => $check_user->getId(),
                            'customer_id'   => $user->getId(),
                            'app_id'        => $datas['app_id'],
                            'value_id'      => $datas['value_id'],
                            'user_name'     => $user->getFirstname(),
                            'user_surname'  => $user->getLastname(),
                            'user_email'    => $user->getEmail(),
                            'user_phone'    => $user->getMobile(),
                            'created_at'    => $user->getCreatedAt(),
                            'updated_at'    => $user->getUpdatedAt(),
                        ])->save();

                    }
                }
                $filters = [
                    'app_id'        => $datas['app_id'],
                    'value_id'      => $datas['value_id'],
                    'user_is_admin'      => 1,
                ];
                
                if(array_key_exists("is_admin", $datas)){
                    $filters['user_is_admin'] = $datas['is_admin'];
                }
                $users = (new Shareleads_Model_User())->findAll($filters);
                $data = [];
                foreach ($users as $user) {
                    $customer_id = $user->getCustomerId();
                    // $status = '<i class="fa fa-user-o" onclick="toggleStatus('.$customer_id.', this)" style="font-size: 1.3em;position:relative;top:1px;color:#f52b2b" aria-hidden="true"></i>';
                    // if($user->getUserIsAdmin()){
                    //     $status = '<i class="fa fa-user" onclick="toggleStatus('.$customer_id.', this)" style="font-size: 1.3em;position:relative;top:1px;color:#f52b2b" aria-hidden="true"></i>';
                    // }
                    
                    $status = '<button type="button"  class="btn default_button color-blue"><i class="fa fa-pencil-square-o" onclick="editAgent('.$user->getId().')" aria-hidden="true"></i></button>';
                    $status .= '&nbsp;<button title="'.p__("shareleads","Agent Ledger").'" type="button" class="btn default_button color-green"><i class="fa fa-file-text-o" onclick="openLedger('.$user->getCustomerId().')" aria-hidden="true"></i></button>';
                    $status .= '&nbsp;<button title="'.p__("shareleads","Agent Subscribers").'" type="button" class="btn btn-info"><i class="fa fa-users" onclick="openClient('.$user->getCustomerId().')" aria-hidden="true"></i></button>';
                    $subscribers = (new Shareleads_Model_User())->findAll([
                        'user_invitation_code'=> $user->getAgentInvitationCode(),
                        'app_id' => $datas['app_id'],
                        'value_id' => $datas['value_id']
                    ])->count();
                    // dd($subscribers);
                    if($subscribers == 0){
                        $status .= '&nbsp;<button type="button" class="btn default_button color-red"><i class="fa fa-trash" onclick="deleteAgent('.$user->getId().')" aria-hidden="true"></i></button>';
                    }
                    $data[] = [
                        $status,
                        $user->getUserName(),
                        $user->getUserSurname(),
                        $user->getUserEmail(),
                        $user->getUserPhone(),
                        $user->getAgentCommissionPoints(),
                        $user->getAgentInvitationCode(),
                        $user->getCreatedAt()
                    ];
                }
                
                $payload = [
                    "data" => $data,
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads",$e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
    public function getClientsAction() {
        if ($datas = $this->getRequest()->getQuery()) {
            // dd($datas);
            try { 
                $application = $this->getApplication();
                $appId = $application->getId();
                $datas['app_id'] = $appId; 
                $filters = [
                    'app_id'        => $datas['app_id'],
                    'value_id'      => $datas['value_id'],
                    'user_is_admin'      => 0,
                ];
                $users = (new Shareleads_Model_User())->findAll($filters);
                $data = [];
                foreach ($users as $user) {
                    $customer_id = $user->getCustomerId();
                    $agent_name= '';
                    if(!is_null($user->getUserInvitationCode()) && !empty($user->getUserInvitationCode())){
                        $agent = (new Shareleads_Model_User())->find([
                            'app_id'        => $datas['app_id'],
                            'value_id'      => $datas['value_id'],
                            'agent_invitation_code'=> $user->getUserInvitationCode()
                        ]);
                        if(count($agent->getData())){
                            $agent_name = $agent->getUserName().''.$agent->getUserSurname();
                        }
                    }
                    $data[] = [
                        null,
                        $user->getUserName(),
                        $user->getUserSurname(),
                        $user->getUserEmail(),
                        $user->getUserPhone(),
                        $agent_name,
                        $user->getCreatedAt()
                    ];
                }
                
                $payload = [
                    "data" => $data,
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads",$e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
    public function getLogsAction() {
        if ($datas = $this->getRequest()->getQuery()) {
            // dd($datas);
            try { 
                $application = $this->getApplication();
                $appId = $application->getId();
                $datas['app_id'] = $appId; 
                $filters = [
                    'app_id'        => $datas['app_id'],
                    'value_id'      => $datas['value_id'],
                ];
                $users = (new Shareleads_Model_Log())->findAll($filters);
                $data = [];
                foreach ($users as $user) {
                    $data[] = [
                        $user->getId(),
                        $user->getUserName(),
                        $user->getUserSurname(),
                        $user->getUserEmail(),
                        $user->getNotificationTitle(),
                        ((int)$user->getLogType())?p__("shareleads",'Agent'): p__("shareleads",'Client'),
                        $user->getCreatedAt()
                    ];
                }
                
                $payload = [
                    "data" => $data,
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads",$e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
    // destroy-assign
    public function destoryAction()
    {
        $datas= $this->getRequest()->getParams();
       
        if ($datas && is_array($datas) && array_key_exists("user_id",$datas) && $datas['user_id']) {
            $application = $this->getApplication();
            $appId = $application->getId();
            $user = (new Shareleads_Model_User())->find([
               'app_id'        => $appId,
               'user_id'       => $datas['user_id'],
                'value_id'      => $datas['value_id'],
            ]);
            if(count($user->getData())){
                $user->setUserIsAdmin(0)->save();   
            }
            $data = [
                'success' => true,
                'message' => p__("shareleads",'The agent has been successfully removed.'),
                'message_loader' => 0,
                'message_button' => 0,
                'message_timeout' => 2
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__("shareleads",'Your request is invalid. Please refresh the page and retry the action.')
            ];
        }

        $this->_sendJson($data);
    }

    public function saveAgentAction()
    {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $application = $this->getApplication();
            $appId = $application->getId();
            $data['app_id'] = $appId;
            $data['user_is_admin'] = 1;
            $errors = '';
            if (empty($data["user_id"])) {
                $errors .= p__("shareleads",'user cannot be empty.') . "<br>";
            }
            if (empty($data["agent_commission_points"])) {
                $errors .= p__("shareleads",'agent commission points cannot be empty.') . "<br>";
            }
            // if (empty($data["type_id"])) {
            //     $errors .= p__("shareleads",'agent type cannot be empty.') . "<br>";
            // }
            if (empty($data["agent_invitation_code"])) {
                $errors .= p__("shareleads",'agent intvitation code cannot be empty.') . "<br>";
            }
            

            $user  = (new Shareleads_Model_User())->find([
                'user_id'=> $data['user_id'],
                'app_id'=> $data['app_id'],
                'value_id'=> $data['value_id']
            ]);
            if(count($user->getData()) && !empty($user->getAgentInvitationCode()) && !is_null($user->getAgentInvitationCode())){
                unset($data['agent_invitation_code']);
            }

            $check_unique_code = (new Shareleads_Model_User())->find([
                'agent_invitation_code'     => $data['agent_invitation_code'],
            ]);
            if(count($check_unique_code->getData()) && $check_unique_code->getId() != $user->getId()){
                $errors .= p__("shareleads",'Please enter a unique code.') . "<br>";
            }
            
            
            if (!empty($errors)) {
                throw new Exception($errors);
            }
            
            $msg = "Agent has been successfully saved.";
            if($user->getIsAdminEmail()==0){
                $email = new Shareleads_Model_Notification();
                $email_setting = $email->find([
                    'value_id'=>$data['value_id'],
                    'app_id'=>$appId,
                ]);
                $emailTemplate = [];
                if(count($email_setting->getData())){
                    $emailTemplate  = $email_setting->getData();
                    if($emailTemplate['notification_is_agent']==1){
                        $user = $user->getData();
                        $template = array_merge($emailTemplate, $user);
                        $template['email_text']= $emailTemplate['notification_client_text'];
                        $template['email_object']= $emailTemplate['notification_client_title'];
                        $application = (new Application_Model_Application())->find($appId);
                        $template['app_name'] = $application->getName();
                        // dd($template);
                        $responce = $this->_sendEmail($template);
                        if($responce['success']){
                            $data['is_admin_email']=1;
                            $tags = ['@@name@@','@@app_name@@'];
                            $strings = [$template['user_surname'].' '.$template['user_name'], $template['app_name']];
                            $email_body = str_replace($tags, $strings, $template["email_text"]);
                            $email_subject = str_replace($tags, $strings, $template["email_object"]);
                            $log = (new Shareleads_Model_Log())->setData([
                                'app_id'=>$appId,
                                'value_id'=>$data['value_id'],
                                'user_id'=>$user['user_id'],
                                'user_name'=>$user['user_name'],
                                'user_surname'=>$user['user_surname'],
                                'user_email'=>$user['user_email'],
                                'notification_title'=>$email_subject,
                                'notification_text'=>$email_body,
                                'log_type'=>1,
                            ])->save();
                        }
                    }
                }
            }
            (new Shareleads_Model_User())->setData($data)->save();
            $data = [
                    "success" => 1,
                    "message" => p__("shareleads",$msg)
                ];
        } catch (Exception $e) {
            $data = array(
                "error" => 1,
                "message" => p__("shareleads",$e->getMessage())
            );
        }
        $this->_sendJson($data);
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
<?php

class Shareleads_NotificationController extends Application_Controller_Default
{
    public function updloadfile($icon,$app_id) {
        $product_icon = "$icon";
        $ext = pathinfo($icon, PATHINFO_EXTENSION);
        $file = Core_Model_Directory::getTmpDirectory(true) . '/' . $icon;
        $dir_image = Core_Model_Directory::getBasePathTo("/images/application/" . $app_id);
        if (!is_dir($dir_image)) {
            mkdir($dir_image, 0775, true);
        }
        if (!is_dir($dir_image . "/features")) {
            mkdir($dir_image . "/features", 0775, true);
        }
        if (!is_dir($dir_image . "/features/shareleads")) {
            mkdir($dir_image . "/features/shareleads", 0775, true);
        }
        $dir_image .= "/features/shareleads/";
        $image_name = str_replace(" ", "_", $icon);
        if (file_exists($file)) {
            copy($file, $dir_image . $image_name);
            $product_icon = $icon;
        } else {
            $product_icon = $icon;
        }
        return $product_icon;
    }
    public function sharetextAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $application = $this->getApplication();
            $appId = $application->getId();
            $data['app_id'] = $appId;
            $errors = '';
            $check = (new Shareleads_Model_Share())->find([
                'value_id'=>$data['value_id'],
                // 'notification_type'=>$data['notification_type'],
                'app_id'=>$data['app_id'],
            ]);
            if(!count($check->getData())){
                $msg="setting save successfully.";
            }else{
                $data['share_id'] = $check->getId();
                $msg="setting edit successfully.";
            }
            // dd($data);
            $email = new Shareleads_Model_Share();
            $email->setData($data)->save();
            // if (!empty($data['notification_picture'])) {
            //     $icon = $data['notification_picture'];
            //     $data['notification_picture'] = $this->updloadfile($icon,$appId);
            // }else{
            //     unset($data['notification_picture']);
            // }
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
    public function saveAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            $application = $this->getApplication();
            $appId = $application->getId();
            $data['app_id'] = $appId;
            $errors = '';
            // if (empty($data["notification_type"])) {
            //     $errors .= p__("shareleads",'type cannot be empty.') . "<br>";
            // }
            // dd($data);
            
            
            if (array_key_exists("notification_is_admin", $data) && (int)$data['notification_is_admin']) {
                $data['notification_is_admin'] = 1;
                if (empty($data["notification_admin_title"])) {
                    $errors .= p__("shareleads",'title cannot be empty.') . "<br>";
                }
                if (empty($data["notification_admin_text"])) {
                    $errors .= p__("shareleads",'text cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_is_admin'] = 0;
            }
            if (array_key_exists("notification_is_agent", $data) && (int)$data['notification_is_agent']) {
                $data['notification_is_agent'] = 1;
                if (empty($data["notification_agent_title"])) {
                    $errors .= p__("shareleads",'title cannot be empty.') . "<br>";
                }
                if (empty($data["notification_agent_text"])) {
                    $errors .= p__("shareleads",'text cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_is_agent'] = 0;
            }
            if (array_key_exists("notification_is_client", $data) && (int)$data['notification_is_client']) {
                $data['notification_is_client'] = 1;
                if (empty($data["notification_client_title"])) {
                    $errors .= p__("shareleads",'title cannot be empty.') . "<br>";
                }
                if (empty($data["notification_client_text"])) {
                    $errors .= p__("shareleads",'text cannot be empty.') . "<br>";
                }
            }else{
                $data['notification_is_client'] = 0;
            }
            if (!empty($errors)) {
                throw new Exception($errors);
            }
            // dd($data);
            $check = (new Shareleads_Model_Notification())->find([
                'value_id'=>$data['value_id'],
                // 'notification_type'=>$data['notification_type'],
                'app_id'=>$data['app_id'],
            ]);
            if(!count($check->getData())){
                $msg="notification setting save successfully.";
            }else{
                $data['notification_id'] = $check->getId();
                $msg="notification setting edit successfully.";
            }
            // dd($data);
            $email = new Shareleads_Model_Notification();
            $email->setData($data)->save();
            // if (!empty($data['notification_picture'])) {
            //     $icon = $data['notification_picture'];
            //     $data['notification_picture'] = $this->updloadfile($icon,$appId);
            // }else{
            //     unset($data['notification_picture']);
            // }
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
    
    public function getpushdataAction()
    {
        if ($datas = $this->getRequest()->getParams()) {
            $application = $this->getApplication();
            $appId = $application->getId();
            $default = new Core_Model_Default();
            $base_url = $default->getBaseUrl(). '/images/application/' . $appId . '/features/shareleads/';
            $datas['app_id'] = $appId;
            $notification_setting = (new Shareleads_Model_Notification())->find([
                'value_id'=>$datas['value_id'],
                // 'notification_type'=>$datas['notification_type'],
                'app_id'=>$datas['app_id'],
            ]);
            if(count($notification_setting->getData())){
                $data = [
                    'base_url'=>$base_url,
                    'success' => true,
                    'data' => $notification_setting->getData(),
                    'message_loader' => 0,
                    'message_button' => 0,
                    'message_timeout' => 2
                ];
            }
            else{
                $data = [
                    'success' => false
                ];
            }
            
        } else {
            $data = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred while deleting the push. Please try again later.')
            ];
        }

        $this->_sendJson($data);
    }

}
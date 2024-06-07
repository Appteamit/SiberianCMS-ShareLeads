<?php

class Shareleads_ApplicationController extends Application_Controller_Default {
    
	public function viewAction() {
        $this->loadPartials();
    }
    public function destoryContractAction()
    {        
        if ($datas= $this->getRequest()->getParams()) {
            $value_id = $datas['value_id'];
            $application = $this->getApplication();
            $appId = $application->getId();
            $contract = (new Shareleads_Model_Contract())->find($datas['id']);
            $customer_id = $contract->getCustomerId();
            $user = (new Shareleads_Model_User())->find([
                'customer_id'=> $customer_id,
                'app_id'=> $appId,
                'value_id'=> $value_id
            ]);
            $agent_code = $user->getUserInvitationCode();
            $agent = (new Shareleads_Model_User())->find([
                'app_id'=> $appId,
                'value_id'=> $value_id,
                'agent_invitation_code'=> $agent_code
            ]);
            if($agent->getdata() && !is_null($agent_code) && !empty($agent_code)){
                $balance = (new Shareleads_Model_Transaction())->getBalance($agent->getCustomerId(), $appId, $value_id);
                $success = true;
                $message = '';
                if ((int)$balance['balance_points'] >= (int)$agent->getAgentCommissionPoints()) {
                    $transaction = (new Shareleads_Model_Transaction())->find([
                        'contract_id_or_product_id'=>$contract->getContractId(),
                        'transaction_type' => 'CREDIT',
                        'app_id' => $appId,
                        'value_id' => $value_id
                    ]);
                    if(count($transaction->getData())){
                        $transaction->delete();   
                    }
                    $contract->delete();
                    $message = p__("shareleads",'Contract deleted successfully.');
                }else{
                    $success = false;
                    $message = p__("shareleads",'This contract can not be deleted. The agent has used up his balance.');
                }
            }else{
                $contract->delete();
            }
            
            $data = [
                'success' => $success,
                'message' => $message,
                'message_loader' => 0,
                'message_button' => 0,
                'message_timeout' => 2
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred while deleting the push. Please try again later.')
            ];
        }

        $this->_sendJson($data);
    }
    // set-contract-form
    public function setContractFormAction()
    {
        if ($datas= $this->getRequest()->getParams()) {
            $application = $this->getApplication();
            $app_id = $application->getId();
            $contract = (new Shareleads_Model_Contract())->find([
                'contract_id'=>$datas['contract_id'],
                'app_id'=>$app_id,
                'value_id'=>$datas['value_id']
            ]);
            $data = [
                'success' => true,
                'contract' => $contract->getData(),
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred while deleting the push. Please try again later.')
            ];
        }
        $this->_sendJson($data);
    }
    public function getContractsAction() {
        if ($datas = $this->getRequest()->getQuery()) {
            // dd($datas);
            try { 
                $application = $this->getApplication();
                $appId = $application->getId();
                $datas['app_id'] = $appId; 
                
                $filters = [
                    'app_id'        => $datas['app_id'],
                    'value_id'      => $datas['value_id']
                ];
                
                // if(array_key_exists("is_admin", $datas)){
                //     $filters['user_is_admin'] = $datas['is_admin'];
                // }
                $contracts = (new Shareleads_Model_Contract())->findAll($filters);
                $data = [];
                // base-url
                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                foreach ($contracts as $contract) {
                    $customer = (new Customer_Model_Customer())->find($contract->getCustomerId());
                    $customer_id = $contract->getCustomerId();
                    $status = '<button type="button" class="btn default_button color-blue"><i class="fa fa-pencil-square-o" onclick="uploadContract('.$contract->getId().')" aria-hidden="true"></i></button>';
                    $status .= '&nbsp;<button type="button" class="btn default_button color-red"><i class="fa fa-trash-o" onclick="deleteContract('.$contract->getId().')" aria-hidden="true"></i></button>';
                    // if($user->getUserIsAdmin()){
                    //     $status = '<i class="fa fa-user" onclick="toggleStatus('.$customer_id.', this)" style="font-size: 1.3em;position:relative;top:1px;color:#f52b2b" aria-hidden="true"></i>';
                    // }
                    $file = "javascript;";
                    if(!is_null($contract->getContractPdfUrl()) && !empty($contract->getContractPdfUrl())){
                        $file = $base_url.'/images/application/' . $appId.'/features/shareleads/files/'.$contract->getContractPdfUrl();
                    }
                    $file = '&nbsp;<a class="btn default_button color-green" href="'.$file.'" download target="_blank"><i class="fa fa-download"></i></a>';
                    $status .= $file;
                    $data[] = [
                        $contract->getId(),
                        $customer->getFirstname() . $customer->getLastname(),
                        $contract->getContractTitle(),
                        $contract->getCreatedAt(),
                        $status,
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
    public function uploadContractAction() {
        if ($datas = $this->getRequest()->getPost()) {
            try {
                // dd($datas);
                $application = $this->getApplication();
                $appId = $application->getId();
                $errors = "";
                // file_name
                if(is_null($datas['contract_id']) || empty($datas['contract_id'])){
                    if (empty($_FILES["file"]["name"])) {
                        $errors .= p__("shareleads",'contract pdf cannot be empty.') . "<br>";
                    }
                }
                
                if (empty($datas['contract_title'])) {
                    $errors .= p__("shareleads",'contract title cannot be empty.') . "<br>";
                }
                if (empty($datas['customer_id'])) {
                    $errors .= p__("shareleads",'customer cannot be empty.') . "<br>";
                }
                // if (empty($datas['contract_pdf_url'])) {
                //     $errors .= p__("shareleads",'contract pdf cannot be empty.') . "<br>";
                // }
                
                if (!empty($errors)) {
                    throw new Exception($errors);
                }
                if($_FILES["file"]["name"]) {
                    // $filename = $_FILES["file"]["name"];
                    $name = $_FILES["file"]["name"];
                    $ext = end((explode(".", $name))); # extra () to prevent notice
                    $filename = uniqid() . time().".".$ext;
                    $source   = $_FILES["file"]["tmp_name"];
                    $type     = $_FILES["file"]["type"];
                    $size     = $_FILES["file"]["size"];

                    $baseUrl = Core_Model_Directory::getBasePathTo("/images/application/" . $appId);
                    if (!is_dir($baseUrl)) {
                        mkdir($baseUrl, 0775, true);
                    }
                    if (!is_dir($baseUrl . "/features")) {
                        mkdir($baseUrl . "/features", 0775, true);
                    }
                    if (!is_dir($baseUrl . "/features/shareleads")) {
                        mkdir($baseUrl . "/features/shareleads", 0775, true);
                    }
                    if (!is_dir($baseUrl . "/features/shareleads/files")) {
                        mkdir($baseUrl . "/features/shareleads/files", 0775, true);
                    }
                    $baseUrl .= "/features/shareleads/files/";
                    // $baseUrl = Core_Model_Directory::getBasePathTo("");
                    // if (!file_exists($baseUrl."/var/tmp")) {
                    //     mkdir($baseUrl."/var/tmp");
                    // }
                    // if (!file_exists($baseUrl."/var/tmp/shareleads")) {
                    //     mkdir($baseUrl."/var/tmp/shareleads");
                    // }
                    $target_path = $baseUrl.$filename;  // change this to the correct site path
                    if(move_uploaded_file($source, $target_path)) {
                        
                    }else {
                        $errors.=p__("shareleads","can not upload file please try again.")."<br>";
                    }
                }
               
                // dd($target_path);
                $msg = "Contract saved successfully.";
                $contract_data = [
                    'app_id' => $appId,
                    'value_id' => $datas['value_id'],
                    'customer_id' => $datas['customer_id'],
                    'contract_title' => $datas['contract_title'],
                    'contract_pdf_url' => $filename,
                    'contract_description' => $datas['contract_description']
                ];
                if(!$_FILES["file"]["name"]){
                    unset($contract_data['contract_pdf_url']);
                }

                if(!is_null($datas['contract_id']) && !empty($datas['contract_id'])){
                    $contract_data['contract_id'] = $datas['contract_id'];
                }

                $contract = (new Shareleads_Model_Contract())->setData($contract_data)->save();
                // get agent id
                $agent_id = 0;
                $points = 0;
                $user  = (new Shareleads_Model_User())->find([
                    'customer_id'=> $datas['customer_id'],
                    'app_id' => $appId,
                    'value_id' => $datas['value_id']
                ]);
                // dd($user->getData());
                if(count($user->getData())){
                    $agent_code = $user->getUserInvitationCode();
                    $agent  = (new Shareleads_Model_User())->find([
                        'agent_invitation_code'=> $agent_code,
                        'app_id' => $appId,
                        'value_id' => $datas['value_id']
                    ]);
                    if(count($agent->getData())){
                        $agent_id = $agent->getCustomerId();
                        $points = $agent->getAgentCommissionPoints();
                    }
                }
                // get agent id end
                $transaction = (new Shareleads_Model_Transaction())->find([
                    'app_id' => $appId,
                    'value_id' => $datas['value_id'],
                    'contract_id_or_product_id'=> $contract->getId(),
                ]);
                $trasaction_data = [
                    'app_id' => $appId,
                    'value_id' => $datas['value_id'],
                    'transaction_type'=> 'CREDIT',
                    'contract_id_or_product_id'=> $contract->getId(),
                    'title'=> $datas['contract_title'],
                    'agent_id'=> $agent_id,
                    'client_id'=> $datas['customer_id'],
                    'client_name'=> $user->getUserName() . ' ' .$user->getUserSurname(),
                    'points'=> $points,
                ];
                if(count($transaction->getData())){
                    $trasaction_data['transaction_id'] = $transaction->getTransactionId();
                }
                $transaction->setData($trasaction_data)->save();
                $payload = [
                    "success" => 1,
                    "message" => p__("shareleads",$msg)
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
    public function cropAction() {
        if ($datas = $this->getRequest()->getPost()) {
            try {
                $uploader = new Core_Model_Lib_Uploader();
                $file = $uploader->savecrop($datas);
                $datas = [
                    'success' => true,
                    'file' => $file,
                    'message' => p__("shareleads",'Info successfully saved.'),
                    'message_button' => 0,
                    'message_timeout' => 2,
                ];
            } catch (Exception $e) {
                $datas = [
                    'error' => true,
                    'message' => $e->getMessage()
                ];
            }

            $this->_sendJson($datas);
        }
    }
    
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

    public function getLedersAction() {
		$data = $this->getRequest()->getPost();
		try {
            $app_id = $data['app_id'];
            $value_id = $data['value_id'];
            $customer_id = $data['agent_id'];
			$layout = new Siberian_Layout();
			$layout->setBaseRender('content', 'shareleads/application/ledgers.phtml', 'core_view_default');
            $transactions = (new Shareleads_Model_Transaction())->findAll([
                        'app_id'=> $app_id,
                        'value_id'=> $value_id,
                        'agent_id'=> $customer_id
                    ],'transaction_id DESC')->toArray();
            $balance = (new Shareleads_Model_Transaction())->getBalance($customer_id, $app_id, $value_id);
            $agent = (new Shareleads_Model_User())->find([
                'app_id'=> $app_id,
                'value_id'=> $value_id,
                'customer_id'=> $customer_id
            ]);
			$layout
				->getBaseRender()
				->setAgent($agent->getData())
				->setTransactions($transactions)
				->setBalance($balance)
				->setAppId($data['app_id'])
				->setValueId($data['value_id']);

            echo $layout->render();
			die;
		} catch (Exception $e) {
            die($e->getMessage());
        }
	}
    public function getClientsAction() {
		$data = $this->getRequest()->getPost();
		try {
            $app_id = $data['app_id'];
            $value_id = $data['value_id'];
            $customer_id = $data['agent_id'];
			$layout = new Siberian_Layout();
			$layout->setBaseRender('content', 'shareleads/application/clients.phtml', 'core_view_default');
            $agent = (new Shareleads_Model_User())->find([
                'app_id'=> $app_id,
                'value_id'=> $value_id,
                'customer_id'=> $customer_id
            ]);
            $subscribers = (new Shareleads_Model_User())->findAll([
                'user_invitation_code'=> $agent->getAgentInvitationCode(),
                'app_id'=> $app_id,
                'value_id'=> $value_id
            ])->toArray();
            $balance = (new Shareleads_Model_Transaction())->getBalance($customer_id, $app_id, $value_id);
			$layout
				->getBaseRender()
				->setSubscribers($subscribers)
                ->setAgent($agent->getData())
				->setBalance($balance)
				->setAppId($data['app_id'])
				->setValueId($data['value_id']);

            echo $layout->render();
			die;
		} catch (Exception $e) {
            die($e->getMessage());
        }
	}
    
	
}
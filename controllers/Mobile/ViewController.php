<?php
class Shareleads_Mobile_ViewController extends Application_Controller_Mobile_Default {


	public function loadAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                $is_admin = 0;
				$user = (new Shareleads_Model_User())->find([
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                    'customer_id'=> $customer_id
                ]);
                
                if(count($user->getData()) && (int) $user->getUserIsAdmin()){
                    $is_admin = 1;
                    $datas['admin_id'] = $customer_id;
                    $datas['customer_id'] = $customer_id;
                }
                $datas['customer_id'] = $customer_id;
                // get all user contracts 
                $contracts_data = [];
                $contracts = (new Shareleads_Model_Contract())->findAll([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id
                ]);
                // base-url
                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                foreach ($contracts as $key => $contract) {
                    if(!empty($contract->getContractPdfUrl()) && !is_null($contract->getContractPdfUrl())){
                        $file = $base_url.'/images/application/' . $app_id.'/features/shareleads/files/'.$contract->getContractPdfUrl();
                        $contract->setFile($file);
                    }else{
                        $contract->setFile('');
                    }
                    $contracts_data[]=$contract->getData();
                }
                // end get all user contracts 
                $subscribers = [];
                if($is_admin){
                    $balance = (new Shareleads_Model_Transaction())->getBalance($customer_id, $app_id, $value_id);
                    $subscribers_list = (new Shareleads_Model_User())->findAll([
                        'user_invitation_code'=> $user->getAgentInvitationCode(),
                        'app_id'=> $app_id,
                        'value_id'=> $value_id
                    ])->toArray();
                }
                // admin section
                
                
                // admin end

                $payload = [
                    "application" => $this->getApplication()->getData(),
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"is_admin" => $is_admin,
					"balance" => $balance,
					"subscribers" => $subscribers_list,
					"contracts" => $contracts_data,
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer(),
					"admin" => $this->getSession()->getCustomer()->getData(),
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function contractDetailAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                
                // get all user contract
                $contract = (new Shareleads_Model_Contract())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                    'contract_id'=>$datas['contract_id']
                ]);
                // base-url
                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                if(!empty($contract->getContractPdfUrl()) && !is_null($contract->getContractPdfUrl())){
                    $file = $base_url.'/images/application/' . $app_id.'/features/shareleads/files/'.$contract->getContractPdfUrl();
                    $contract->setFile($file);
                }else{
                    $contract->setFile('');
                }
                // end get all user contracts 

                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"contract" => $contract->getData(),
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function saveCodeAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                
                $agent = (new Shareleads_Model_User())->find([
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                    'user_is_admin'=> 1,
                    'agent_invitation_code'=> $datas['invitation_code']
                ]);
                
                $message = "Invalid Code";
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                if(count($agent->getData())){                    
                    if(count($user->getData())){
                        $user->setUserInvitationCode($datas['invitation_code']);
                        $user->save();
                        $message = "Done";
                    }
                    
                }
                
                $payload = [
                    "success" => true,
                    "user" => $user->getData(),
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"message" => p__("shareleads",$message),
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function buyNowAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $message = '';
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                $app_id = $this->getApplication()->getId();
				$agent_id = $this->getSession()->getCustomer()->getId();

                $product = (new Shareleads_Model_Product())->find([
                    'product_id'=> $datas['product_id'],
                    'app_id'=> $app_id,
                    'value_id'=> $value_id
                ]);

                $transaction = (new Shareleads_Model_Transaction())->find([
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                    'contract_id_or_product_id'=> $datas['product_id'],
                    'agent_id'=> $this->getSession()->getCustomer()->getId(),
                    'transaction_type'=> 'DEBIT',
                ]);

                if(count($transaction->getData())){
                    $message = "Already bought";
                    throw new Exception($message);
                }

                $balance = (new Shareleads_Model_Transaction())->getBalance($agent_id, $app_id, $value_id);
                if(count($balance) && array_key_exists('balance_points', $balance) && $balance['balance_points'] < $product->getProductPoints()){
                    $message = "Insufficient Points";
                    throw new Exception($message);
                }else{
                    $message = "Done";
                    $buy = (new Shareleads_Model_Transaction())->setData([
                        'app_id'=> $app_id,
                        'value_id'=> $value_id,
                        'transaction_type'=> 'DEBIT',
                        'contract_id_or_product_id'=> $product->getId(),
                        'title'=> $product->getProductName(),
                        'agent_id'=> $this->getSession()->getCustomer()->getId(),
                        'points'=> $product->getProductPoints()
                    ])->save();
                }
                // dd($balance);
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"message" => p__("shareleads",$message),
					"agent_id" => $agent_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function getCodeAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"user" => $user->getData(),
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function getSubscribersAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                $subscribers = [];
                if((int) $user->getUserIsAdmin()){
                    $subscribers = (new Shareleads_Model_User())->findAll([
                        'user_invitation_code'=> $user->getAgentInvitationCode(),
                        'app_id'=> $app_id,
                        'value_id'=> $value_id
                    ])->toArray();
                }
                
                
                
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"subscribers" => $subscribers,
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function getShopAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                $shops = [];
                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                if((int) $user->getUserIsAdmin()){
                    $shops = (new Shareleads_Model_Product())->getShop([
                        'baseUrl'=> $base_url,
                        'app_id'=> $app_id,
                        'value_id'=> $value_id
                    ]);
                }
                
                
                
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"shops" => $shops,
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function getMyProductsAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                $shops = [];
                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                if((int) $user->getUserIsAdmin()){
                    $shops = (new Shareleads_Model_Product())->getMyShop([
                        'baseUrl'=> $base_url,
                        'app_id'=> $app_id,
                        'value_id'=> $value_id,
                        'agent_id'=> $customer_id,
                    ]);
                }
                
                
                
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"shops" => $shops,
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function getTransactionsAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                $transactions = [];
                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                $debit_icon= $base_url .'/app/local/modules/Shareleads/icons/debit.png';
                $credit_icon= $base_url .'/app/local/modules/Shareleads/icons/credit.png';
                if((int) $user->getUserIsAdmin()){
                    $transactions = (new Shareleads_Model_Transaction())->findAll([
                        'app_id'=> $app_id,
                        'value_id'=> $value_id,
                        'agent_id'=> $customer_id
                    ],'transaction_id DESC')->toArray();
                }
                
                
                $balance = (new Shareleads_Model_Transaction())->getBalance($customer_id, $app_id, $value_id);
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"transactions" => $transactions,
					"balance" => $balance,
					"debit_icon" => $debit_icon,
					"credit_icon" => $credit_icon,
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
	public function getShopDetailsAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                $product = [];
                $default  = new Core_Model_Default();
                $base_url  = $default->getBaseUrl();
                $base_url .= "/images/application/$app_id/features/shareleads/";
                if((int) $user->getUserIsAdmin()){
                    
                    $product = (new Shareleads_Model_Product())->find($datas['product_id']);
                    if(!is_null($product->getProductImg()) && !empty($product->getProductImg())){
                        $product->setProductImg($base_url.$product->getProductImg());
                    }
                    $transaction = (new Shareleads_Model_Transaction())->find([
                        'app_id'=> $app_id,
                        'value_id'=> $value_id,
                        'agent_id'=> $customer_id,
                        'transaction_type'=> 'DEBIT',
                        'contract_id_or_product_id'=> $datas['product_id'],
                    ]);
                    $product->setRedeem(0);
                    if(count($transaction->getData())){
                        $product->setRedeem(1);
                    }
                    $product = $product->getData();
                }
                
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"product" => $product,
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
    public function getAppLink($id) {
        // $default = new Core_Model_Default();
        // $base_url = $default->getBaseUrl(); // base url
        $base_url = 'https://' . __get('main_domain'); // base url
        $app = (new Application_Model_Application())->find([
            'app_id' => $id
        ]);
        $app_link = $base_url;
        if(count($app->getData())){
            $app_link = $app_link.'/'.$app->getKey();
        }
        // dd($app_link);
        return $app_link;
    }
	public function getCustomShareTextAction() {
        $app_id = $this->getApplication()->getId();
        if ($value_id = $this->getRequest()->getParam('value_id')) {
            try {
                $datas = $this->getRequest()->getParams();
                $datas['app_id'] = $app_id;
                
                
				$app_id = $this->getApplication()->getId();
				$customer_id = $this->getSession()->getCustomer()->getId();
                $user = (new Shareleads_Model_User())->find([
                    'customer_id'=> $customer_id,
                    'app_id'=> $app_id,
                    'value_id'=> $value_id,
                ]);
                $text = [];
                if((int) $user->getUserIsAdmin()){
                    $text = (new Shareleads_Model_Share())->find([
                        'app_id'=> $app_id,
                        'value_id'=> $value_id
                    ]);
                    $tags = ['@@agent_name@@','@@app_name@@','@@app_link@@','@@agent_code@@'];
                    $strings = [$user->getUserName().' ' .$user->getUserSurname(),$this->getAppLink($app_id),$this->getApplication()->getName(),$user->getAgentInvitationCode()];
                    $text->setShareText(str_replace($tags, $strings, $text->getShareText()));
                }
                $payload = [
                    "success" => true,
                    "page_title" => p__("shareleads", $this->getCurrentOptionValue()->getTabbarName()),
					"text" => $text->getData(),
					"customer_id" => $customer_id,
					"customer" => $this->getSession()->getCustomer()
                ];
            } catch (\Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => p__("shareleads", $e->getMessage())
                ];
            }
        } else {
            $payload = [
                'error' => true,
                'message' => p__("shareleads", 'An error occurred during process. Please try again later.')
            ];
        }
        $this->_sendJson($payload);
    }
}
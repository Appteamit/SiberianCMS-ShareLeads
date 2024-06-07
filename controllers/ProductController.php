<?php

class Shareleads_ProductController extends Application_Controller_Default {
    
	public function viewAction() {
        $this->loadPartials();
    }
    public function deleteProductAction()
    {
        if ($datas= $this->getRequest()->getParams()) {
            $product = (new Shareleads_Model_Product())->find($datas['id']);
            $product->delete();
            $message = p__("shareleads",'Product deleted successfully.');
            $data = [
                'success' => true,
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
    public function saveProductAction() {
        try {
            $request = $this->getRequest();
            $data = $request->getPost();
            // dd($data);

            $application = $this->getApplication();
            $app_id = $application->getId();
            $data['app_id'] = $app_id;
            
            $errors = "";
            if (empty($data["product_name"])) {
                $errors .= p__("shareleads",'product name cannot be empty.') . "<br>";
            }
            if (empty($data["product_points"])) {
                $errors .= p__("shareleads",'product points cannot be empty.') . "<br>";
            }
            
            if (!empty($data["product_start_at"]) && !empty($data["product_expire_at"])) {
                // Convert string dates to DateTime objects
                $product_start_date = DateTime::createFromFormat('d/m/Y', $data["product_start_at"]);
                $product_expire_date = DateTime::createFromFormat('d/m/Y', $data["product_expire_at"]);

                // Check if start date is smaller than expire date
                if ($product_start_date > $product_expire_date) {
                    $errors .= p__("shareleads",'Start date is not smaller than expire date.') . "<br>";
                }
            }

            if (!empty($data['product_img'])) {
                $icon = $data['product_img'];
                $data['product_img'] = $this->updloadfile($icon,$app_id);
            }else{
                $data['product_img'] = '';
            }
            
            if (!empty($errors)) {
                throw new Exception($errors);
            }
            if($data['product_id']=='0' || $data['product_id']=='' || !array_key_exists("product_id",$data)){
                unset($data['product_id']);
                $msg = "Product has been successfully saved.";
            }else{
                $msg = "Product has been successfully updated.";
            }
            (new Shareleads_Model_Product())->setData($data)->save();
            
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
    public function getProductAction()
    {
        if ($datas= $this->getRequest()->getParams()) {
            $application = $this->getApplication();
            $app_id = $application->getId();
            $product = (new Shareleads_Model_Product())->find([
                'product_id'=>$datas['product_id'],
                'app_id'=>$app_id,
                'value_id'=>$datas['value_id']
            ]);
            $data = [
                'success' => true,
                'product' => $product->getData(),
            ];
        } else {
            $data = [
                'error' => true,
                'message' => p__("shareleads",'An error occurred while deleting the push. Please try again later.')
            ];
        }
        $this->_sendJson($data);
    }
    public function findAllProductsAction() {
        if ($datas = $this->getRequest()->getQuery()) {
            // dd($datas);
            try { 
                $application = $this->getApplication();
                $appId = $application->getId();
                $datas['app_id'] = $appId; 
                $products = (new Shareleads_Model_Product())->findAll([
                    'app_id'    => $datas['app_id'], 
                    'value_id'  => $datas['value_id'],
                ]);
                $products_array = [];
                foreach ($products as $product) {
                    $redeemed = (new Shareleads_Model_Transaction())->findAll([
                        'app_id'    => $appId,
                        'value_id'  => $datas['value_id'],
                        'contract_id_or_product_id' => $product->getId(),
                        'transaction_type' => 'DEBIT',
                    ])->count();
                    $status = '<button type="button" class="btn default_button color-blue"><i class="fa fa-pencil-square-o" onclick="editProduct('.$product->getId().', this)" aria-hidden="true"></i></button>';
                    if($redeemed==0){
                        $status .= '&nbsp;<button type="button" class="btn default_button color-red"><i class="fa fa-trash-o" onclick="deleteProduct('.$product->getId().')" aria-hidden="true"></i></button>';
                    }
                    $products_array[] = [
                            $product->getId(),
                            $product->getProductName(),
                            $product->getproduct_start_at(),
                            $product->getproduct_expire_at(),
                            $product->getproduct_points(),
                            $redeemed,
                            $status,
                    ];
                }
                $payload = [
                    "data" => $products_array,
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

    
	
}
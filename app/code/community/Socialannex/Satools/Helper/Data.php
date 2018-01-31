 <?php
/**
 * Sample Widget Helper
 */
class Socialannex_Satools_Helper_Data extends Mage_Core_Helper_Abstract
{
	 public $modelRnrObj = '';
    public $siteId = '';
    public $blockObj = '';
    public $isEnable = '0';
    public $productPageId = "2";
    public $categoryPageId = "3";
    public $orderPageId = "8";
    public $categoryType = '';
    public $enableCategoryGallery = 0;
    public $satemplateId = 0;
    public $loadCssFromCDN = 0;
    public $isRAFEnable = '0';
    public $isVcEnable = 0;
    public $enableInlineRating = 0;
    public $templateId = 0;
	
	public function __construct() {
        //get Site Id
        $this->siteId = Mage::getStoreConfig('satools/sageneral/text_field');


        //activate Module flag
        $this->isRnrEnable = Mage::getStoreConfig('satools/enableModule/enableRNR');

        //Common Settings like page id
        $this->productPageId = Mage::getStoreConfig('satools/saPageId/productPage');
        $this->categoryPageId = Mage::getStoreConfig('satools/saPageId/categoryPage');
        $this->orderPageId = Mage::getStoreConfig('satools/saPageId/orderPage');

        //Create RNR model class Object
        $this->modelRnrObj = Mage::getModel('satools/reviewmodel');
        //create block class object
        $class = Mage::getConfig()->getBlockClassName('satools/satools');
        $this->blockObj = new $class;

        //RNR related Varaible
        $this->satemplateId = Mage::getStoreConfig('satools/saRNR/rnrTemplateId');
        $this->loadCssFromCDN = Mage::getStoreConfig('satools/saRNR/rnrCdnCSS');
        $this->enableInlineRating = Mage::getStoreConfig('satools/saRNR/enableRatings');
    }
	 public function getAverageRating($data = array()) {

        $output = '';
        if ($this->isRnrEnable == 1) {

            if (empty($data))
                return false;
            if ($this->enableInlineRating == 1) {
                $templateId = $this->satemplateId;
                if (isset($data['templateId'])) {
                    $templateId = $data['templateId'];
                }
                $output = $this->blockObj->getAvgRating(array("productId" => $data['productId'],
                    "templateId" => $templateId,
                    "siteId" => $this->siteId,
                    "categoryPage"=>$data['categoryPage']));
            }
        }

        return $output;
    }

    public function getproductreview($data = array()) {

        if (empty($data))
            return false;
        $reviewHtml = '';
        if ($this->isRnrEnable) {
            $templateId = $this->satemplateId;
            if (isset($data['templateId'])) {
                $templateId = $data['templateId'];
            }
            //get RNR extra product information.
            $extraParam = $this->blockObj->saRnrParameter(array("productId" => $data['productId'],
                "templateId" => $templateId,
                "siteId" => $this->siteId));
            $reviewData = $this->modelRnrObj->getproductreview1(array("productId" => $data['productId'], "templateId" => $templateId));

            $rnrCSS = $this->getRNRcss(array("templateId" => $templateId,
                "siteId" => $this->siteId));
            $reviewHtml .= $extraParam . PHP_EOL;
            $reviewHtml .= $rnrCSS . PHP_EOL;
            $reviewHtml .= '<div id="socialannex-reviewrating-bottom">' . $reviewData["reviewHtml"] . '</div>';
        }
        return array("reviewCount"=>$reviewData["reviewCount"],"reviewHtml"=>$reviewHtml);
    }

    public function getRNRcss($data = array()) {
        $rnrcss = '';
        if ($this->isRnrEnable == 1) {
            $templateId = $this->satemplateId;
            if (isset($data['templateId'])) {
                $templateId = $data['templateId'];
            }
            $rnrcss = $this->blockObj->loadRNRcss(array("siteId" => $this->siteId,
                "templateId" => $templateId));
            $failureCSSFuncton = '';
            $loadCssFromCDN = Mage::getStoreConfig('satools/saRNR/rnrCdnCSS');
            if ($loadCssFromCDN == 1) {
                $rnrcssUrl = '//cdn.socialannex.com/getcss/' . $this->siteId . '/28/getcss_' . $templateId . '.css';
                $rnrcss = '<link rel="stylesheet" type="text/css" media="screen"  onerror="sa_s28_loadCSSBackup();" href="' . $rnrcssUrl . '" />';
                $failureCSSFuncton = $this->blockObj->saLoadCssFromCDN(array("siteId" => $this->siteId,
                    "templateId" => $templateId));
            }

            $rnrcss = $failureCSSFuncton . $rnrcss;
        }
        return $rnrcss;
    }
    public function loadsauniversal($data = array()) {
        $pageId = $this->productPageId;
        if(isset($data['pageId']) && $data['pageId'] != ''){
           $pageId =  $data['pageId'];
        }
        $universal = $this->blockObj->loadsauniversal(array("pageId" => $pageId,
            "siteId" => $this->siteId));
        return $universal;
    }

    public function getLoggedUserInfo() {
        $customerInfo = '';
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
            $customerID = $customerData->getId(); //entity_id can be used as id
            $firstName = $customerData->getFirstname();
            $lastName = $customerData->getLastname();
            $email = $customerData->getEmail();
            $customerInfo = array("customerId" => $customerID, "customerEmail" => $email, "firstName" => $firstName, "lastName" => $lastName);
        }
        return $customerInfo;
    }

	public function saSaleTrack()
    {
		$emailId = "";
			$firstName = "";
			$lastName = "";
			$name = "";
        $session = Mage::getSingleton('checkout/session')->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($session);
        $customerId = $order->getCustomerId();
        if ($customerId != '') {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $emailId = $customer->getEmail();
			$name = $customer->getName();
			$firstName = $customer->getFirstname();
            $lastName = $customer->getLastname();
        } else {
            $emailId = $order['customer_email'];
			$firstName = $order['customer_firstname'];
			$lastName = $order['customer_lastname'];
			$name =  $firstName." ".$lastName;
		}
		$storeConfig = "satools/sageneral/text_field";
        $saSiteId = Mage::getStoreConfig($storeConfig);
		//$saSiteId = '1';
        $orderId = $order->getRealOrderId();
        $saleAmount = $order->getGrandTotal();
		$coupon= $order->getCouponCode();
		$orderDate = $order['created_at'];
		$orderItems = $order->getItemsCollection();//collect product information from order id
		$count = 1;
		foreach ($orderItems as $item){
 			$productId = $item->product_id;
    		$productSku = $item->sku;
			$productQuantity = $item->qty_ordered;
    		$productName = str_replace("'","^^",$item->getName());
			$productName = str_replace("&","and",$productName);
    		$_product = Mage::getModel('catalog/product')->load($productId);
			$productUrl  = $_product->getProductUrl();
			$productPrice= $item->getPrice(); //product's regular Price
    		$cats = $_product->getCategoryIds();
			$categoryId = $cats[0]; // just grab the first id
    		$category = Mage::getModel('catalog/category')->load($categoryId);
    		$categoryName = str_replace("'","^^",$category->getName());
			$categoryName = str_replace("&","and",$categoryName);
			$prductImageUrl = Mage::helper('catalog/image')->init($_product, 'thumbnail');
			$productArray[$count] = array("id"=>$productId,"sku"=>$productSku,"price"=>$productPrice,"qty"=>$productQuantity,"product_name"=>$productName,"product_url"=>htmlentities($productUrl),"product_image_url"=>htmlentities($prductImageUrl),"category_id"=>$categoryId,"category_name"=>$categoryName) ;
			$count++;
		}
		$orderData = json_encode($productArray);
		$orderData = str_replace('\/','/',$orderData);
		
       if ($emailId != "" && $orderId != "") {
            $script = "<script src='https://c.socialannex.com/c-sale-track/?";
            $script .= "site_id=$saSiteId&order_id=$orderId";
            $script .= "&sale_amount=$saleAmount&email_id=$emailId&exclude_products=$orderData&name=$name&purchase_date=$orderDate&coupon=$coupon'></script>";
            return $script;
	   }
    }
    //End here
} 
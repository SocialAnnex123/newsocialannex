<?php
class Socialannex_Satools_Model_Reviewmodel extends Mage_Core_Model_Abstract
{
    protected $_siteId;
    protected $_hostName;
    protected $_userName;
    protected $_password;

    //local file path
    protected $_localFilePath;
    protected $_localFileName;

    //remote file path variable
    protected $_remoteFilePath;
    protected $_remoteFileName;
    protected $_remoteFullPath;

    protected $_templateId;
    protected $_isRnrEnable;
    protected $_enableInlineRating;


    protected function _construct()
    {
        //get site id from config section
        $this->_siteId =Mage::getStoreConfig('satools/sageneral/text_field');

        //initialize rnr module variable
        $this->_hostName =   Mage::getStoreConfig("satools/saRNR/ftpHostName");
        $this->_userName =   Mage::getStoreConfig("satools/saRNR/ftpUserName");
        $this->_password =   Mage::getStoreConfig("satools/saRNR/ftpPassword");

        //initialize local file path for generate and upload avg rating csv file
        $this->_localFilePath= Mage::getBaseDir("var") . DS . "import" . DS . "socialannexfeeds";
		$this->_localFileName="average-ratings-".date("U").".csv";
        

        $this->_remoteFilePath = Mage::getStoreConfig("satools/saRNR/feedPath");
        $this->_remoteFileName = Mage::getStoreConfig("satools/saRNR/feedFileName");
		$this->_remoteFullPath= "/".$this->_remoteFilePath. "/" .$this->_remoteFileName ;
        $this->_templateId = Mage::getStoreConfig('satools/saRNR/rnrTemplateId');
        $this->_enableInlineRating = Mage::getStoreConfig('satools/saRNR/enableRatings');
        $this->_isRnrEnable =  Mage::getStoreConfig('satools/enableModule/enableRNR');

    }
    

    //Below function is used to load review of a product
    public function getproductreview1($data)
    {
        if(empty($data)) return false;
        //get product information from sku
        $_product=Mage::getModel('catalog/product')->load($data['productId']);
        $productUrl = $_product->getProductUrl();
        if ($data['templateId'] != '') {
            $this->_templateId = $data['templateId'];
        }
        $currentPageUrl = '';
        $saProductSKU= $data['productId'];
        if ($data['productId'] == '') {
            $saProductSKU= 0;
            $currentPageUrl = str_replace("/", "*", $productUrl);
            $currentPageUrl = str_replace("?", "|", $currentPageUrl);
            $currentPageUrl = str_replace("&", "^", $currentPageUrl);
        }
        $saDomainUrl  = 'http://s28.socialannex.com/s28_curl_file-v1.php?';
        $saDomainUrl = $this->appendParams($saDomainUrl, "site_id", $this->_siteId);
        $saDomainUrl = $this->appendParams($saDomainUrl, "location", $currentPageUrl);
        $saDomainUrl = $this->appendParams($saDomainUrl, "proid", $saProductSKU);
        $saDomainUrl = $this->appendParams($saDomainUrl, "template_id", $this->_templateId);
        /*** SA Curl Call ****/
        $curl = new Varien_Http_Adapter_Curl();
        $curl->write(Zend_Http_Client::GET, $saDomainUrl, '1.0');
        $saOutput = $curl->read();
        $outputArray = preg_split('/^\r?$/m', $saOutput, 2);
        $saReadReview= explode("[BRK]", $outputArray[1]);
        $saReviewTopContent = $saReadReview[0];
        $saReviewBottomContent = $saReadReview[1];
       return array("reviewCount"=>$saReadReview[2],"reviewHtml"=>$saReviewBottomContent);        
    }
    protected function appendParams($url, $key, $val)
    {
        $seperator = "?";  
        if(strpos($url, "?") !== false)
          $seperator = "&";
        $url .= $seperator.$key."=".urlencode($val);
        return $url;
    }


    //below function is used to parse avg. rating from reverse feed file

    public function getReverseFeed()
    {
        if($this->_isRnrEnable == 1){
			if($this->_enableInlineRating == 1){
				$saReadCsvOutput= '';
                $this->downloadSAFile();
                $this->createProductAttributesIfNecessary();
                $saReadCsvOutput  = $this->readCsvFile();
                if (file_exists($this->_localFilePath.DS.$this->_localFileName)) {
                    //unlink($this->_localFilePath.DS.$this->_localFileName);
                }
            }

        }
    }//end function here

    public function downloadSAFile(){
		Mage::log("Socialannex - starting download from  server");
        $ioObject = new Varien_Io_File();
        try {
            if (!$ioObject->fileExists($this->_localFilePath, false)) {
                $ioObject->mkdir($this->_localFilePath, 0777, true);
            }
        } catch (Exception $e) {
            Mage::log("'".$this->_localFilePath."' failed to create download feed.  Error: " . $e->getTraceAsString());
            return false;
        }
        if (!$ioObject->isWriteable($this->_localFilePath)) {
            Mage::log("Socialannex - local directory '".$this->_localFilePath."' is not writable.");
            return false;
        }
        //Establish a connection to the FTP host
        Mage::log("Socialannex - beginning file download");
		$connection = ftp_connect($this->_hostName);
        $login = ftp_login($connection, $this->_userName, $this->_password);
        ftp_pasv($connection, true);
        if (!$connection || !$login) {
			Mage::log("Socialannex - FTP connection attempt failed!");
            return false;
        }
		try {
			ftp_get($connection, $this->_localFilePath . DS . $this->_localFileName, "/home/plaesocial".$this->_remoteFullPath, FTP_BINARY);
		} catch (Exception $ex) {
            Mage::log("Socialannex - Exception downloading file: " . $ex);
        }
        //Validate file was downloaded
        if (!$ioObject->fileExists($this->_localFilePath . DS . $this->_localFileName, true)) {
            Mage::log("Socialannex - unable to download file '" . $this->_localFilePath . DS . $this->_localFileName . "'");
            return false;
        }
        return true;
    }
    public function createProductAttributesIfNecessary()
    {
        $attributeModel = Mage::getModel('catalog/resource_eav_attribute');
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $customAttributes = array("sa_average_rating_html", "sa_review_count", "sa_average_rating");
        $attributesbackendType = array("text",'int','decimal');
        $attributesfrontendInput = array("textarea",'text','text');
        $attricount = count($customAttributes);
        for ($i=0;$i<$attricount;$i++) {
            $attribute = $attributeModel->loadByCode($entityTypeId, $customAttributes[$i]);
            if (!$attribute->getId()) {
                $this->createProductAttribute($customAttributes[$i], $attributesbackendType[$i], $attributesfrontendInput[$i]);
            }
        }
    }//End function here
    public function createProductAttribute($attribCode, $backendType, $frontendinput)
    {
        $model = Mage::getModel('catalog/resource_eav_attribute');
        $data = array(
            'attribute_code' => $attribCode,
            'is_global' => '1',
            'frontend_input' => $frontendinput, // equivalent of a text field
            'default_value_text' => '',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '',
            'is_unique' => '0',
            'is_required' => '0',
            //'apply_to' => array(),     // Apply to everything
            'is_configurable' => '0',
            'is_filterable' => '0',
            'is_filterable_in_search' => '0',
            'is_searchable' => '1',
            'is_visible_in_advanced_search' => '1',
            'is_comparable' => '0',
            'is_used_for_price_rules' => '0',
            'is_wysiwyg_enabled' => '1',
            'is_html_allowed_on_front' => '1',
            'is_visible_on_front' => '1',
            'used_in_product_listing' => '1',
            'used_for_sort_by' => '0',
            'frontend_label' => array($attribCode)
        );
        $data['backend_type'] = $backendType; //$model->getBackendTypeByInput($data['frontend_input']);
        $model->addData($data);
        $model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
        $model->setIsUserDefined(1);
        try {
            $model->save();
        } catch (Exception $e) {

        }
    }//End function here

    public function readCsvFile()
    {
        $csv = new Varien_File_Csv();
        try {
            $csvData = $csv->getData($this->_localFilePath.DS.$this->_localFileName);
			//print_r($csvData);
            $foundval = 'Not Found';
            $row =0;
            foreach ($csvData as $k => $v) {
                if (trim(strtolower($v[0]))!=trim(strtolower('Product ID'))) {
					echo "Product Id==>".$v[0]."<===>".$v[1];
                    $foundval = $this->storeProductData(array("productId"=>$v[0],
                                                        "reviewAverage"=>$v[1],
                                                        "reviewCount"=>$v[2])
                                                            );
                    if (isset($foundval) && $foundval != '') {
                       
                        $row++;
                    }
                }
            }
            $this->rowsAffected = $row;
            return $foundval;
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }//End function here

    public function getRowsAffected()
    {
        return $this->rowsAffected;
    }
public function storeProductData($data=array())
    {
        $_product = Mage::getModel('catalog/product')->load($data['productId']);
        $returnVar = '';
        if($_product->sku)
        {
			$attrData =array("sa_review_count"=>$data['reviewCount'],"sa_average_rating"=>$data['reviewAverage']);
            $storeId = 0;
            Mage::getSingleton('catalog/resource_product_action')->updateAttributes(array($data['productId']), $attrData, $storeId);
            $returnVar = $_product;
        }
        return $returnVar;
    }
    //Check product is present or not in client database table
    public function getProductFromProductExternalId($productExternalId)
    {
        $model = Mage::getModel('catalog/product');
        $productCollection = $model->getCollection()->addAttributeToSelect('*')->load($productExternalId);
        foreach ($productCollection as $product) {
            return $product;
        }
        return null;
    }//End here

}


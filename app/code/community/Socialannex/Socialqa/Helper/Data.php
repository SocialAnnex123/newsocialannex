<?php
class Socialannex_Socialqa_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function __construct()
  {
  }

  public function loadSocialQA()
  {
    
      $saSiteId= Mage::getStoreConfig("satools/sageneral/text_field");
      $productID=Mage::registry('current_product')->getId();
      $_product=Mage::getModel('catalog/product')->load($productID);
      $skuID = urlencode($_product->getSku());
      $pimg=urlencode($_product->getImageUrl());
      $produrl=urlencode($_product->getProductUrl());
      $name= urlencode($_product['name']);
      $price =urlencode($_product['price']);
      /*** Bulid Sa call ****/ 
      $store = "satools/sa_socialqa/ApiURL";
      $saDomainUrl = Mage::getStoreConfig($store);
      $saDomainUrl = $this->appendParams($saDomainUrl, "siteID", $saSiteId);
      $saDomainUrl = $this->appendParams($saDomainUrl, "sa_p_id", $skuID);
      $saDomainUrl = $this->appendParams($saDomainUrl, "sa_p_img_url", $pimg);
      $saDomainUrl = $this->appendParams($saDomainUrl, "sa_p_url", $produrl);
      $saDomainUrl = $this->appendParams($saDomainUrl, "sa_p_price", $price);
      $saDomainUrl = $this->appendParams($saDomainUrl, "sa_p_name", $name);

      /*** SA Curl Call ****/ 
      $curl = new Varien_Http_Adapter_Curl();
      $curl->write(Zend_Http_Client::GET, $saDomainUrl, '1.0');
      $output = $curl->read();
      $outputArray = preg_split('/^\r?$/m', $output, 2); 
      $output = trim($outputArray[1]);
      /*** Prepare output and return ****/
      $qaData ="<div id='socialannex'>".$output."</div>
               <script src='//s23.socialannex.com/v4/js/s23-main-curl.js'>
			   </script>
                <script type='text/javascript'>
                    window.s23AsyncInit = function(){
                        var buildProductDetails= {
                            sa_p_url:'".$produrl."',
                            sa_p_img_url:'".$pimg."',
                            sa_p_name:'".$name."',
                            sa_p_id:'".$skuID."',
                            sa_p_price:'".$price."'
                        }
                        S23Obj.init({
                            siteID: ".$saSiteId.",
                            saGetProdDetails:buildProductDetails,
                        });
                        S23Obj.targetId = 'socialannex';
                    };
                </script>";
      return $qaData;
   
  }
  protected function appendParams($url,$key,$val)
  {
    $seperator = "?";  
    if(strpos($url, "?") !== false)
      $seperator = "&";
    $url .= $seperator.$key."=".urlencode($val);
    return $url;
  }
}

 <?php
/**
 * Sample Widget Helper
 */
class Socialannex_Referfriend_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function loadSaReferfriend()
	{
		 $saSiteId  = Mage::getStoreConfig('satools/sageneral/text_field');
  //$_saPageId = Mage::getStoreConfig('referfriend/general/HomePageid');
  $_saPageId =11;
  $jsUrl     = "//cdn.socialannex.com/partner/$saSiteId/universal.js";
  
  $script    = "<div id='sa_refer_friend'></div>" .PHP_EOL;
  $script    .= "<!-- Social Annex Universal JS -->" . PHP_EOL;
  $script .= "<script type='text/javascript'>" . PHP_EOL;
  $script .= "var sa_uni = sa_uni || [];" . PHP_EOL;
  $script .= "sa_uni.push(['sa_pg', $_saPageId]);" . PHP_EOL;
  $script .= "sa_uni.push(['sa_cs', 0]);" . PHP_EOL;
  $script .= "sa_uni.push(['sa_rs', 0]);" . PHP_EOL;
  if (Mage::getSingleton('customer/session')->isLoggedIn()) {
   $customer     = Mage::getSingleton('customer/session')->getCustomer();
   //$email = $customer->getEmail();
   //$customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
   $saCustomerID = $customer->getId(); //entity_id can be used as id
   $saFirstname  = $customer->getFirstname();
   $saLastname   = $customer->getLastname();
   $saEmail      = $customer->getEmail();
   $script .= "sa_uni.push(['first_name', '$saFirstname']);" . PHP_EOL;
   $script .= "sa_uni.push(['last_name', '$saLastname']);" . PHP_EOL;
   $script .= "sa_uni.push(['email', '$saEmail']);" . PHP_EOL;
   $script .= "sa_uni.push(['Account_Number', $saCustomerID]);" . PHP_EOL;
  }
  $script .= "(function () { function sa_async_load() { " . PHP_EOL;
  $script .= "var sa = document.createElement('script'); " . PHP_EOL;
  $script .= "sa.type = 'text/javascript'; " . PHP_EOL;
  $script .= "sa.async = true; " . PHP_EOL;
  $script .= "sa.src = '$jsUrl'; " . PHP_EOL;
  $script .= "var sax = document.getElementsByTagName('script')[0]; " . PHP_EOL;
  $script .= "sax.parentNode.insertBefore(sa, sax); " . PHP_EOL;
  $script .= "} " . PHP_EOL;
  $script .= "if (window.attachEvent) { " . PHP_EOL;
  $script .= "window.attachEvent('onload', sa_async_load); } else " . PHP_EOL;
  $script .= "{window.addEventListener('load',sa_async_load, false);" . PHP_EOL;
  $script .= "} " . PHP_EOL;
  $script .= "})();" . PHP_EOL;
  $script .= "</script> " . PHP_EOL;
  $script .= "<!-- End Social Annex Universal JS -->" . PHP_EOL;
  return $script;
	}
	public function loadSaRafReward()
	{
		 $saSiteId  = Mage::getStoreConfig('satools/sageneral/text_field');
  //$_saPageId = Mage::getStoreConfig('referfriend/general/HomePageid');
  $_saPageId =1;
  $jsUrl     = "//cdn.socialannex.com/partner/$saSiteId/universal.js";
  
  $script    = "<div id='socialannex1'></div>" .PHP_EOL;
  $script    .= "<!-- Social Annex Universal JS -->" . PHP_EOL;
  $script .= "<script type='text/javascript'>" . PHP_EOL;
  $script .= "var sa_uni = sa_uni || [];" . PHP_EOL;
  $script .= "sa_uni.push(['sa_pg', $_saPageId]);" . PHP_EOL;
  $script .= "sa_uni.push(['sa_cs', 1]);" . PHP_EOL;
  $script .= "sa_uni.push(['sa_rs', 0]);" . PHP_EOL;
  if (Mage::getSingleton('customer/session')->isLoggedIn()) {
   $customer     = Mage::getSingleton('customer/session')->getCustomer();
   $customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
   $saCustomerID = $customerData->getId(); //entity_id can be used as id
   $saFirstname  = $customerData->getFirstname();
   $saLastname   = $customerData->getLastname();
   $saEmail      = $customerData->getEmail();
   $script .= "sa_uni.push(['first_name', $saFirstname]);" . PHP_EOL;
   $script .= "sa_uni.push(['last_name', $saLastname]);" . PHP_EOL;
   $script .= "sa_uni.push(['email', $saEmail]);" . PHP_EOL;
   $script .= "sa_uni.push(['Account_Number', $saCustomerID]);" . PHP_EOL;
  }
  $script .= "(function () { function sa_async_load() { " . PHP_EOL;
  $script .= "var sa = document.createElement('script'); " . PHP_EOL;
  $script .= "sa.type = 'text/javascript'; " . PHP_EOL;
  $script .= "sa.async = true; " . PHP_EOL;
  $script .= "sa.src = '$jsUrl'; " . PHP_EOL;
  $script .= "var sax = document.getElementsByTagName('script')[0]; " . PHP_EOL;
  $script .= "sax.parentNode.insertBefore(sa, sax); " . PHP_EOL;
  $script .= "} " . PHP_EOL;
  $script .= "if (window.attachEvent) { " . PHP_EOL;
  $script .= "window.attachEvent('onload', sa_async_load); } else " . PHP_EOL;
  $script .= "{window.addEventListener('load',sa_async_load, false);" . PHP_EOL;
  $script .= "} " . PHP_EOL;
  $script .= "})();" . PHP_EOL;
  $script .= "</script> " . PHP_EOL;
  $script .= "<!-- End Social Annex Universal JS -->" . PHP_EOL;
  return $script;
	}
	public function loadSaRafStatus()
	{
		 $saSiteId  = Mage::getStoreConfig('satools/sageneral/text_field');
  //$_saPageId = Mage::getStoreConfig('referfriend/general/HomePageid');
  $_saPageId =1;
  $jsUrl     = "//cdn.socialannex.com/partner/$saSiteId/universal.js";
  
  $script    = "<div id='sa_refer_friend'></div>" .PHP_EOL;
  $script    .= "<!-- Social Annex Universal JS -->" . PHP_EOL;
  $script .= "<script type='text/javascript'>" . PHP_EOL;
  $script .= "var sa_uni = sa_uni || [];" . PHP_EOL;
  $script .= "sa_uni.push(['sa_pg', $_saPageId]);" . PHP_EOL;
  $script .= "sa_uni.push(['sa_cs', 0]);" . PHP_EOL;
  $script .= "sa_uni.push(['sa_rs', 1]);" . PHP_EOL;
  if (Mage::getSingleton('customer/session')->isLoggedIn()) {
   $customer     = Mage::getSingleton('customer/session')->getCustomer();
   $customerData = Mage::getModel('customer/customer')->load($customer->getId())->getData();
   $saCustomerID = $customerData->getId(); //entity_id can be used as id
   $saFirstname  = $customerData->getFirstname();
   $saLastname   = $customerData->getLastname();
   $saEmail      = $customerData->getEmail();
   $script .= "sa_uni.push(['first_name', $saFirstname]);" . PHP_EOL;
   $script .= "sa_uni.push(['last_name', $saLastname]);" . PHP_EOL;
   $script .= "sa_uni.push(['email', $saEmail]);" . PHP_EOL;
   $script .= "sa_uni.push(['Account_Number', $saCustomerID]);" . PHP_EOL;
  }
  $script .= "(function () { function sa_async_load() { " . PHP_EOL;
  $script .= "var sa = document.createElement('script'); " . PHP_EOL;
  $script .= "sa.type = 'text/javascript'; " . PHP_EOL;
  $script .= "sa.async = true; " . PHP_EOL;
  $script .= "sa.src = '$jsUrl'; " . PHP_EOL;
  $script .= "var sax = document.getElementsByTagName('script')[0]; " . PHP_EOL;
  $script .= "sax.parentNode.insertBefore(sa, sax); " . PHP_EOL;
  $script .= "} " . PHP_EOL;
  $script .= "if (window.attachEvent) { " . PHP_EOL;
  $script .= "window.attachEvent('onload', sa_async_load); } else " . PHP_EOL;
  $script .= "{window.addEventListener('load',sa_async_load, false);" . PHP_EOL;
  $script .= "} " . PHP_EOL;
  $script .= "})();" . PHP_EOL;
  $script .= "</script> " . PHP_EOL;
  $script .= "<!-- End Social Annex Universal JS -->" . PHP_EOL;
  return $script;
	}
} 
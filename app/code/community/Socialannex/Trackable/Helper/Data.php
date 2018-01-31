 <?php
/**
 * Social Annex Trackable Widget Helper
 */
class Socialannex_trackable_Helper_Data extends Mage_Core_Helper_Abstract
{
	 public function loadSaTrackable()
 {
  $socialdiv          = '';
  $safbshareflag      = Mage::getStoreConfig('satools/satrackable/safbshare');
  $satwfollowflag     = Mage::getStoreConfig('satools/satrackable/satwfollow');
  $sapintitflag       = Mage::getStoreConfig('satools/satrackable/sapintit');
  $saemailshareflag   = Mage::getStoreConfig('satools/satrackable/saemailshare'); 
  
   
  $safblikeflag       = Mage::getStoreConfig('satools/satrackable/safblike');
  $safifttwfollowflag = Mage::getStoreConfig('satools/satrackable/safifttwfollow');
  $sainstagram        = Mage::getStoreConfig('satools/satrackable/sainstagram');
  $sa_pin_follow = Mage::getStoreConfig('satools/satrackable/sa_pin_follow');
  
  
  
  $_saPageId          = Mage::getStoreConfig('satools/satrackable/satrackpg');
  
  $currentUrl         = Mage::helper('core/url')->getCurrentUrl();
  $url                = Mage::getSingleton('core/url')->parseUrl($currentUrl);
  $actualdomainurl    = $url->getPath(); 
  $getdomain          = explode('/', $actualdomainurl);
  $script="";
  $socialdiv="";
  
  $satracksiteid      = Mage::getStoreConfig('satools/sageneral/text_field');
  if ($satracksiteid != '') {
  if($_saPageId=="")
	  $_saPageId=44;
    $jsUrl = "//cdn.socialannex.com/partner/$satracksiteid/sa_trackable.js";
    $script .= '<!-- Social Annex Universal JS -->';
    $script .= '<script type="text/javascript">';
    $script .= "var sa_uni = sa_uni || [];" . PHP_EOL;
    $script .= "sa_uni.push(['sa_pg', $_saPageId]);" . PHP_EOL;
    $script .= '(function () {';
    $script .= 'function sa_async_load() {';
    $script .= 'var sa = document.createElement("script");';
    $script .= 'sa.type = "text/javascript";';
    $script .= 'sa.async = true;';
    $script .= "sa.src = '$jsUrl'; " . PHP_EOL;
    $script .= 'var sax = document.getElementsByTagName("script")[0];';
    $script .= 'sax.parentNode.insertBefore(sa, sax); ';
    $script .= '}';
    $script .= ' if (window.attachEvent) { ';
    $script .= ' window.attachEvent("onload", sa_async_load); ';
    $script .= ' } else { ';
    $script .= ' window.addEventListener("load", sa_async_load, false); ';
    $script .= ' } ';
    $script .= ' })();';
    $script .= ' </script>';
    $script .= '<!-- End of Social Annex Universal JS -->';
   
  }
     return $socialdiv . ' ' . $script;
 }

} 

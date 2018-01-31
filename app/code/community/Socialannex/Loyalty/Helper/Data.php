<?php

/**
 * Sample Widget Helper
 */
class Socialannex_Loyalty_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_saPageId = null;
    protected function loadSaUniversal($_saPageId, $userEmail)
    {
        $storeConfig = "satools/sageneral/text_field";
        $saSiteId = Mage::getStoreConfig($storeConfig); 
        $jsUrl = "//cdn.socialannex.com/partner/$saSiteId/universal.js";
        $script = "<div id='socialannex_dashboard'></div>" . PHP_EOL;
        $script .= "<!-- Social Annex Universal JS -->" . PHP_EOL;
        $script .= "<script type='text/javascript'>" . PHP_EOL;
        $script .= "var sa_uni = sa_uni || [];" . PHP_EOL;
        $script .= "sa_uni.push(['sa_pg', $_saPageId]);" . PHP_EOL;
        $script .= "var sa_emailid= '$userEmail';" . PHP_EOL;
        $script .= "(function () { function sa_async_load() { " . PHP_EOL;
        $script .= "var sa = document.createElement('script'); " . PHP_EOL;
        $script .= "sa.type = 'text/javascript'; " . PHP_EOL;
        $script .= "sa.async = true; " . PHP_EOL;
        $script .= "sa.src = '$jsUrl'; " . PHP_EOL;
        $script .= "var sax = document.getElementsByTagName";
        $script .= "('script')[0]; " . PHP_EOL;
        $script .= "sax.parentNode.insertBefore(sa, sax); " . PHP_EOL;
        $script .= "} " . PHP_EOL;
        $script .= "if (window.attachEvent) { " . PHP_EOL;
        $script .= "window.attachEvent('onload', sa_async_load); }" . PHP_EOL;
        $script .= " else { window.addEventListener ";
        $script .= "('load', sa_async_load, false);} " . PHP_EOL;
        $script .= "})();" . PHP_EOL;
        $script .= "</script> " . PHP_EOL;
        $script .= "<!-- End Social Annex Universal JS -->" . PHP_EOL;
        return $script;
    }

    //Loyalty Dashboard
    public function loadSaLoyaltyDashboard($saPageId)
    {
        
        $isLoogedIn = Mage::getSingleton('customer/session')->isLoggedIn();
        if ($isLoogedIn) {
            $session = Mage::getSingleton('customer/session')->getCustomer();
            $email = $session->getEmail();
        } else {
            $email = "";
        }
        if ($email != "") {
            $loadUniversal = $this->loadSaUniversal($saPageId, $email);
            return $loadUniversal;
        }
    }

    //To give points
    public function saGivePoints($saUserEmail, $actionId, $saFname, $saLname)
    {
        if (isset($saUserEmail) && isset($actionId)) {

            $saFname = isset($saFname) ? $saFname : "";
            $saLname = isset($saLname) ? $saLname : "";
            $store = "satools/Loyaltysettings/s15BaseUrl";
            $saURL = Mage::getStoreConfig($store);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?
'https://' : 'http://';
            $saURL = $protocol.$saURL;
            $sId = "satools/sageneral/text_field";
            $siteid = Mage::getStoreConfig($sId);
            $aToken = "satools/Loyaltysettings/AccessToken";
            $accessToken = Mage::getStoreConfig($aToken);
            //Create user
            $reqParam = "?access_token=" . $accessToken;
            $requestURL = $saURL."/user/".$siteid."/".$saUserEmail.$reqParam;
            $data = array('fname' => $saFname, 'lname' => $saLname);

            $responseUser = $this->makeRequest($requestURL, 'POST', $data);

            //Give points
            $requestUrl = $saURL."/userpoints/".$siteid."/".$saUserEmail;
            $requestUrl .= $reqParam;
            $postData = array('action_id' => $actionId, 'action_use' => '4');

            $response = $this->makeRequest($requestUrl, 'POST', $postData);
            return $response;
        }
    }  

    private function makeRequest($url, $method, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (null != $data) {
            $requestBody = http_build_query($data);
            $headers = "Content-Type:  application/x-www-form-urlencoded";
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($headers));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        return $content;
    }

}

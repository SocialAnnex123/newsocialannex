<?php
class Socialannex_Loyalty_IndexController extends 
    Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
    
	public function rewardlistAction()
    {
		$userEmail = $this->getRequest()->getParam('email');
		
        if ($userEmail) {
			 $store = "satools/Loyaltysettings/s15BaseUrl";
            $baseUrl = Mage::getStoreConfig($store);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?'https://' : 'http://';
            $baseUrl = $protocol.$baseUrl;
            $store = "satools/sageneral/text_field";
            $siteId = Mage::getStoreConfig($store);
            $aToken = "satools/Loyaltysettings/AccessToken";
            $accessToken = Mage::getStoreConfig($aToken);
			
			$url= $baseUrl."/reward/".$siteId."/".$userEmail; 
            $url .= "?access_token=".$accessToken;
            $response = $this->makeRequest($url, 'GET', NULL);

            echo $response;
        } 
        
    }
    
    public function userdetailAction()
    {
		$userEmail = $this->getRequest()->getParam('email');
		
        if ($userEmail) {
			 $store = "satools/Loyaltysettings/s15BaseUrl";
            $baseUrl = Mage::getStoreConfig($store);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?'https://' : 'http://';
            $baseUrl = $protocol.$baseUrl;
            $store = "satools/sageneral/text_field";
            $siteId = Mage::getStoreConfig($store);
            $aToken = "satools/Loyaltysettings/AccessToken";
            $accessToken = Mage::getStoreConfig($aToken);
			
			$url= $baseUrl."/user/".$siteId."/".$userEmail; 
            $url .= "?access_token=".$accessToken;
            $response = $this->makeRequest($url, 'GET', NULL);

            echo $response;
        } 
        
    }

	
    public function redeemAction()
    {
        $emailId = $this->getRequest()->getParam('email'); 
        $points = $this->getRequest()->getParam('points');
        $rewardId = $this->getRequest()->getParam('reward_id');
        $reason = $this->getRequest()->getParam('reason');        
		$store = "satools/Loyaltysettings/s15BaseUrl";
		$saBaseURL = Mage::getStoreConfig($store);
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?'https://' : 'http://';
		$saBaseURL = $protocol.$saBaseURL;
		$store = "satools/sageneral/text_field";
		$siteId = Mage::getStoreConfig($store);
		$aToken = "satools/Loyaltysettings/AccessToken";
		$accessToken = Mage::getStoreConfig($aToken);
			
        if ($emailId != '' && $points != '' && $rewardId != '' 
            && $siteId != '') {
            $url = $saBaseURL."/points/".$siteId."/".$emailId; 
            $url .= "?access_token=".$accessToken;
            $data = array('points'=>$points,'reward_id'=>$rewardId);
            $response = $this->makeRequest($url,'PUT',$data);
            echo $response;
        }elseif($emailId != '' && $rewardId != '' && $siteId != ''){
            $url = $saBaseURL."/points/".$siteId."/".$emailId; 
            $url .= "?access_token=".$accessToken;
            $data = array('reward_id'=>$rewardId);
            $response = $this->makeRequest($url,'PUT',$data);
            echo $response;
        }elseif($emailId != '' && $points != '' && $siteId != '' && $reason != ''){
            $url = $saBaseURL."/points/".$siteId."/".$emailId; 
            $url .= "?access_token=".$accessToken;
            $data = array('points'=>$points,'reason'=>$reason);
            $response = $this->makeRequest($url,'PUT',$data);
            echo $response;
            
        }
        
        
    }
	
	public function pointsAction()
    {
		$userEmail = $this->getRequest()->getParam('email'); 
        $saFname = $this->getRequest()->getParam('fname');
        $saLname = $this->getRequest()->getParam('lname');
		$actionId = $this->getRequest()->getParam('action_id');

        if ($userEmail) {
    	    $store = "satools/Loyaltysettings/s15BaseUrl";
            $saBaseURL = Mage::getStoreConfig($store);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?'https://' : 'http://';
            $saBaseURL = $protocol.$saBaseURL;
            $store = "satools/sageneral/text_field";
            $siteid = Mage::getStoreConfig($store);
            $aToken = "satools/Loyaltysettings/AccessToken";
            $accessToken = Mage::getStoreConfig($aToken);
            
            //Create user
            $reqParam = "?access_token=".$accessToken;
            $requestURL= $saBaseURL."/user/".$siteid."/".$userEmail.$reqParam;
            $data = array('fname'=>$saFname,'lname'=>$saLname);
            $responseUser = $this->makeRequest($requestURL, 'POST', $data);

            //Give points
            $requestUrl = $saBaseURL."/userpoints/".$siteid."/".$userEmail;
            $requestUrl .= $reqParam;
            $postData = array('action_id'=>$actionId,'action_use'=>'4');
            $response = $this->makeRequest($requestUrl, 'POST', $postData);
			
			echo $response;
        }
    }
	
	public function discountAction()
	{ 
        
		$sa_submit = $this->getRequest()->getParam('sa_submit');
		
		if($sa_submit=='removeToken'){
			Mage::getSingleton('core/session')->setData('saDiscountAmount', '0');
		} else {
			$session = Mage::getSingleton('customer/session')->getCustomer();
			$email = $session->getEmail();
			// create curl resource 
            $store = "satools/Loyaltysettings/s15BaseUrl";
            $baseUrl = Mage::getStoreConfig($store);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?'https://' : 'http://';
            $baseUrl = $protocol.$baseUrl;
            $store = "satools/sageneral/text_field";
            $siteId = Mage::getStoreConfig($store);
            $aToken = "satools/Loyaltysettings/AccessToken";
            $accessToken = Mage::getStoreConfig($aToken);
			
			$url= $baseUrl."/user/".$siteId."/".$email; 
            $url .= "?access_token=".$accessToken;
            $response = $this->makeRequest($url, 'GET', NULL);
            $userData = json_decode($response,true);
            if($userData['error_code']=='0'){
                $availablePoints = $userData['data']['available_points'];
            } else {
                $availablePoints = 0;
            }
			$quote = Mage::getModel('checkout/session')->getQuote();
			$quoteData= $quote->getData();
			$grandTotal=$quoteData['subtotal'];
			$discountAmount = Mage::getSingleton('core/session')->getData('saDiscountAmount');
			$discountPoints = $discountAmount * 100;
            $grandTotalPoints = $grandTotal * 100;
            if($discountAmount){
        
                if($discountPoints==$grandTotalPoints){
                    $redeemablePoints = $discountPoints;
                } else if($discountPoints<$grandTotalPoints && $grandTotalPoints<=$availablePoints) {
                    $redeemablePoints = $grandTotalPoints;
                } else if($discountPoints<$grandTotalPoints && $grandTotalPoints>$availablePoints){
                    $redeemablePoints = $discountPoints;
                } else if($discountPoints>$grandTotalPoints){
                    $redeemablePoints = $grandTotalPoints;
                }
            } else {
                if($grandTotalPoints<=$availablePoints){
                    $redeemablePoints = $grandTotalPoints;
                } else {
                    $redeemablePoints = $availablePoints;
                }
            }

			$convertedCurrency = $redeemablePoints/100;
			Mage::getSingleton('core/session')->setData('saDiscountAmount', $convertedCurrency);
		}
		
		//$this->_redirect('checkout/cart');
        //$this->_redirect('onestepcheckout/index');
		return;
	}
    
    public function ajaxDiscountAction()
	{
		$content = $this->getLayout()->createBlock('core/template')->setTemplate('socialannex/sadiscount.phtml')->toHtml();
        echo $content;
        exit;
	}

    private function makeRequest($url,$method,$data) 
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

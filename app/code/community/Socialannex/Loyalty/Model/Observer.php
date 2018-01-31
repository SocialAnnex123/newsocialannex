<?php
/** Action observer */
class Socialannex_Loyalty_Model_Observer
{
    public function customerRegisterSuccess(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        $userEmail = $customer->getEmail(); // user Email
        $saFname = $customer->getFirstname(); // First Name
        $saLname = $customer->getLastname(); // Last Name
	$customerId = $customer->getId();
        if ($userEmail) {
    	    $store = "satools/Loyaltysettings/s15BaseUrl";
            $saBaseURL = Mage::getStoreConfig($store);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?
'https://' : 'http://';
            $saBaseURL = $protocol.$saBaseURL;
            $store = "satools/sageneral/text_field";
            $siteid = Mage::getStoreConfig($store);
            $aToken = "satools/Loyaltysettings/AccessToken";
            $accessToken = Mage::getStoreConfig($aToken);
            $store = "satools/Loyaltyactionsettings/CreateAccount";
            $signupActionId = Mage::getStoreConfig($store);
            //Create user
            $reqParam = "?access_token=".$accessToken;
            $requestURL= $saBaseURL."/user/".$siteid."/".$userEmail.$reqParam;
            $data = array('fname'=>$saFname,'lname'=>$saLname,'mage_customer_id'=>$customerId);
            $responseUser = $this->makeRequest($requestURL, 'POST', $data);

            //Give points
            $requestUrl = $saBaseURL."/userpoints/".$siteid."/".$userEmail;
            $requestUrl .= $reqParam;
            $postData = array('action_id'=>$signupActionId,'action_use'=>'4');
            $referralStat = Mage::getModel('core/cookie')->get('sa_referral_stat');
            if($referralStat!='') {
              $postData['statid'] = $referralStat;
            }
            $response = $this->makeRequest($requestUrl, 'POST', $postData);
            $pointsData = json_decode($response,true);
            if($pointsData['error_code']=='0'){
                $points = $pointsData['point_detail']['earned_points'];
                $msg = 'You received '.$points.' Tokens for signing up!';
                Mage::getSingleton('core/session')->addSuccess($msg);
            }
        }
    }

    public function newsletterSubscriberSaveAfter(Varien_Event_Observer $ob)
    {
        $subscription = $ob->getEvent()->getSubscriber();
        $saUserEmail = $subscription->subscriber_email;
        if ($saUserEmail) {
            $saFname = ''; // First Name
            $saLname = ''; // Last Name
            $store = "satools/Loyaltysettings/s15BaseUrl";
            $saBaseURL = Mage::getStoreConfig($store);
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ?
'https://' : 'http://';
            $saBaseURL = $protocol.$saBaseURL;
            $sId = "satools/sageneral/text_field";
            $siteid = Mage::getStoreConfig($sId);
            $aToken = "satools/Loyaltysettings/AccessToken";
            $accessToken = Mage::getStoreConfig($aToken);
            $st = "satools/Loyaltyactionsettings/SignupNewsletter";
            $signupNewsletterActionId = Mage::getStoreConfig($st);
            //Create user
            $reqParam = "?access_token=".$accessToken;
            $requestURL= $saBaseURL."/user/".$siteid."/".$saUserEmail.$reqParam;
            $data = array('fname'=>$saFname,'lname'=>$saLname);
            $responseUser = $this->makeRequest($requestURL, 'POST', $data);

            //Give points
            $requestUrl = $saBaseURL."/userpoints/".$siteid."/".$saUserEmail;
            $requestUrl .= $reqParam;
            $postData = array('action_id'=>$signupNewsletterActionId,
                              'action_use'=>'4');
            $response = $this->makeRequest($requestUrl, 'POST', $postData);
            $pointsData = json_decode($response,true);
            if($pointsData['error_code']=='0'){
                $points = $pointsData['point_detail']['earned_points'];
                $msg = 'You received '.$points.' Tokens for signing newsletter!';
                Mage::getSingleton('core/session')->addSuccess($msg);
            }
        }
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
    
    public function setDiscount($observer)
	{
		$quote=$observer->getEvent()->getQuote();
		$quoteid=$quote->getId();
		$quoteSession = Mage::getModel('checkout/session')->getQuote();
		$quoteData= $quoteSession->getData();
		$grandTotal=$quoteData['grand_total'];
		if($grandTotal<0) {
			Mage::getSingleton('core/session')->setData('saDiscountAmount', '0');
		}
		$discountAmount = Mage::getSingleton('core/session')->getData('saDiscountAmount');
		//$discountAmount=10;
		if($quoteid) {



		if($discountAmount>0) {
		$total=$quote->getBaseSubtotal();
		$quote->setSubtotal(0);
		$quote->setBaseSubtotal(0);

		$quote->setSubtotalWithDiscount(0);
		$quote->setBaseSubtotalWithDiscount(0);

		$quote->setGrandTotal(0);
		$quote->setBaseGrandTotal(0);


		$canAddItems = $quote->isVirtual()? ('billing') : ('shipping'); 
		foreach ($quote->getAllAddresses() as $address) {

		$address->setSubtotal(0);
			$address->setBaseSubtotal(0);

			$address->setGrandTotal(0);
			$address->setBaseGrandTotal(0);

			$address->collectTotals();

			$quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
			$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

			$quote->setSubtotalWithDiscount(
				(float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
			);
			$quote->setBaseSubtotalWithDiscount(
				(float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
			);

			$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
			$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

		$quote ->save(); 

		$quote->setGrandTotal($quote->getBaseSubtotal()-$discountAmount)
		->setBaseGrandTotal($quote->getBaseSubtotal()-$discountAmount)
		->setSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
		->setBaseSubtotalWithDiscount($quote->getBaseSubtotal()-$discountAmount)
		->save(); 


		if($address->getAddressType()==$canAddItems) {
		//echo $address->setDiscountAmount; exit;
		$address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount()-$discountAmount);
		$address->setGrandTotal((float) $address->getGrandTotal()-$discountAmount);
		$address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount()-$discountAmount);
		$address->setBaseGrandTotal((float) $address->getBaseGrandTotal()-$discountAmount);
		if($address->getDiscountDescription()){
		$address->setDiscountAmount(-($address->getDiscountAmount()-$discountAmount));
		$address->setDiscountDescription($address->getDiscountDescription().', LEVEL UP reward');
		$address->setBaseDiscountAmount(-($address->getBaseDiscountAmount()-$discountAmount));
		}else {
		$address->setDiscountAmount(-($discountAmount));
		$address->setDiscountDescription('LEVEL UP reward');
		$address->setBaseDiscountAmount(-($discountAmount));
		}
		$address->save();
		}//end: if
		} //end: foreach
		//echo $quote->getGrandTotal();

		foreach($quote->getAllItems() as $item){
				 //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
				 $rat=$item->getPriceInclTax()/$total;
				 $ratdisc=$discountAmount*$rat;
				 $item->setDiscountAmount(($item->getDiscountAmount()+$ratdisc) * $item->getQty());
				 $item->setBaseDiscountAmount(($item->getBaseDiscountAmount()+$ratdisc) * $item->getQty())->save();
				
			   }
			
				
			}
			
		}
	}
}

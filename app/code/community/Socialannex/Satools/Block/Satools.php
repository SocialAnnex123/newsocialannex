<?php
class Socialannex_Satools_Block_Satools extends Mage_Core_Block_Abstract implements Mage_Widget_Block_Interface
{
  public function loadsauniversal($data = array()) {
        $jsUrl = '//cdn.socialannex.com/partner/' . $data['siteId'] . '/universal.js';
        $script = '<!-- Social Annex Universal JS -->' . PHP_EOL;
        $script.='<script type="text/javascript">' . PHP_EOL;
        $script.='var sa_uni = sa_uni || [];' . PHP_EOL;
        $script.='sa_uni.push(["sa_pg", "' . $data['pageId'] . '"]);' . PHP_EOL;
        $script.='(function () { function sa_async_load() { ' . PHP_EOL;
        $script.='var sa = document.createElement("script"); ' . PHP_EOL;
        $script.='sa.type = "text/javascript"; ' . PHP_EOL;
        $script.='sa.async = true; ' . PHP_EOL;
        $script.='sa.src = "' . $jsUrl . '"; ' . PHP_EOL;
        $script.='var sax = document.getElementsByTagName("script")[0];';
        $script.='sax.parentNode.insertBefore(sa, sax); ' . PHP_EOL;
        $script.='} ' . PHP_EOL;
        $script.='if (window.attachEvent) { ' . PHP_EOL;
        $script.='window.attachEvent("onload", sa_async_load); } else {';
        $script.='window.addEventListener("load", sa_async_load, false);';
        $script.='} ' . PHP_EOL;
        $script.='})();' . PHP_EOL;
        $script.='</script> ' . PHP_EOL;
        return $script;
    }

    //RNR function
    public function loadRNRcss($data = array()) {
        if (empty($data))
            return false;
        $passparam = 'site_id=' . $data['siteId'] . '&template_id=' . $data['templateId'];
        $cssurl = '//s28.socialannex.com/s28-curl-css.php?' . $passparam;
        $rnrcss = '<link rel="stylesheet" type="text/css" media="screen" href="' . $cssurl . '">';
        return $rnrcss;
    }

    public function saRnrParameter($data = array()) {
        if (empty($data))
            return false;
        $_product=Mage::getModel('catalog/product')->load($data['productId']);
        //$productMediaConfig = Mage::getModel('catalog/product_media_config');
        //$productimage = $productMediaConfig->getMediaUrl($_product->getImage());
        $productimage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $_product->getImage();
        //$productimage = '';
        $productname = $_product->getMetaTitle();
        $productdesc = $_product->getMetaDescription();
        $productprice = $_product->getPrice();
        $producturl = $_product->getProductUrl();
        $firstname = '';
        $email = '';
        $lastname = '';
        $city = '';
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $fullname = $customer->getName();
            $firstname = $customer->getFirstname();
            $lastname = $customer->getLastname();
            $email = $customer->getEmail();
            $customerID = $customer->getId(); //entity_id can be used as id
            $address = Mage::getModel('customer/address')->load($customerID);
            $city = $address->getCity();
        }
        $script = '<script type="text/javascript">' . PHP_EOL;
        $script .= 'var sa_s28_review_form    =    "0";' . PHP_EOL;
        $script .= 'var sa_s28_email        =    "' . $email . '";' . PHP_EOL;
        $script .= 'var sa_s28_first_name    =    "' . $firstname . '";' . PHP_EOL;
        $script .= 'var sa_s28_last_name    =    "' . $lastname . '";' . PHP_EOL;
        $script .= 'var sa_s28_user_location    =    "' . $city . '";' . PHP_EOL;
        $script .= 'var sa_s28_location         =       window.location.href;' . PHP_EOL;
        $script .= 'var sa_s28_product_url      =       "' . $producturl . '";' . PHP_EOL;
        $script .= 'var sa_s28_template_id      =       "' . $data['templateId'] . '";' . PHP_EOL;
        $script .= 'var sa_s28_product_id       =       "' . $data['productId'] . '";' . PHP_EOL;
        $script .= 'var sa_s28_product_name     =       "' . $productname . '";' . PHP_EOL;
        $script .= 'var sa_s28_product_desc     =       "' . $productdesc . '";' . PHP_EOL;
        $script .= 'var sa_s28_product_image_url =      "' . $productimage . '";' . PHP_EOL;
        $script .= 'var sa_s28_product_price =          "' . $productprice . '";' . PHP_EOL;
        $script .='</script>';

        return $script;
    }

    public function saLoadCssFromCDN($data = array()) {
        if (empty($data))
            return false;
        $script = '<script type="text/javascript">';
        $script .= 'function sa_s28_loadCSSBackup(){';
        $script .= 'var css = document.createElement("link");';
        $script .= 'css.setAttribute("rel", "stylesheet");';
        $script .= 'css.setAttribute("type", "text/css");';
        $passparam = "site_id=" . $data['siteId'] . "&template_id=" . $data['templateId'];
        $script .= 'css.setAttribute("href", "//s28.socialannex.com/s28-curl-css.php?' . $passparam . '");';
        $script .= 'document.getElementsByTagName("head")[0].appendChild(css);';
        $script .= '}';
        $script .= '</script>';
        return $script;
    }

    public function getAvgRating($data = array()) {
        if (empty($data))
            return false;
        $_product = Mage::getModel('catalog/product')->load($data['productId']);
        $productName = $_product['name'];
        $reviewCount = 0;
        $avgRate = 0;
        $ratingImage = '//cdn.socialannex.com/partner/' . $data['siteId'] . '/images/' . $data['templateId'] . '/average/norating.png';
        if ($_product['sa_average_rating'] != '' && $_product['sa_average_rating'] != 0) {
            $avgRate = round($_product['sa_average_rating'],1);
            $imageData = $this->getRatingImage($avgRate);
            $ratingImage = '//cdn.socialannex.com/partner/' . $data['siteId'] . '/images/' . $data['templateId'] . '/average/' . $imageData . '.png';
            $reviewCount = $_product['sa_review_count'];
        }
        if (!isset($reviewCount) && $reviewCount == '') {
            $reviewCount = 0;
        }
        $reviewCountLink = "(" . $reviewCount . ")";
        $readReviewLink = '';
        if (!isset($data['categoryPage']) && $data['categoryPage'] != 1) {
            $displayReviewCount = '<span class="sa_s28_reviewCount">'.$reviewCount.'</span><span class="sa_s28_reviewText"> Review</span>';
            if($reviewCount > 1){
                $displayReviewCount = '<span class="sa_s28_reviewCount">'.$reviewCount.'</span><span class="sa_s28_reviewText"> Reviews</span>';
            }
            $readReviewLink = '<div class="sa_s28_readreview_toplink"><a href="javascript:void(0);" id="saS28ReadReviewLink">'.$displayReviewCount.'</a></div>';
            $readReviewLink .= '<div id="sa_s28_write_review_link"></div>';
        }
        $aggregaterating = "http://schema.org/AggregateRating";
        $ratingval = '<div class="sa_s28_display_avg_rating" itemscope itemtype="http://schema.org/Product">';
        $ratingval.='<span itemprop="name" style="display:none;">' . $productName . '</span>';
        $ratingval.='<div class="sa_s28_rating"><div class="sa_s28_Like_rating"><div class="ratingblock" >';
        $ratingval.='<div id="unit_long_'.$data['productId'].'" ><div id="unit_ul_'.$data['productId'].'" class="unit-rating">';
        $ratingval.='<div class="current-rating"><img src="' . $ratingImage . '"></div></div>';
        $ratingval.='</div></div></div></div><div class="sa_s28_points_rating"><span class="sa_s28_common_ratingCount">' . $reviewCountLink . '</span></div>';
        $ratingval.='<div itemprop="aggregateRating" itemscope itemtype="' . $aggregaterating . '" style="display:none;">';
        $ratingval.='<span itemprop="ratingValue">' . $avgRate . '</span><span itemprop="reviewCount">5</span>';
        $ratingval.='<meta itemprop="bestRating" content="5"><meta itemprop="worstRating" content="0"></div></div>';
        $ratingval.=$readReviewLink;
        return $ratingval;
    }

    public function getRatingImage($avgrating) {

        if ($avgrating >= 5) {
            $ratingImage = '5star';
        } elseif ($avgrating >= 4.5 && $avgrating < 5) {
            $ratingImage = '4_5star';
        } elseif ($avgrating >= 4 && $avgrating < 4.5) {
            $ratingImage = '4star';
        } elseif ($avgrating >= 3.5 && $avgrating < 4) {
            $ratingImage = '3_5star';
        } elseif ($avgrating >= 3 && $avgrating < 3.5) {
            $ratingImage = '3star';
        } elseif ($avgrating >= 2.5 && $avgrating < 3) {
            $ratingImage = '2_5star';
        } elseif ($avgrating >= 2 && $avgrating < 2.5) {
            $ratingImage = '2star';
        } elseif ($avgrating >= 1.5 && $avgrating < 2) {
            $ratingImage = '1_5star';
        } elseif ($avgrating >= 1 && $avgrating < 1.5) {
            $ratingImage = '1star';
        } elseif ($avgrating >= 0.5 && $avgrating < 1) {
            $ratingImage = '0_5star';
        } elseif ($avgrating >= 0 && $avgrating < 0.5) {
            $ratingImage = 'norating';
        }
        return $ratingImage;
    }

}

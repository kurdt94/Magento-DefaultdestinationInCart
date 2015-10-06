<?php
class K94_Defaultdestination_Model_Observer {
	
	// Handle Collect
	public function handleCollect(Varien_Event_Observer $observer) {
		if (!Mage::getStoreConfig('shipping/origin/applydefaultstoemptyquote')){
			return $this;}
			
		$quote = $observer->getEvent()->getQuote();
		
		$shippingAddress = $quote->getShippingAddress();
		$billingAddress = $quote->getBillingAddress();
		$saveQuote = false;
		
		if (!$shippingAddress->getCountryId()) {
			$country = Mage::getStoreConfig('shipping/origin/country_id');
			$state = Mage::getStoreConfig('shipping/origin/region_id');
			$postcode = Mage::getStoreConfig('shipping/origin/postcode');
			$method = Mage::getStoreConfig('shipping/origin/shippingmethod');
			
			        if (!($country = $this->_getCoreSession()->getAutoShippingCountry())) {
            $country = Mage::getStoreConfig('autoshipping/settings/country_id');
            $this->_getCoreSession()->setAutoShippingCountry($country);
        }
			
			$shippingAddress
				->setCountryId($country)
				->setRegionId($state)
				->setPostcode($postcode)
				->setShippingMethod($method)
				->setCollectShippingRates(true);
			
			$shippingAddress->collectShippingRates();
			$shippingAddress->save();
			
			$saveQuote = true;
		}
		if (Mage::getStoreConfig('shipping/origin/applydefaultstobillingaddress')) {
				$country = Mage::getStoreConfig('shipping/origin/country_id');
				$state = Mage::getStoreConfig('shipping/origin/region_id');
				$postcode = Mage::getStoreConfig('shipping/origin/postcode');
							
				$billingAddress
					->setCountryId($country)
					->setRegionId($state)
					->setPostcode($postcode);
				
				$saveQuote = true;               
		}
		
		if ($saveQuote){
				$quote->save();
				$this->_getCheckoutSession()->resetCheckout();
				return $this;
		}
	}
	
	/**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    /**
     * @return Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }
	
}

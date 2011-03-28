<?php

class Netzarbeiter_NicerImageNames_Model_Observer
{
	/**
	 * Override the catalog product image model for magento versions < 1.4.1
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function controllerFrontInitBefore($observer)
	{
		if (version_compare(Mage::getVersion(), '1.4.1', '<') && strpos(Mage::getVersion(), '-devel') === false)
		{
			Mage::getConfig()->setNode('global/models/catalog/rewrite/product_image', 'Netzarbeiter_NicerImageNames_Model_Pre141_Image');
		}
	}
}
<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_NicerImageNames
 * @copyright  Copyright (c) 2008 Vinai Kopp http://netzarbeiter.com/
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog image helper, overwrite
 *
 * @author      Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_NicerImageNames_Helper_Image extends Mage_Catalog_Helper_Image
{
	/**
	 * Add the nice cache name to the image model
	 */
	/**
	 * Add the nice cache name to the image model
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param string $attributeName
	 * @param string  $imageFile
	 * @return Netzarbeiter_NicerImageNames_Helper_Image
	 */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null)
    {
    	parent::init($product, $attributeName, $imageFile);
    	if (! Mage::getStoreConfig("catalog/nicerimagenames/disable_ext")) {
    		$this->_getModel()->setNiceCacheName($this->_getNiceCacheName($attributeName));
    	}
        return $this;
    }
	
    /**
     * Build the nice image cache name from the config setting
     *
     * @param string
     * @return string
     */
	protected function _getNiceCacheName($attributeName)
	{	
		$map = Mage::getStoreConfig("catalog/nicerimagenames/map");
		if (preg_match_all('/(%([a-z0-9]+))/i', $map, $m, PREG_PATTERN_ORDER)) {
			for ($i = 0; $i < count($m[1]); $i++) {
				$map = str_replace($m[1][$i], $this->_getProductAttributeValue($m[2][$i]), $map);
			}
		}
		if (Mage::getStoreConfig("catalog/nicerimagenames/unique")) {
			$map .= '-' . $this->_imageAttributeNameToNum($attributeName);
			$map .= $this->_getMediaGalleryId();
		}
		
		return $map;
	}
	
	/**
	 * Return the value of an attribute
	 *
	 * @param string $attribute_code
	 * @para, boolean $_sentry
	 * @return string
	 */
	protected function _getProductAttributeValue($attributeName, $_sentry = false)
	{
		/*
		if (! $product->getData('media_gallery'))
		{
			if ($backend = $this->_getMediaBackend($product))
			{
				$backend->afterLoad($product);
			}
		}
		 */
		/*
		 * Transform camelCase to underscore (e.g. productName => product_name)
		 */
		$attribute_code = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $attributeName));
		$attribute = $this->getProduct()->getResource()->getAttribute($attribute_code);
		if ($attribute->usesSource())
		{
			$value = $this->getProduct()->getAttributeText($attribute_code);
		}
		else
		{
			$value = $this->getProduct()->getDataUsingMethod($attribute_code);
		}
		if (! isset($value) && ! $_sentry) {
			// last try, load attribute
			$this->_loadAttributesOnProduct($this->getProduct());
			return $this->_getProductAttributeValue($attribute_code, $_sentry = true);
		}
		// haha
		if (! is_scalar($value)) return $attribute_code;
		
		return str_replace(' ', '-', preg_replace('@(/|\.\.)@', '_', strval($value)));
	}

	protected function _loadAttributesOnProduct(Mage_Catalog_Model_Product $product)
	{
		$data = $product->getData();
		$product->load($product->getId())->addData($data);
		return $this;
	}
	
	/**
	 * Return the numeric position if the image in the media gallery array
	 *
	 * @return integer
	 */
	protected function _getMediaGalleryId()
	{
		$product = $this->getProduct();
		
		if (! ($file = $this->getImageFile())) {
			$file = $product->getData($this->_getModel()->getDestinationSubdir());
		}
		if (! $file) return 0;
	
		if (! ($gallery = $product->getMediaGalleryImages())) {
			$this->_loadAttributesOnProduct($product);
			$gallery = $product->getMediaGalleryImages();
		}
		foreach ($gallery as $image) {
			if ($image->getFile() == $file) return $image->getPosition(); //return $image->getId();
		}
		// image not found in media gallery...
		return 0;
	}
	
	/**
	 * Return a different number depending on the attributeCode passt to init()
	 *
	 * @param string $attributeCode
	 * @return integer
	 */
	protected function _imageAttributeNameToNum($attributeCode)
	{
		switch ($attributeCode)
		{
			case 'thumbnail': return 1;
			case 'small_image': return 2;
			case 'image': return 3;
			default: return 0;
		}
	}
	
	/**
	 * Debug log method
	 *
	 * @param mixed $var
	 */
	public function log($var)
	{
		$var = print_r($var, 1);
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $var = str_replace("\n", "\r\n", $var);
		Mage::log($var);
	}
}
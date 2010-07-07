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
 * Catalog product link model overwrite, make the cache image names nicer
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_NicerImageNames
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_NicerImageNames_Model_Pre141_Image extends Mage_Catalog_Model_Product_Image
{
	/**
	 * This will be used as the file name in the cache
	 *
	 * @var string
	 */
	protected $_niceCacheName = '';
	
	/**
	 * Set the cache file base name
	 *
	 * @param string $name
	 * @see Netzarbeiter_NicerImageNames_Helper_Image::init()
	 */
	public function setNiceCacheName($name)
	{
		$this->_niceCacheName = $name;
	}
	
	/**
	 * Return the cache file base name
	 *
	 * @return string
	 * @see Netzarbeiter_NicerImageNames_Helper_Image::init()
	 */
	public function getNiceCacheName()
	{
		return $this->_niceCacheName;
	}
	
    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setBaseFile($file)
    {
        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }
        $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();

        if ('/no_selection' == $file) {
            $file = null;
        }
        if ($file) {
            if ((!file_exists($baseDir . $file)) || !$this->_checkMemory($baseDir . $file)) {
                $file = null;
            }
        }
        if (!$file) {
            // check if placeholder defined in config
            $isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
            $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;
            if ($isConfigPlaceholder && file_exists($baseDir . $configPlaceholder)) {
                $file = $configPlaceholder;
            }
            else {
                // replace file with skin or default skin placeholder
                $skinBaseDir     = Mage::getDesign()->getSkinBaseDir();
                $skinPlaceholder = "/images/catalog/product/placeholder/{$this->getDestinationSubdir()}.jpg";
                $file = $skinPlaceholder;
                if (file_exists($skinBaseDir . $file)) {
                    $baseDir = $skinBaseDir;
                }
                else {
                    $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
                }
            }
        }

        $baseFile = $baseDir . $file;

        if ((!$file) || (!file_exists($baseFile))) {
            throw new Exception(Mage::helper('catalog')->__('Image file not found'));
        }
        $this->_baseFile = $baseFile;

        // build new filename (most important params)
        $path = array(
            Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath(),
            'cache',
            Mage::app()->getStore()->getId(),
            $path[] = $this->getDestinationSubdir()
        );
        if((!empty($this->_width)) || (!empty($this->_height)))
            $path[] = "{$this->_width}x{$this->_height}";
        // add misc params as a hash
        $path[] = md5(
            implode('_', array(
                ($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
                ($this->_keepFrame        ? '' : 'no')  . 'frame',
                ($this->_keepTransparency ? '' : 'no')  . 'transparency',
                ($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',
                $this->_rgbToString($this->_backgroundColor),
                'angle' . $this->_angle,
            ))
        );
        
        $path = implode('/', $path);
        if (! Mage::getStoreConfig("catalog/nicerimagenames/disable_ext")) {
        	$file = $this->_getNiceFileName($path, $file);
        	if (Mage::getStoreConfig("catalog/nicerimagenames/lowercase")) {
        		$file = strtolower($file);
        	}
        }
    	
        // append prepared filename
        $this->_newFile = $path . $file; // the $file contains heading slash

        return $this;
    }
    
    /**
     * Return the filename with the correct number
     *
     * @param string $path
     * @param string $file
     * @return string
     */
    protected function _getNiceFileName($path, $file)
    {
        // add the image name without the file type extension to the image cache path
        $pos = strrpos($file, '.');
    	$pathExt = substr($file, 1, $pos -1);
        $extension = substr($file, $pos+1);
    	
    	$file = $this->getNiceCacheName();
    	return sprintf('/%s/%s.%s', $pathExt, $file, $extension);
    	
    	$fileGlob = sprintf("%s/%s/%s-*.%s", $path, $pathExt, $file, $extension);
    	if (($res = glob($fileGlob))) {
    		// found a match, return extended basename with leading slash
    		return sprintf('/%s/%s', $pathExt, basename($res[0]));
    	}
    	// no match found, find an unused number
    	$fileGlob = sprintf("%s/*/%s-*.%s", $path, $file, $extension);
    	if (! ($res = glob($fileGlob))) {
    		// no image for this product has been cached so far
    		return sprintf("/%s/%s-1.%s", $pathExt, $file, $extension);
    	}
    	$num = 0;
    	$regex = sprintf('#-(\d+).%s$#', $extension);
    	foreach ($res as $match) {
    		if (preg_match($regex, $match, $m)) {
    			if ($m[1] > $num) $num = $m[1];
    		}
    	}
    	$file = sprintf("/%s/%s-%d.%s", $pathExt, $file, $num+1, $extension);
    	return $file;
    }
    
    /**
     * Convert array of 3 items (decimal r, g, b) to string of their hex values
     * Need to include this method because it's private in Mage_Catalog_Model_Product_Image
     * for some reason that escapes me.
     *
     * @param array $rgbArray
     * @return string
     */
    private function _rgbToString($rgbArray)
    {
        $result = array();
        foreach ($rgbArray as $value) {
            if (null === $value) {
                $result[] = 'null';
            }
            else {
                $result[] = sprintf('%02s', dechex($value));
            }
        }
        return implode($result);
    }
}





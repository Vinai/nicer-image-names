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
 * @copyright  Copyright (c) 2014 Vinai Kopp http://netzarbeiter.com/
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product link model overwrite, make the cache image names nicer
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_NicerImageNames
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_NicerImageNames_Model_Image extends Mage_Catalog_Model_Product_Image
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
     * @throws Mage_Core_Exception
     */
    public function setBaseFile($file)
    {
        parent::setBaseFile($file);

        if (!Mage::getStoreConfig("catalog/nicerimagenames/disable_ext") && !$this->_isBaseFilePlaceholder) {
            // The $_newFile property is set during parent::setBaseFile()
            list($path, $file) = $this->_getFilePathAndName($this->_newFile);
            $file = $this->_getNiceFileName($file);
            if (Mage::getStoreConfig("catalog/nicerimagenames/lowercase")) {
                $file = strtolower($file);
            }
            list($pathExt, $extension) = $this->_getFileNameParts($file);

            // Check that generated filename is not longer than 255
            $maxfilelen = 255;
            if (strlen(basename($file) . $extension) > $maxfilelen) {
                $file = substr($file, 0, ($maxfilelen - strlen($extension) + strlen(dirname($file))));
                $file = $file . '.' . $extension;
            }
            $this->_newFile = $path . $file; // the $file contains heading slash
            
            if (defined('PHP_MAXPATHLEN')) {
                $maxlen = PHP_MAXPATHLEN;
            } else {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $maxlen = 255; // NTFS sucks
                } else {
                    $maxlen = 1024; // Safe assumption, probably more
                }
            }
            if (strlen($this->_newFile) > $maxlen) {
                // hack off as much as necessary to make it fit. Urg.
                // See https://github.com/Vinai/nicer-image-names/issues/14 for more info
                // This is no real solution, as there is no check if the resulting image
                // still is in the right directory.
                
                // The proper thing to do is to not use Windows for hosting,
                // or, not use attributes with looog values in the name template.
                
                $pathExt = substr($path . rtrim($pathExt, '/\\') . $pathExt, 0, ($maxlen - strlen($extension) - 2 ));
                $this->_newFile = rtrim($pathExt, '/\\') . '.' . $extension;
            }
        }
        return $this;
    }

    /**
     * Return the file path and file name as an array
     * 
     * @param $file
     * @return array
     */
    protected function _getFilePathAndName($file)
    {
        $path = dirname($file);
        $file = '/' . basename($file);
        
        return array($path, $file);
    }

    /**
     * Return the filename with the correct number
     *
     * @param string $file
     * @return string
     */
    protected function _getNiceFileName($file)
    {
        // add the image name without the file type extension to the image cache path
        list($pathExt, $extension) = $this->_getFileNameParts($file);
        
        if (Mage::getStoreConfig("catalog/nicerimagenames/hash_image_folder_name")) {
            $pathExt = md5($pathExt);
        }

        $file = $this->getNiceCacheName();
        return sprintf('/%s/%s.%s', $pathExt, $file, $extension);
    }

    /**
     * Return the extended file path and extension
     *
     * @param string $file
     * @return array
     */
    protected function _getFileNameParts($file)
    {
        $pos = strrpos($file, '.');
        $pathExt = substr($file, 1, $pos - 1);
        $extension = substr($file, $pos + 1);
        return array($pathExt, $extension);
    }
    
    /**
     * Overrides the parent method. It checks the base file change time, 
     * and if cached file is older - we concider an image is not cached
     *
     * @return bool
     */
    public function isCached()
    {
        if (Mage::getStoreConfig("catalog/nicerimagenames/disable_ext")) {
        	// use parent method if nicerimagenames disabled
            return parent::isCached();
        }

        if (!$this->_fileExists($this->_newFile)) {
            return false;
        }

        if (!file_exists($this->_baseFile)) {
            return true;
        }

        if (filemtime($this->_newFile) < filemtime($this->_baseFile)) {
            // checks the base file change time, and if cached file is older - we concider an image is not cached
            return false;
        }

        return true;
    }
}





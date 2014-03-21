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
     * @throws Mage_Core_Exception
     */
    public function setBaseFile($file)
    {
        parent::setBaseFile($file);

        if (!Mage::getStoreConfig("catalog/nicerimagenames/disable_ext")) {
            // The $_newFile property is set during parent::setBaseFile()
            list($path, $file) = $this->_getFilePathAndName($this->_newFile);

            $file = $this->_getNiceFileName($file);
            if (Mage::getStoreConfig("catalog/nicerimagenames/lowercase")) {
                $file = strtolower($file);
            }
            $this->_newFile = $path . $file; // the $file contains heading slash
        }
        return $this;
    }

    /**
     * Return the filename with the correct number
     *
     * @param string $path
     * @param string $file
     * @return string
     */
    protected function _getNiceFileName($file)
    {
        // add the image name without the file type extension to the image cache path
        $pos = strrpos($file, '.');
        $pathExt = substr($file, 1, $pos - 1);
        $extension = substr($file, $pos + 1);

        $file = $this->getNiceCacheName();
        return sprintf('/%s/%s.%s', $pathExt, $file, $extension);
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
            } else {
                $result[] = sprintf('%02s', dechex($value));
            }
        }
        return implode($result);
    }
}





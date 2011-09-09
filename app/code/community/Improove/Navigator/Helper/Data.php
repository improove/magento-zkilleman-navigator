<?php
/**
 * Improove
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 *
 * DISCLAIMER
 *
 * This piece of software is made available strictly on an "as is" basis
 * and comes with absolutely NO warranty
 *
 * @category    Improove
 * @package     Improove_Navigator
 * @copyright   Copyright (c) 2011 Improove (http://www.improove.se)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Main Helper class for the Improove_Navigator module
 *
 * @author      Henrik Hedelund <henrik@improove.se>
 */
class Improove_Navigator_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Storage var for lazy-loaded cache key data
     * @var array
     */
    public static $_cacheKeyInfo = null;

    /**
     * Renders Node Html
     *
     * @param Improove_Navigator_Model_Node $node
     * @return string Node Html
     */
    public function getNodeHtml(Improove_Navigator_Model_Node &$node)
    {
        return $this->getLayout()
                ->createBlock('navigator/node')
                ->setNode($node)
                ->toHtml();
    }

    /**
     * Asks each Node content type and Node visibility type which parameters
     * to take into account when building block cache keys
     *
     * @return array An array of data to use for building cache keys
     */
    public static function getCacheKeyInfo()
    {
        if (self::$_cacheKeyInfo === null) {
            self::$_cacheKeyInfo = array(
                'path' => Mage::app()->getRequest()->getPathInfo()
            );

            $contentTypes = Mage::getModel('navigator/node_content_type')->getTypes();
            foreach ($contentTypes as $type) {
                $model = Mage::getModel($type['model']);
                if ($model instanceof Improove_Navigator_Model_Node_Content_Type) {
                    $ckInfo = $model->getCacheKeyInfo();
                    if (is_string($ckInfo) && strlen($ckInfo) > 0) {
                        self::$_cacheKeyInfo[$type['model']] = $ckInfo;
                    }
                }
            }
            $visibilityTypes = Mage::getModel('navigator/node_visibility')->getTypes();
            foreach ($visibilityTypes as $type) {
                $model = Mage::getModel($type['model']);
                if ($model instanceof Improove_Navigator_Model_Node_Visibility_Abstract) {
                    $ckInfo = $model->getCacheKeyInfo();
                    if (is_string($ckInfo) && strlen($ckInfo) > 0) {
                        self::$_cacheKeyInfo[$type['model']] = $ckInfo;
                    }
                }
            }
        }
        return self::$_cacheKeyInfo;
    }
}
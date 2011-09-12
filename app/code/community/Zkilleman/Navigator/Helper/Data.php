<?php
/**
 * Zkilleman_Navigator
 *
 * Copyright (C) 2011 Henrik Hedelund (henke.hedelund@gmail.com)
 *
 * This file is part of Zkilleman_Navigator.
 *
 * Zkilleman_Navigator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zkilleman_Navigator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Zkilleman_Navigator.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP Version 5.1
 *
 * @category  Zkilleman
 * @package   Zkilleman_Navigator
 * @author    Henrik Hedelund <henke.hedelund@gmail.com>
 * @copyright 2011 Henrik Hedelund (henke.hedelund@gmail.com)
 * @license   http://www.gnu.org/licenses/lgpl.html GNU LGPL
 * @link      https://github.com/henkelund/magento-zkilleman-navigator
 */

/**
 * Zkilleman_Navigator_Helper_Data
 *
 * @category   Zkilleman
 * @package    Zkilleman_Navigator
 * @author     Henrik Hedelund <henke.hedelund@gmail.com>
 */
class Zkilleman_Navigator_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Storage var for lazy-loaded cache key data
     * @var array
     */
    public static $_cacheKeyInfo = null;

    /**
     * Renders Node Html
     *
     * @param Zkilleman_Navigator_Model_Node $node
     * @return string Node Html
     */
    public function getNodeHtml(Zkilleman_Navigator_Model_Node &$node)
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
                if ($model instanceof Zkilleman_Navigator_Model_Node_Content_Type) {
                    $ckInfo = $model->getCacheKeyInfo();
                    if (is_string($ckInfo) && strlen($ckInfo) > 0) {
                        self::$_cacheKeyInfo[$type['model']] = $ckInfo;
                    }
                }
            }
            $visibilityTypes = Mage::getModel('navigator/node_visibility')->getTypes();
            foreach ($visibilityTypes as $type) {
                $model = Mage::getModel($type['model']);
                if ($model instanceof Zkilleman_Navigator_Model_Node_Visibility_Abstract) {
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
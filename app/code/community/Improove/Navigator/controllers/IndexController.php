<?php

class Improove_Navigator_IndexController extends Mage_Core_Controller_Front_Action
{
    const VISIBILITY_TYPES_XML_PATH = 'global/navigator/visibility/type';

    public function indexAction()
    {
        /*$result = array();
        $visibilities = Mage::getConfig()->getNode(self::VISIBILITY_TYPES_XML_PATH)->asArray();
        foreach($visibilities as $code => $visibility) {
            $module = 'navigator';
            if(isset($visibility['@']['module'])) {
                $module = $visibility['@']['module'];
            }
            $label = Mage::helper($module)->__($visibility['label']);
            $result[$code] = array('module' => $module, 'label' => $label);
        }
        var_dump($result);*/
        //$node = Mage::getModel('navigator/node')->load(3);
        //$node->setContentData('checkout/cart/index')->save();
        //echo $node->getLink();
//        $item = Mage::getModel('navigator/node_item')->load(1);
//        $item->setData('data', 'http://www.google.se');
//        $item->save();
        //$node->save();
        /*$tree = Mage::getModel('navigator/tree')->load();
        $node = Mage::getModel('navigator/node');
        $node->setCode('reptiles');
        $node->setTitle('Reptiles');
        $node->save();*/
    }
}

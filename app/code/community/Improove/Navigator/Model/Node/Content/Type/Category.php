<?php

class Improove_Navigator_Model_Node_Content_Type_Category
    extends Improove_Navigator_Model_Node_Content_Type_Link
{
    const CHILDREN_POSITION_BEFORE = 'before';
    const CHILDREN_POSITION_AFTER = 'after';

    protected static $_pseudoCategoryInstance = null;

    public function setNode(Improove_Navigator_Model_Node &$node)
    {
        parent::setNode($node);

        // Kind of ugly condition but it works to avoid infinite recursion
        if (!$node->getIsNotRootOfCategoryTree()) {
            $category = $this->getCategory();

            $childCategories = $category
                ->getCategories(
                        $category->getId(),
                        (int) $this->_getContentDataItem('max_depth'),
                        false, // sorted
                        false, // asCollection
                        true // toLoad
            );

            $childrenBefore = $this->childrenBeforeNodeChildren();
            $prevSibling = null;
            foreach ($childCategories as $child) {
                if(!$child->getIsActive())
                    continue;
                $newNode = Mage::getModel('navigator/node')
                    ->setContentType($node->getContentType())
                    ->setIsEnabled(true)
                    ->setId(md5($child->getId()))
                    ->setIsNotRootOfCategoryTree(true);

                $newNode->setTitle($child->getName());
                $newNode->getContentTypeInstance()->setCategory($child);
                $this->_buildNodeTree($newNode, $child, $node->getLevel() + 1);

                if($childrenBefore) {
                    $node->insertChild($newNode, $prevSibling);
                }
                else {
                    $node->appendChild($newNode);
                }
                $prevSibling = $newNode;
            }
        }
    }

    protected function _buildNodeTree($node, $category, $level)
    {
        $children = array();
        $childrenCount = 0;

        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }

        foreach ($children as $child) {
            if(!$child->getIsActive())
                continue;
            $newNode = Mage::getModel('navigator/node')
                ->setContentType($node->getContentType())
                ->setIsEnabled(true)
                ->setLevel($level)
                ->setIsNotRootOfCategoryTree(true);

            $newNode->setTitle($child->getName());
            $newNode->getContentTypeInstance()->setCategory($child);
            $this->_buildNodeTree($newNode, $child, $level + 1);

            $node->appendChild($newNode);
        }
    }

    protected static function _getPseudoCategoryInstance()
    {
        if(self::$_pseudoCategoryInstance === null) {
            self::$_pseudoCategoryInstance =
                Mage::getModel('catalog/category');
        }
        return self::$_pseudoCategoryInstance;
    }

    /**
     *
     * @return <type> 
     */
    public function getCategoryOptionArray()
    {
        $storeId = (int) $this->getNode()->getStoreId();
        $rootId = null;

        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $rootId = $store->getRootCategoryId();
        }
        else {
            $rootId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
        }
        $tree = Mage::getResourceSingleton('catalog/category_tree')
                ->load(null, $recursionLevel);

        $collection = Mage::getModel('catalog/category')->getCollection();
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active')
            ->setStoreId($this->getNode()->getStoreId());

        $tree->addCollectionData($collection);
        $root = $tree->getNodeById($rootId);

        if (is_array($root)) {
            $root = new Varien_Data_Tree_Node($root, 'entity_id', new Varien_Data_Tree);
        }

        return $this->_getCategoryOptionArray($root);
    }

    public function getChildrenPositionOptionArray()
    {
        return array(
            self::CHILDREN_POSITION_BEFORE =>
                Mage::helper('navigator')->__('Before node children'),
            self::CHILDREN_POSITION_AFTER =>
                Mage::helper('navigator')->__('After node children')
        );
    }

    public function childrenBeforeNodeChildren()
    {
        return self::CHILDREN_POSITION_AFTER !=
                (string) $this->_getContentDataItem('children_position');
    }

    protected function _getCategoryOptionArray($node, $level = 0)
    {
        $pad = str_pad('', $level*4, '.');
        $result =  array(
            $node->getId() =>
            (strlen($pad) ? $pad . '| ' : '') . $node->getName()
        );
        foreach ($node->getChildren() as $child) {
            if($child->getIsActive()) {
                $cResult = $this->_getCategoryOptionArray($child, $level + 1);
                foreach($cResult as $cId => $cLabel) {
                    $result[$cId] = $cLabel;
                }
            }
        }
        return $result;
    }

    /**
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    public function populateFieldset(Varien_Data_Form_Element_Fieldset &$fieldset)
    {
        $categoryId = $fieldset->addField('category_id', 'select', array(
            'label' => Mage::helper('navigator')->__('Category'),
            'name'  => 'content_fields[category_id]',
            'value' => (string) $this->_getContentDataItem('category_id'),
            'options' => $this->getCategoryOptionArray()
        ));

        $childPos = $fieldset->addField('children_position', 'select', array(
            'label' => Mage::helper('navigator')
                            ->__('Place category children...'),
            'name'  => 'content_fields[children_position]',
            'value' => (string) $this->_getContentDataItem('children_position'),
            'options' => $this->getChildrenPositionOptionArray()
        ));

        /*$overrideTitle = $fieldset->addField('override_title', 'select', array(
            'label' => Mage::helper('navigator')->__('Override title'),
            'name'  => 'content_fields[override_title]',
            'value' => (string) $this->_getContentDataItem('override_title'),
            'options' => array(
                        '1' => Mage::helper('navigator')->__('Yes'),
                        '0' => Mage::helper('navigator')->__('No')
            ),
            'note' => Mage::helper('navigator')
                        ->__('Choose &apos;Yes&apos; to use the category name' .
                             ' instead of the node title')
        ));*/

        $maxDepth = $fieldset->addField('max_depth', 'text', array(
            'label' => Mage::helper('navigator')->__('Maximum depth'),
            'name'  => 'content_fields[max_depth]',
            'value' => (int) $this->_getContentDataItem('max_depth')
        ));
    }

    public function setCategoryId($id)
    {
        $this->_setContentDataItem('category_id', $id);
        return $this;
    }
    
    public function getCategory()
    {
        if (!$this->hasData('category')) {
            $this->setData(
                'category',
                Mage::getModel('catalog/category')->load(
                    (int) $this->_getContentDataItem('category_id')
                )
            );
        }
        return $this->getData('category');
    }

    public function getHref()
    {
        return self::_getPseudoCategoryInstance()
            ->setData($this->getCategory()->getData())
            ->getUrl();
    }

    public function isInCurrentPath()
    {
        if ($layer = Mage::getSingleton('catalog/layer')) {
            $current = $layer->getCurrentCategory();
            return in_array(
                (int) $this->getCategory()->getId(),
                $current->getPathIds()
            );
        }
        return false;
    }

    /*public function getChildNodes()
    {
        Mage::log($this->getCategory()->getName(), null, 'navigator.log', true);
        $childrenIds = explode(',', $this->getCategory()->getChildren());
        $type = $this->getNode()->getContentType();
        $result = array();
        foreach($childrenIds as $id) {
            $node = Mage::getModel('navigator/node')->setContentType($type);
            $node->getContentTypeInstance()->setCategoryId($id);
            $result[] = $node;
        }
        return $result;
    }*/
}
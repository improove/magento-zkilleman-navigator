<?php
class Zkilleman_Navigator_Model_Entity_Attribute_Source_Contenttype extends Mage_Eav_Model_Entity_Attribute_Source_Config
{
    public function getAllOptions()
    {
        $types = array();
        foreach(Zkilleman_Navigator_Model_Node_Content_Type::getTypes()
                as $code => $type) {
            $types[$code] = $type['label'];
        }
        return $types;
    }
}
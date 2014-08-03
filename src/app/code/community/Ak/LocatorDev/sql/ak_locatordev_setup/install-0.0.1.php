<?php

$installer = $this;

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'website', array(
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Website',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 50,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->addAttribute(Ak_Locator_Model_Location::ENTITY, 'store_code', array(
    'input'         => 'text',
    'type'          => 'text',
    'label'         => 'Store Code',
    'backend'       => '',
    'user_defined'  => false,
    'visible'       => 1,
    'required'      => 0,
    'position'    => 60,
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$formAttributes = array(
   'store_code',
   'website'
);

$eavConfig = Mage::getSingleton('eav/config');

foreach($formAttributes as $code){
    $attribute = $eavConfig->getAttribute(Ak_Locator_Model_Location::ENTITY, $code);
    $attribute->setData('used_in_forms', array('location_edit','location_create'));
    $attribute->save();
}

$installer->endSetup(); 

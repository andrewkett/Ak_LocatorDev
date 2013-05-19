<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrewkett
 * Date: 19/05/13
 * Time: 12:42 PM
 * To change this template use File | Settings | File Templates.
 */


class MageBrews_LocatorDev_Adminhtml_LocatordevController extends Mage_Adminhtml_Controller_Action
{
    public function installtestdataAction()
    {
        Mage::getModel('magebrews_locatordev/import')->run();
    }

}
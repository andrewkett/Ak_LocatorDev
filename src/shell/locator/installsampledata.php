<?php

$base = realpath(dirname(__FILE__).'/');

require_once $base.'/../abstract.php';
require_once $base.'/../../app/Mage.php';
umask(0);
Mage::app();

if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
}
ini_set('memory_limit','2048M');
ini_set('display_errors', 1);

class Locator_Shell_Install extends Mage_Shell_Abstract
{

    public function run()
    {
        Mage::getModel('ak_locatordev/import')->run();
    }

}

$shell = new Locator_Shell_Install();
$shell->run();


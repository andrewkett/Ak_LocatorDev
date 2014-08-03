<?php

/**
 * @category   Ak_LocatorDev
 * @package    Ak_LocatorDev
 * @author     Andrew Kett
 */

class Ak_LocatorDev_Model_Import extends Varien_Object
{
    private $headers = array();

    public function run()
    {
        //die('run import');
        $filepath =  Mage::getModuleDir('','Ak_LocatorDev') .'/data/nz-supermarkets.csv';
        $i = 0;
        if(($handle = fopen("$filepath", "r")) !== FALSE) {
            while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
                if($i==0){
                    $this->setHeaders($data);
                }else{
                    $this->saveFromCsv($this->parseCsv($data));
                }
                $i++;
            }
        }
        else{
            Mage::getSingleton('adminhtml/session')->addError("There is some Error");
            $this->_redirect('*/*/index');
        }
    }

    public function saveFromCsv($data)
    {
        $loc = Mage::getModel('ak_locator/location');

        if(count($loc->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('store_code',$data['id']))){
            //echo 'updating existing store '.trim($data['name']).PHP_EOL;
            $loc = $loc->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('store_code',$data['id'])
                        ->getFirstItem();
        }else{
            //echo 'importing new store '.trim($data['name']).PHP_EOL;
        }

        //add all attributes that can be copied across directly without manipulation
        $mapper = array(
            'title' => 'name',
            'address' => 'address',
            'website' => 'website',
            'store_code' => 'id',
            'latitude' =>'geometry_y',
            'longitude' => 'geometry_x'
        );
        foreach($mapper as $att => $col){
            $loc->setData($att,$data[$col]);
        }

        $loc->setData('country','new zealand');

        $loc->setGeocoded(1);

        $loc->setUrlKey(str_replace("'", '', strtolower(str_replace(' ', '-', $data['name']))));


        $loc->save();
        echo trim($data['name']).' saved'.PHP_EOL;
    }


    private function setHeaders($data)
    {
        foreach($data as $col){
            $this->headers[] = str_replace(' ', '_', strtolower($col));
        }

    }

    /**
     * parse csv row to array with column header as key
     */
    private function parseCsv($data)
    {
        $storeData = array();

        $col = 0;
        foreach($data as $value){
            $storeData[$this->headers[$col]] = trim($value);
            $col++;
        }

        return $storeData;
    }
}

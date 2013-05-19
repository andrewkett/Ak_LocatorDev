<?php

/**
 * @category   MageBrews_LocatorDev
 * @package    MageBrews_LocatorDev
 * @author     Andrew Kett
 */

class MageBrews_LocatorDev_Model_Import extends Varien_Object
{
    private $headers = array();

    public function run()
    {
        //die('run import');
        $filepath =  Mage::getModuleDir('','MageBrews_LocatorDev') .'/data/nz-supermarkets.csv';
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
        $loc = Mage::getModel('magebrews_locator/location');

        if(count($loc->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('store_code',$data['id']))){
            echo 'updating existing store '.trim($data['name']).PHP_EOL;
            $loc = $loc->getCollection()
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('store_code',$data['id'])
                        ->getFirstItem();
        }else{
            echo 'importing new store '.trim($data['name']).PHP_EOL;
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

//        if($geodata = $this->geocodeData($data)){
//            echo 'geocoding address'.PHP_EOL;
//
//
//            $addressComponents = array(
//                'sub_premise',
//                'premise',
//                'thoroughfare',
//                'postalcode',
//                'locality',
//                'dependent_locality',
//                'administrative_area',
//                'sub_administrative_area',
//                'country',
//                'latitude',
//                'longitude'
//            );
//
//            //clear out existing address data
//            foreach($addressComponents as $component){
//                $loc->setData($component,'');
//            }
//
//            $loc->setLatitude($geodata->geometry->location->lat);
//            $loc->setLongitude($geodata->geometry->location->lng);
//
//            foreach($geodata->address_components as $component){
//
//                switch ($component->types[0]) {
//                    case 'country':
//                        $loc->setCountry($component->long_name);
//                        break;
//                    case 'administrative_area_level_1':
//                        $loc->setAdministrativeArea($component->long_name);
//                        break;
//                    case 'locality':
//                        $loc->setLocality($component->long_name);
//                        break;
//                    case 'postal_code':
//                        $loc->setPostalCode($component->long_name);
//                        break;
//                    case 'route':
//                        $loc->setThoroughfare($component->long_name);
//                        break;
//                    case 'street_number':
//                        $loc->setPremise($component->long_name);
//                        break;
//                    case 'subpremise':
//                        $loc->setSubPremise($component->long_name);
//                        break;
//                }
//
//            }
//            $loc->setGeocoded(1);
//        }

        $loc->save();
        echo trim($data['name']).' saved<br />'.PHP_EOL;
    }


    private function setHeaders($data)
    {
        foreach($data as $col){
            $this->headers[] = str_replace(' ', '_', strtolower($col));
        }

    }

//    /**
//     *  Get address in display format from csv data
//     */
//    public function getAddress($data)
//    {
//        $parts = array();
//
//        if($data['store_address']){
//            $parts[] = $data['store_address'];
//        }
//        if($data['store_address_2']){
//            $parts[] = $data['store_address_2'];
//        }
//        if($data['store_address_3']){
//            $parts[] = $data['store_address_3'];
//        }
//
//        return implode(', ', $parts);
//    }

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

//    /**
//     * attempt to generate a lat long value from address data givin
//     */
//    private function geocodeData($data)
//    {
//        //return false;
//        include_once(Mage::getBaseDir('lib').'/geoPHP/geoPHP.inc');
//        $key = Mage::getStoreConfig('locator_settings/google_maps/api_key');
//        $geocoder = new GoogleGeocode($key);
//        $query = $data['club_title'].', '.$data['club_address'].' '.$data['club_address_two'].', '.$data['store_suburb'].', '.$data['postcode'].', '.$data['state'].', Australia';
//
//        $result = $geocoder->read($query,'raw');
//
//        if($result){
//            return $result;
//        }else{
//            return false;
//        }
//    }
}
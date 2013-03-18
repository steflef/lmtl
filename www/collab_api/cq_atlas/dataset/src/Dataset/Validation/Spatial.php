<?php

namespace Dataset\Validation;

/**
 * Validate Minimum Rows
 *
 * @package Dataset
 */
class Spatial extends \Dataset\Validation\Base
{

    protected static $longitudeHeaders = array(
        'lon',
        'lng',
        'longitude'
    );

    protected static $latitudeHeaders = array(
        'lat',
        'latitude'
    );

    protected static $locationHeaders = array(
        'adresse',
        'address',
        'addr',
        'location',
        'localisation'
    );

    /**
     * Error message
     * @var string
     */
    protected $message = 'Minimum de %s lignes';

    /**
     * Constructor
     *
     * @param number $minimumRows Minimum Rows
     * @example new \Dataset\Validation\Extension(2)
     */
    public function __construct( )
    {

    }

    public function matchKeys($keys, $lookup)
    {
        foreach($lookup as $item){
            foreach ($keys as $key) {
                if(mb_strtolower($key) === $item){
                    return $key;
                }
            }
        }
        return false;
    }




    /**
     * Validate
     * @param  \Upload\File $file
     * @return bool
     */
    public function validate(\CQAtlas\Helpers\AbstractReader $Reader)
    {
        $isValid = true;


            // ###Spatial Fields Validation
        try{
            $lonHeader = $this->matchKeys($fileData['header'], self::$longitudeHeaders);
            $latHeader = $this->matchKeys($fileData['header'], self::$latitudeHeaders);
            $locationHeader = $this->matchKeys($fileData['header'], self::$locationHeaders);

            if( $lonHeader === false || $latHeader === false){
                if( $locationHeader === false ){
                    // #####>> STOP - No Location Field Detected
                    throw new Exception('Aucune entête spatiale (lon,lat ou adresse)');
                }
                // #####Data Need Geocoding! Now on the Client Side ...Easier on Quota
                $fileData['geoFields']['locField'] = $locationHeader;
                $fileData['geoFields']['geocoded'] = 0;
            }else
            {
                // ####Lng/Lat Validation
                require 'models/geo.php';
                $llErrors = 0;
                $llValids = 0;
                $llErrorsObjs = array();

                foreach($fileData['rows'] as $row){
                    if(Geo::geo_utils_is_valid_longitude($row[$lonHeader]) && Geo::geo_utils_is_valid_latitude($row[$latHeader])){
                        $llValids ++;
                    }else{
                        $llErrors ++;
                        $llErrorsObjs[] = '('.$row[$lonHeader].','.$row[$latHeader].')';
                    }
                }
                if( $llErrors > 0 ){
                    throw new \Exception('Certaines coordonnées (lon,lat) sont invalides. '.$llErrors.' erreurs.{'. implode(',',$llErrorsObjs).'}');
                }

                $fileData['geoFields']['lonField'] = $lonHeader;
                $fileData['geoFields']['latField'] = $latHeader;
            }





            if ($Reader->getRowsCount()< $this->minimumRows) {
                $this->setMessage(sprintf($this->message, $this->minimumRows));
                $isValid = false;
            }

            return $isValid;

        }catch (\Exception $e){

        }
    }
}
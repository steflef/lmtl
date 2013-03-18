<?php
/**
 * Dataset
 *
 * @author      Stephane Lefebvre <info@steflef.com>
 * @copyright   2013 Stephane Lefebvre
 * @link        http://www.steflef.com
 * @version     1.0.0
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Dataset;

/**
 * File
 *
 * This class provides the implementation for a Dataset file. It exposes
 * common attributes for the dataset file (e.g. name, extension, media type)
 * and allows you to attach validations to the file that must pass for the
 * dataset to be added to a collection.
 *
 * @author  StefLef <info@steflef.com>
 * @since   1.0.0
 * @package Dataset
 */
class Dataset
{
    /********************************************************************************
    * Static Properties
    *******************************************************************************/



    /********************************************************************************
    * Instance Properties
    *******************************************************************************/

    /**
     * File source
     * @var \SplFileInfo
     */
    protected $File;

    /**
     * File source
     * @var \Pimple
     */
    protected $di;

    protected $Reader;

    protected $delimiter;

    /**
     * Validations
     * @var array[\Dataset\Validation\Base]
     */
    protected $validations;

    protected $errors;

    /**
     * Constructor
     * @param  \SplFileInfo $source
     */
    public function __construct(\SplFileInfo $File, \Pimple $di, $delimiter=',')
    {
        $this->File = $File;
        $this->di = $di;
        $this->delimiter = ($delimiter === null)? ',': $delimiter;
        $this->validations = array();
        $this->errors = array();
        # 1-ok Create Reader
        # 2-ok Validate
        # 3- Extract Meta setMetadata()
        # 4- Extract Fields Metadata setFieldsMeta()
        # 5- Extract Spatial Fields setSpatial()
        # 6- Json Dump Dataset->toJson(), ->toArray()
    }

    public function process(){

        $this->read();
        if(!validate()){
            throw new \Exception( implode(',',$this->errors));
        }

        //$this->

    }

    protected function read(){

        // ####Delimited File
        if( in_array($this->File->getExtension(), $this->di['delimitedExtensions'] )){

            $this->Reader = new \CQAtlas\Helpers\DelimitedReader($this->getPath(),0,$this->delimiter);
            return $this;
        }
        // ####Excel File
        if( in_array($this->File->getExtension(), $this->di['excelExtensions']) ){

            $this->Reader = new \CQAtlas\Helpers\ExcelReader($this->getPath(),0);
            return $this;
        }

        throw new \Exception('Format non compatible (Reader).');
    }


    protected function getPath(){
        return sprintf('%s/%s.%s',
            $this->di['uploadDir'],
            $this->File->getFilename(),
            $this->File->getExtension());
    }

    protected function getMetadata(){
        return array();
    }
    protected function getFieldsMetadata(){
        return array();
    }
    protected function getData(){
        return array();
    }

    protected function getHeaders(){
        return $this->Reader->getHeaderRow();
    }

    public function toArray(){
        return array(
            'data' => $this->getData(),
            'headers' =>$this->getFieldsMetadata(),
            'metadata' => $this->getMetadata()
        );
    }

    public function toJson(){
        return json_encode($this->toArray());
    }

    /********************************************************************************
    * Validate
    *******************************************************************************/

    /**
     * Add file validations
     * @param \Dataset\Validation\Base|array[\Dataset\Validation\Base] $validations
     */
    public function addValidations($validations)
    {
        if (!is_array($validations)) {
            $validations = array($validations);
        }
        foreach ($validations as $validation) {
            if ($validation instanceof \Dataset\Validation\Base) {
                $this->validations[] = $validation;
            }
        }
    }

    /**
     * Get file validations
     * @return array[\Upload\Validation\Base]
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * Validate file
     * @return bool True if valid, false if invalid
     */
    public function validate()
    {
        // User validations
        foreach ($this->validations as $validation) {
            if ($validation->validate($this->Reader) === false) {
                $this->errors[] = $validation->getMessage();
            }
        }

        return empty($this->errors);
    }

    /**
     * Get file validation errors
     * @return array[String]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add file validation error
     * @param  string
     * @return \Upload\File Self
     */
    public function addError($error)
    {
        $this->errors[] = $error;

        return $this;
    }

}

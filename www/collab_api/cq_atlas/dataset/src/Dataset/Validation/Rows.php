<?php

namespace Dataset\Validation;

/**
 * Validate Minimum Rows
 *
 * @package Dataset
 */
class Rows extends \Dataset\Validation\Base
{

    protected $minimumRows;

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
    public function __construct( $minimumRows )
    {
        $this->minimumRows = $minimumRows;
    }

    /**
     * Validate
     * @param  \Upload\File $file
     * @return bool
     */
    public function validate(\CQAtlas\Helpers\AbstractReader $Reader)
    {
        $isValid = true;

        if ($Reader->getRowsCount()< $this->minimumRows) {
            $this->setMessage(sprintf($this->message, $this->minimumRows));
            $isValid = false;
        }

        return $isValid;
    }
}
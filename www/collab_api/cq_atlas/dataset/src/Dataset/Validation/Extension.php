<?php

namespace Dataset\Validation;

/**
 * Validate File Extension
 *
 * This class validates an uploads file extension. It takes file extension with out dot
 * or array of extensions. For example: 'png' or array('jpg', 'png', 'gif').
 *
 * WARING! Validation only by file extension not very secure.
 *
 * @author  Alex Kucherenko <kucherenko.email@gmail.com>
 * @package Upload
 */
class Extension extends \Dataset\Validation\Base
{
    /**
     * Array of cceptable file extensions without leading dots
     * @var array
     */
    protected $allowedExtensions;

    /**
     * Error message
     * @var string
     */
    protected $message = 'Invalid file extension. Must be one of: %s';

    /**
     * Constructor
     *
     * @param string|array $allowedExtensions Allowed file extensions
     * @example new \Dataset\Validation\Extension(array('png','jpg','gif'))
     * @example new \Dataset\Validation\Extension('png')
     */
    public function __construct($allowedExtensions)
    {
        if (is_string($allowedExtensions)) {
            $allowedExtensions = array($allowedExtensions);
        }

        array_filter($allowedExtensions, function ($val) {
            return strtolower($val);
        });

        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Validate
     * @param  \Upload\File $file
     * @return bool
     */
    public function validate(\Upload\File $file)
    {
        $fileExtension = strtolower($file->getExtension());
        $isValid = true;

        if (!in_array($fileExtension, $this->allowedExtensions)) {
            $this->setMessage(sprintf($this->message, implode(', ', $this->allowedExtensions)));
            $isValid = false;
        }

        return $isValid;
    }
}

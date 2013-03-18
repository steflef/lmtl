<?php

namespace Dataset\Storage;

/**
 * FileSystem Storage
 *
 * This class uploads files to a designated directory on the filesystem.
 *
 * @author  Josh Lockhart <info@joshlockhart.com>
 * @since   1.0.0
 * @package Upload
 */
class FileSystem extends \Dataset\Storage\Base
{
    /**
     * Upload directory
     * @var string
     */
    protected $directory;

    /**
     * Overwrite existing files?
     * @var bool
     */
    protected $overwrite;

    /**
     * Constructor
     * @param  string                       $directory      Relative or absolute path to upload directory
     * @param  bool                         $overwrite      Should this overwrite existing files?
     * @throws \InvalidArgumentException                    If directory does not exist
     * @throws \InvalidArgumentException                    If directory is not writable
     */
    public function __construct($directory, $overwrite = false)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('Directory does not exist');
        }
        if (!is_writable($directory)) {
            throw new \InvalidArgumentException('Directory is not writable');
        }
        $this->directory = rtrim($directory, '/') . DIRECTORY_SEPARATOR;
        $this->overwrite = $overwrite;
    }

    /**
     * Upload
     * @param  \Upload\File $file The file object to upload
     * @return bool
     * @throws \RuntimeException   If overwrite is false and file already exists
     */
    public function upload(\Upload\File $file)
    {
        $newFile = $this->directory . $file->getNameWithExtension();
        if ($this->overwrite === false && file_exists($newFile)) {
            $file->addError('File already exists');
            throw new \Upload\Exception\UploadException('File already exists');
        }

        return $this->moveUploadedFile($file->getPathname(), $newFile);
    }

    /**
     * Move uploaded file
     *
     * This method allows us to stub this method in unit tests to avoid
     * hard dependency on the `move_uploaded_file` function.
     *
     * @param  string $source      The source file
     * @param  string $destination The destination file
     * @return bool
     */
    protected function moveUploadedFile($source, $destination)
    {
        return move_uploaded_file($source, $destination);
    }
}

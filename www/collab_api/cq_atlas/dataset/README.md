# Datasets

## Usage

This component simplifies dataset validation.

The server-side PHP code can validate the Dataset like this:

    <?php
    $storage = new \Upload\Storage\FileSystem('/path/to/directory');
    $file = new \Upload\File('foo', $storage);

    // Validate file upload
    $file->addValidations(array(
        // Ensure file is of type "image/png"
        new \Upload\Validation\Mimetype('image/png'),

        // Ensure file is no larger than 5M (use "B", "K", M", or "G")
        new \Upload\Validation\Size('5M')
    ));

    // Try to upload file
    try {
        // Success!
        $file->upload();
    } catch (\Exception $e) {
        // Fail!
        $errors = $file->getErrors();
    }

## How to Install

Install composer in your project:

    curl -s https://getcomposer.org/installer | php

Create a composer.json file in your project root:

    {
        "require": {
            "codeguy/upload": "*"
        }
    }

Install via composer:

    php composer.phar install

## Author

[Josh Lockhart](https://github.com/codeguy)

## License

MIT Public License

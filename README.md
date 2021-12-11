# The Filesystem Package [![Build Status](https://ci.joomla.org/api/badges/joomla-framework/filesystem/status.svg?ref=refs/heads/2.0-dev)](https://ci.joomla.org/joomla-framework/filesystem)

## File upload example

```php
use Joomla\Filesystem\File;

$file = $this->input->files->get('file');

$config = array(
    'extensions'    => 'jpg,jpeg,gif,png,pdf,doc,docx',
    'max_size'      => 30000000, // 30 MB
    'folder'        => 'documents'
);

// Check there is some file to upload
if (empty($file['name']))
{
    return;
}

// Check max size
if ($file['size'] > $config['max_size'])
{
    throw new \RuntimeException('Uploaded file size (' . round($file['size'] / 1000) . ' kB) is greater than allowed size (' . round($config['max_size'] / 1000) . ' kB).');
}

$config['extensions'] = explode(',', $config['extensions']);

// Get File extension
$ext = strtolower(substr($file['name'], (strrpos($file['name'], '.') + 1)));

// Sanitize allowed extensions
foreach ($config['extensions'] as &$extension)
{
    $extension = str_replace('.', '', trim(strtolower($extension)));
}

// Check allowed extensions
if (!in_array($ext, $config['extensions']))
{
    throw new \RuntimeException('Uploaded file extension (' . $ext . ') is not within allowed extensions (' . implode(',', $config['extensions']) . ')');
}

$path = JPATH_ROOT . '/' . $config['folder'] . '/' . File::makeSafe($file['name']);

File::upload($file['tmp_name'], $path);
```

## Changes From 1.x

### Patcher

In 1.x, the second parameter of the `add` and `addFile` methods was optional.  In 2.0, this parameter is required.  This parameter requires the
root path of the source which you are patching.

## Installation via Composer

Add `"joomla/filesystem": "~2.0"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/filesystem": "~2.0"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
```

If you want to include the test sources, use

```sh
composer require --prefer-source joomla/filesystem "~2.0"
```

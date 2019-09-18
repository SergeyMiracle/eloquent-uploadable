# Eloquent Uploadable

A trait to automatically handle file uploads for Laravel Eloquent models.

## Installation
`composer require sergeymiracle/eloquent-uploadable`

## Example Usage

```
use SergeyMiracle\Uploadable\UploadableModelTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {

  use UploadableModelTrait;
  
  // define options function
  public function getUploadableOptions(): array
  {
    return [
        'attributes' => [ // model attributes which you'd like to be automatically handled by the trait.
            'featured_image',
            'featured_file'
        ],
        'directory' => 'my_dir', // destination directory, optional
        'disk' => 'my_disk' // flysystem disk, optional, if not present in return array disk from config file used
    ];
  }

}
```

On saving array of files, a json encoded string saved in database.

## Changelog
* 3.1.0 - *added new config options for filename generation*
* 3.0.0 - *remove options - uploadables, use function getUploadableOptions() instead*
* 2.0.0 - *removed UtilsTrait.php and UploadableControllerTrait.php, UploadableFileHandler can be used instead, php 7.2*

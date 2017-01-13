# Eloquent Uploadable

A trait to automatically handle file uploads for Laravel Eloquent models.

## Example Usage

```
use SergeyMiracle\Uploadable\UploadableModelTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {

  use UploadableModelTrait;

  protected $uploadables = ['featured_image'];

  protected $upload_dir = '.images';
  
  protected $cropped = [
          'featured_image' => ['width' => 25, 'height' => 25]
      ];

}
```

Our model's `$uploadables` is an array of file input name attributes which you'd like to be automatically handled by the trait.

`$upload_dir` destination folder

`$cropped` is an array of file input name which you'd like to be cropped.

Setup 'upload' disk in `config/filesystems.php`

```
'disks' => [

        ...

        'upload' => [
            'driver' => 'local',
            'root' => public_path('upload'),
            'visibility' => 'public',
        ],

        ...
    ]
```

On saving array of files, a json encoded string saved in database.

Includes uploadable trait for controller - `UploadableControllerTrait`
UploadableControllerTrait has two methods: `moveFile` and `moveImage`
Both expects `UploadedFile` instance. moveImage methods crops image.

```
$this->moveFile($request->file('image'))

$this->moveImage($request->file('image'), $width, $height)
```
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

}
```

Our model's `$uploadables` is an array of file input name attributes which you'd like to be automatically handled by the trait.

`$upload_dir` destination folder

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

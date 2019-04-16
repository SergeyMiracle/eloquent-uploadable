# Eloquent Uploadable

A trait to automatically handle file uploads for Laravel Eloquent models.

## Example Usage

```
use SergeyMiracle\Uploadable\UploadableModelTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {

  use UploadableModelTrait;

  protected $uploadables = ['featured_image'];

  protected $upload_dir = '.images'; // optional

}
```

Our model's `$uploadables` is an array of file input name attributes which you'd like to be automatically handled by the trait.

`$upload_dir` destination folder prefix, can be omitted


On saving array of files, a json encoded string saved in database.

`UploadableFileHandler` can be used directly

```
    UploadableFileHandler::save($dir_name, Illuminate\Http\UploadedFile $file, $file_name);
    
    UploadableFileHandler::delete($file_path);
```

## Changelog
* 2.0.0 - **removed UtilsTrait.php and UploadableControllerTrait.php, UploadableFileHandler can be used instead, php 7.2**

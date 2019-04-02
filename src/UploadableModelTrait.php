<?php

namespace SergeyMiracle\Uploadable;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait UploadableModelTrait
{
    use UtilsTrait;

    /**
     * Boot the trait's observer.
     *
     * @return void
     */
    public static function bootUploadableModelTrait()
    {
        static::observe(new UploadableModelObserver());
    }


    /**
     * When saving a model, upload any 'uploadable' fields.
     *
     * @return void
     */
    public function performUploads()
    {
        $this->checkForUploadables();

        foreach ($this->getUploadables() as $key) {
            if (request()->hasFile($key)) {
                if ($this->original && $this->original[$key]) {
                    $this->deleteExisting($key);
                }

                $files = request()->file($key);

                if (is_array($files)) {
                    $output = [];
                    foreach ($files as $file) {
                        $output[] = $this->moveFile($file);
                    }

                    $this->attributes[$key] = json_encode($output);
                } else {
                    $this->attributes[$key] = $this->moveFile($files);
                }

                $this->performCrop();
            }
        }
    }

    private function performCrop()
    {
        if (!$this->cropped) return;

        foreach ($this->cropped as $key => $attr) {
            \Image::make(public_path($this->attributes[$key]))->fit($attr['width'], $attr['height'])->save();
        }
    }

    /**
     * When deleting a model, cleanup the file system too.
     *
     * @return void
     */
    public function performDeletes()
    {
        $this->checkForUploadables();

        foreach ($this->getUploadables() as $key) {
            $this->deleteExisting($key);
        }
    }

    /**
     * Save file on disk
     *
     * @param $file \Illuminate\Http\UploadedFile;
     * @return string
     */
    private function moveFile($file)
    {
        try {
            $path = \Storage::disk(config('uploadable.disk'))->putFileAs($this->getUploadDir(), $file, $this->createFileName($file->getClientOriginalName()));
        } catch (\Exception $e) {
            throw new FileException($e->getMessage());
        }

        $path = config('uploadable.root') . $path;

        if (config('uploadable.images.optimize') && getimagesize(public_path($path))) {
            $this->perfomOptimize(public_path($path));
        }

        return $path;
    }


    /**
     * Uploadable fields getter.
     *
     * @return array
     */
    public function getUploadables()
    {
        return $this->uploadables;
    }

    /**
     * Uploadable fields setter.
     *
     * @param array $uploadables
     */
    public function setUploadables($uploadables)
    {
        $this->uploadables = $uploadables;
    }

    /**
     * Check is $uploadables is a non-empty array.
     *
     * @return void
     * @throws \Exception
     */
    private function checkForUploadables()
    {
        if (!$this->getUploadables()) {
            throw new \Exception('$this->uploadables must be a non-empty array.');
        }
    }


    /**
     * Delete an existing 'uploadable' file in
     * the filesystem when deleting a Model.
     * @param   string $key
     * @return  bool
     */
    private function deleteExisting($key)
    {
        if (str_contains($this->original[$key], 'storage')) return true;

        if ($this->is_json($this->original[$key])) {
            $key = json_decode($this->original[$key]);

            foreach ($key as $path) {
                if (str_contains($path, 'storage')) continue;

                $path = public_path($path);

                if (file_exists($path) && is_file($path))
                    unlink($path);
            }

            return true;
        }

        $path = public_path($this->original[$key]);

        if (file_exists($path) && is_file($path)) {
            return unlink($path);
        }

        return false;
    }


    /**
     * @param string $upload_dir
     */
    public function setUploadDir(string $upload_dir)
    {
        $this->upload_dir = $upload_dir;
    }

    private function is_json($string) {
        return is_array(json_decode($string, true));
    }
}

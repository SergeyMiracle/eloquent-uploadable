<?php

namespace SergeyMiracle\Uploadable;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait UploadableModelTrait
{
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
                }


                $this->attributes[$key] = $this->moveFile($files);
            }
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
            $path = \Storage::disk('upload')->putFileAs($this->getUploadDir(), $file, $this->createFileName($file->getClientOriginalName()));
        } catch (\Exception $e) {
            throw new FileException($e->getMessage());
        }

        return '/upload/' . $path;
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
     * @param $file string
     * @return string
     */
    private function createFileName($file)
    {
        $path = pathinfo($file);

        return uniqid() . '_' . str_slug($path['filename'], '_') . '.' . $path['extension'];
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

        if (is_json($this->original[$key])) {
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
     * @return string
     */
    public function getUploadDir()
    {
        $date = new Carbon();
        return $this->upload_dir . DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month;
    }

    /**
     * @param string $upload_dir
     */
    public function setUploadDir(string $upload_dir)
    {
        $this->upload_dir = $upload_dir;
    }
}

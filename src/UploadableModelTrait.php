<?php

namespace SergeyMiracle\Uploadable;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

trait UploadableModelTrait
{
    /**
     * Boot the trait's observer.
     *
     * @return void
     */
    public static function bootUploadableModelTrait(): void
    {
        static::observe(new UploadableModelObserver());
    }


    /**
     * When saving a model, upload any 'uploadable' fields.
     *
     * @return void
     * @throws Exceptions\FileException
     * @throws Exception
     */
    public function performUploads(): void
    {
        $options = $this->getUploadableOptions();
        $uploadDir = $this->getUploadDir($options);

        $request = app('request');

        foreach ($options['attributes'] as $key) {
            if ($request->hasFile($key)) {
                if ($this->original && $this->original[$key]) {
                    UploadableFileHandler::delete($key, $options['attributes'] ?? null);
                }

                $files = $request->file($key);

                if (is_array($files)) {
                    $output = [];
                    foreach ($files as $file) {
                        $output[] = UploadableFileHandler::save(
                            $uploadDir,
                            $file,
                            $this->createFileName($file->getClientOriginalname()),
                            $options['disk'] ?? null
                        );
                    }

                    $this->attributes[$key] = json_encode($output);
                } else {
                    $this->attributes[$key] = UploadableFileHandler::save(
                        $uploadDir,
                        $files,
                        $this->createFileName($files->getClientOriginalname()),
                        $options['disk'] ?? null
                    );
                }
            }
        }
    }

    /**
     * When deleting a model, cleanup the file system too.
     *
     * @return void
     * @throws Exception
     */
    public function performDeletes(): void
    {
        $options = $this->getUploadableOptions();

        foreach ($options['attributes'] as $key) {
            if (isset($this->attributes[$key]) && !is_null($this->attributes[$key])) {
                UploadableFileHandler::delete($this->attributes[$key], $options['disk'] ?? null);
            }
        }
    }


    /**
     * @param $options
     * @return string
     * @throws Exception
     */
    protected function getUploadDir($options): string
    {
        $date = new Carbon();
        $dir = $options['directory'] ?? '';

        return $dir . DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month;
    }


    /**
     * @param $file string
     * @return string
     */
    protected function createFileName($file): string
    {
        $path = pathinfo($file);

        $name = trim($path['filename']);

        if (config('uploadable.file_name.slugify') === true) {
            $name = Str::slug($name, '_');
        }

        if (config('uploadable.file_name.uuid') === true) {
            $name .= '_' . (string)Str::uuid();
        }

        return $name . '.' . $path['extension'];
    }
}

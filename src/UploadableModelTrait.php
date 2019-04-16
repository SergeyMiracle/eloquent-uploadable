<?php

namespace SergeyMiracle\Uploadable;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use RuntimeException;

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
        $this->checkForUploadables();

        $request = app('request');

        foreach ($this->getUploadables() as $key) {
            if ($request->hasFile($key)) {
                if ($this->original && $this->original[$key]) {
                    UploadableFileHandler::delete($key);
                }

                $files = $request->file($key);

                if (is_array($files)) {
                    $output = [];
                    foreach ($files as $file) {
                        $output[] = UploadableFileHandler::save(
                            $this->getUploadDir(),
                            $file,
                            $this->createFileName($file->getClientOriginalname())
                        );
                    }

                    $this->attributes[$key] = json_encode($output);
                } else {
                    $this->attributes[$key] = UploadableFileHandler::save(
                        $this->getUploadDir(),
                        $files,
                        $this->createFileName($files->getClientOriginalname())
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
        $this->checkForUploadables();

        foreach ($this->getUploadables() as $key) {
            UploadableFileHandler::delete($this->attributes[$key]);
        }
    }


    /**
     * Uploadable fields getter.
     *
     * @return array
     */
    public function getUploadables(): array
    {
        return $this->uploadables;
    }

    /**
     * Check is $uploadables is a non-empty array.
     *
     * @return void
     * @throws Exception
     */
    private function checkForUploadables(): void
    {
        if (!$this->getUploadables()) {
            throw new RuntimeException('$this->uploadables must be a non-empty array.');
        }
    }


    /**
     * @return string
     * @throws Exception
     */
    protected function getUploadDir(): string
    {
        $date = new Carbon();

        return $this->upload_dir . DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month;
    }


    /**
     * @param $file string
     * @return string
     */
    protected function createFileName($file): string
    {
        $path = pathinfo($file);

        return Str::slug($path['filename'], '_') . '.' . $path['extension'];
    }
}

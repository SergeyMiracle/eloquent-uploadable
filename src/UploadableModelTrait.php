<?php

namespace SergeyMiracle\Uploadable;

use Exception;
use RuntimeException;

trait UploadableModelTrait
{
    use UploadableUtilsTrait;

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
                    $this->removeFile($key);
                }

                $files = $request->file($key);

                if (is_array($files)) {
                    $output = [];
                    foreach ($files as $file) {
                        $output[] = $this->moveFile($file);
                    }

                    $this->attributes[$key] = json_encode($output);
                } else {
                    $this->attributes[$key] = $this->moveFile($files);
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
            $this->removeFile($this->attributes[$key]);
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
     * Uploadable fields setter.
     *
     * @param array $uploadables
     */
    public function setUploadables($uploadables): void
    {
        $this->uploadables = $uploadables;
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
     * @param string $upload_dir
     */
    public function setUploadDir(string $upload_dir): void
    {
        $this->upload_dir = $upload_dir;
    }
}

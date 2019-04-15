<?php

namespace SergeyMiracle\Uploadable;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SergeyMiracle\Uploadable\Exceptions\FileException;

trait UtilsTrait
{
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

    /**
     * Save file on disk
     *
     * @param $file UploadedFile;
     * @return string
     * @throws FileException
     */
    public function moveFile($file): string
    {
        try {
            $path = Storage::disk(config('uploadable.disk'))->putFileAs($this->getUploadDir(), $file, $this->createFileName($file->getClientOriginalName()));
        } catch (Exception $e) {
            throw new FileException($e->getMessage());
        }

        return $path;
    }

    /**
     * Remove file
     *
     * @param $file
     * @return bool
     */
    public function removeFile($file): bool
    {
        return Storage::disk(config('uploadable.disk'))->delete($file);
    }
}

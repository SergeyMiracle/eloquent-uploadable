<?php

namespace SergeyMiracle\Uploadable;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use SergeyMiracle\Uploadable\Exceptions\FileException;

class UploadableFileHandler
{
    /**
     * Save file on disk
     *
     * @param $file UploadedFile;
     * @param $directory string
     * @param $filename string
     * @param null $disk
     * @return string
     * @throws FileException
     */
    public static function save(string $directory, UploadedFile $file, string $filename, $disk = null): string
    {
        try {
            $path = Storage::disk($disk ?? config('uploadable.disk'))
                ->putFileAs($directory, $file, $filename);
        } catch (Exception $e) {
            throw new FileException($e->getMessage());
        }

        return $path;
    }

    /**
     * Remove file
     *
     * @param $file string
     * @param null $disk
     * @return bool
     */
    public static function delete(string $file, $disk = null): bool
    {
        return Storage::disk($disk ?? config('uploadable.disk'))->delete($file);
    }
}

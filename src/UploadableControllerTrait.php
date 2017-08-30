<?php

namespace SergeyMiracle\Uploadable;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait UploadableControllerTrait
{
    use UtilsTrait;

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

        $path = config('uploadable.root') . $path;

        if (config('uploadable.images.optimize') && getimagesize(public_path($path))) {
            $this->perfomOptimize(public_path($path));
        }

        return $path;
    }

    /**
     * Save image on disk and crop
     *
     * @param $file \Illuminate\Http\UploadedFile;
     * @param int $width
     * @param int $height
     * @return string
     */
    private function moveImage($file, $width = 250, $height = 250)
    {
        $path = $this->moveFile($file);
        \Image::make(public_path($path))->fit($width, $height)->save();

        return $path;
    }

    /**
     * Remove file
     *
     * @param $file
     */
    private function deleteIfExists($file)
    {
        if (file_exists($file) && is_file($file)) {
            unlink($file);
        }
    }
}

<?php

namespace SergeyMiracle\Uploadable;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

trait UploadableControllerTrait
{
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
     * @return string
     */
    private function getUploadDir(): string
    {
        $date = new Carbon();
        return $this->upload_dir . DIRECTORY_SEPARATOR . $date->year . DIRECTORY_SEPARATOR . $date->month;
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
}
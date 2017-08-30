<?php

namespace SergeyMiracle\Uploadable;

trait UtilsTrait
{
    private function perfomOptimize($path)
    {
        [$width, $height] = getimagesize($path);

        if ($height > config('uploadable.images.max_height')) {
            \Image::make($path)->resize(null, config('uploadable.images.max_height'), function ($constraint) {
                $constraint->aspectRatio();
            })->save();
        }

        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($path);
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
     * @param $file string
     * @return string
     */
    private function createFileName($file)
    {
        $path = pathinfo($file);

        return uniqid() . '_' . str_slug($path['filename'], '_') . '.' . $path['extension'];
    }
}

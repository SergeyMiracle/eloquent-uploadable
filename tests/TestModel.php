<?php

namespace SergeyMiracle\Uploadable\Tests;

use Illuminate\Database\Eloquent\Model;
use SergeyMiracle\Uploadable\UploadableModelTrait;

/**
 * Class TestModel
 * @package SergeyMiracle\Uploadable\Tests
 */
class TestModel extends Model
{
    use UploadableModelTrait;

    protected $table = 'test_models';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $uploadables = [
        'image'
    ];

    protected $upload_dir = '/';
}

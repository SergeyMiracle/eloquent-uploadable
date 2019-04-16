<?php

namespace SergeyMiracle\Uploadable\Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadableTest extends TestCase
{
    public function test_model_can_upload_end_delete_file(): void
    {
        $this->app['router']->addRoute(
            'POST',
            '/sergeymiracle/test/upload',
            function () {
                $model = new TestModel();
                $model->title = uniqid('test', true) . '_test';
                $model->save();
            }
        );

        $response = $this->json('POST', '/sergeymiracle/test/upload', [
            'title' => 'Some Title',
            'image' => UploadedFile::fake()->image('photo_test.jpg')
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $model = TestModel::find(1);
        $this->assertNotEmpty($model->image);
        Storage::disk('local')->assertExists($model->image);

        $model->delete();

        Storage::disk('local')->assertMissing($model->image);
    }
}

<?php

namespace Mohsenbostan\LaravelSecretImage;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use \Illuminate\Http\UploadedFile;

class LaravelSecretImage
{
    /**
     * Save Single Image
     * @param UploadedFile $image
     * @param string $path
     * @param null $newImageName
     * @return false|string
     */
    public static function saveSingleImage(UploadedFile $image, $path = 'secret-image', $newImageName = null): string
    {
        if (Str::substr($path, 0, 6) !== 'public') {

            $storage_driver = config('laravel-secret-image.storage_driver');

            return is_null($newImageName)
                ? Storage::disk($storage_driver)->putFile($path, $image)
                : Storage::disk($storage_driver)->putFileAs($path, $image, $newImageName . '.' . $image->getClientOriginalExtension());
        }

        throw new HttpException(422, 'the provided path is not secret. Please remove the `public` from the beginning.');
    }

    /**
     * Save Multiple Images
     * @param $images
     * @param string $path
     * @return array
     */
    public static function saveMultiImages($images, $path = 'secret-image'): array
    {
        if (Str::substr($path, 0, 6) !== 'public') {

            $storage_driver = config('laravel-secret-image.storage_driver');
            $key = 0;
            $images_path = [];
            foreach ($images as $image) {
                $images_path[$key++] = Storage::disk($storage_driver)->putFile($path, $image);
            }
            return $images_path;
        }

        throw new HttpException(422, 'the provided path is not secret. Please remove the `public` from the beginning.');
    }

    /**
     * Get Image Url
     * @param $image
     * @return string
     */
    public static function getImageUrl($image): string
    {
        return route('secret-image.show-image') . "?image=$image";
    }
}

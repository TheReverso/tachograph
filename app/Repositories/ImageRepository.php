<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageRepository
{
    /**
     * Upload the image
     * @param $image
     * @return Response
     */


    protected function storeImage($image)
    {

        // Get filename with extension
        $filenameWithExt = $image->getClientOriginalname();

        // Get file path
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);

        //Remove unwatned characters
        $filename = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);
        $filename = preg_replace("/\s+/", '-', $filename);

        // Get the original image extension
        $extension = $image->getClientOriginalExtension();

        // Create unique file name
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;

        // Prepare image to save

        $file = Image::make($image)->encode('jpg');

        // Save original image

        $saveImage = Storage::put("public/images/{$fileNameToStore}", $file->__toString());

        // Save thumbnail

        $thumbnail = $file->resize(200, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg');

        $saveThumbnail = Storage::put("/public/thumbnails/{$fileNameToStore}", $thumbnail->__toString());

        if($saveImage && $saveThumbnail) {
            return [
                'success' => true,
                'thumbnail_path' => $thumbnail->pathinfo(),
                'image_path' => $file->pathinfo()
            ];
        } else {
            return [
                'success' => false
            ];
        }

    }


}

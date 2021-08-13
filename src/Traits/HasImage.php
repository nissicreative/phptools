<?php

namespace Nissi\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait HasImage
{
    /**
     * The URL of the cached image.
     */
    public function imgSrc($size = 'full', $default = null)
    {
        $attribute = $this->getImagePathAttributeName();

        if (! $this->{$attribute}) {
            return $default;
        }

        return sprintf(
            '/%s/%s/%s',
            config('imagecache.route', 'images/cache'),
            $size,
            basename($this->{$attribute})
        );
    }

    /**
     * Move uploaded image file to appropriate path and update the object.
     */
    public function storeImage(UploadedFile $file)
    {
        $attribute = $this->getImagePathAttributeName();
        $path = $file->store($this->getImageDirectory());

        $this->update([
            $attribute => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    /**
     * The name of the directory in which to store the image.
     */
    public function getImageDirectory()
    {
        return $this->imageDirectory ?: Str::slug($this->getTable());
    }

    /**
     * The name of the attribute used to store the image path.
     */
    public function getImagePathAttributeName()
    {
        return $this->imagePathAttributeName ?: 'image_path';
    }
}

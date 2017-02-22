<?php

namespace Backend\Modules\MediaGalleries\Domain\MediaGallery\Command;

use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGallery;
use Backend\Modules\MediaGalleries\Domain\MediaGallery\MediaGalleryDataTransferObject;

final class UpdateMediaGallery extends MediaGalleryDataTransferObject
{
    /**
     * UpdateMediaGallery constructor.
     *
     * @param MediaGallery $mediaGallery
     */
    public function __construct(MediaGallery $mediaGallery)
    {
        parent::__construct($mediaGallery);
    }
}

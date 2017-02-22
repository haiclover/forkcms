<?php

namespace Frontend\Modules\MediaLibrary\Component;

use Backend\Modules\MediaLibrary\Component\ImageSettings;

/**
 * Frontend Resolution
 * We use this component to create a image thumbnail to be used in the frontend.
 */
class FrontendResolution
{
    // Methods to edit the file
    const METHOD_CROP = 'crop';
    const METHOD_RESIZE = 'resize';

    /**
     * @var string
     */
    protected $customKey;

    /**
     * @var ImageSettings
     */
    protected $imageSettings;

    /**
     * Construct
     *
     * @param string $customKey
     * @param ImageSettings $imageSettings
     */
    private function __construct(
        $customKey,
        ImageSettings $imageSettings
    ) {
        $this->setCustomKey($customKey);
        $this->imageSettings = $imageSettings;
    }

    /**
     * Create
     * 
     * @param $customKey
     * @param ImageSettings $imageSettings
     * @return FrontendResolution
     */
    public static function create(
        $customKey,
        ImageSettings $imageSettings
    ) {
        return new self(
            $customKey,
            $imageSettings
        );
    }

    /**
     * Gets the value of customKey.
     *
     * @return string
     */
    public function getCustomKey()
    {
        return $this->customKey;
    }

    /**
     * Gets the thumbnail settings
     *
     * @return ImageSettings
     */
    public function getImageSettings()
    {
        return $this->imageSettings;
    }

    /**
     * Set custom key
     *
     * @param $customKey
     * @return FrontendResolution
     * @throws \Exception
     */
    protected function setCustomKey($customKey)
    {
        $customKey = (string) $customKey;

        // We have found spaces in the custom key
        if (preg_match('/\s/', $customKey) > 0) {
            throw new \Exception('Your frontend.resolution customKey must not contain spaces.');
        }

        $this->customKey = $customKey;
        return $this;
    }
}

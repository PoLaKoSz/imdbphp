<?php

namespace Imdb;

class Image
{
    /**
     * @var string
     */
    private static $baseURL = 'https://m.media-amazon.com/images/M/';

    /**
     * Absolute URL.
     *
     * @var ?string
     */
    private $original;

    /**
     * var string
     */
    private $id;

    private function __construct()
    {
    }

    /**
     * Initialize a new instance from an image ID.
     *
     * @return Imdb\Image
     */
    public static function fromID(string $id) : Image
    {
        $image = new Image();
        $image->id = $id;
        return $image;
    }

    /**
     * Initialize a new instance from an absolute image URL.
     *
     * @return Imdb\Image
     * @throws \ParseError
     */
    public static function fromURL(string $url) : Image
    {
        $image = new Image();
        $image->original = $url;
        $image->id = static::extractIdFrom($url);
        return $image;
    }

    /**
     * Gets the image's ID.
     */
    public function ID() : string
    {
        return $this->id;
    }

    /**
     * Gets the original URL.
     *
     * @return ?string
     */
    public function originalURL() : ?string
    {
        return $this->original;
    }

    /**
     * Gets an URL for this image which has the
     * specified width (keeping aspect ratio).
     *
     * @param  int    $value Output image width.
     * @return string        Absolute URL.
     */
    public function withWidth(int $value) : string
    {
        return static::$baseURL . $this->id . "@._V1_FMjpg_UX$value.jpg";
    }

    /**
     * Gets an URL for this image which has the
     * specified height (keeping aspect ratio).
     *
     * @param  int    $value Output image height.
     * @return string        Absolute URL.
     */
    public function withHeight(int $value) : string
    {
        return static::$baseURL . $this->id . "@._V1_FMjpg_UY$value.jpg";
    }

    /**
     * Gets an URL for an image with the given width and height.
     *
     * @param  int    $width
     * @param  int    $height
     * @return string         Absolute URL.
     */
    public function asExact(int $width, int $height) : string
    {
        return static::$baseURL . $this->id . '@._V1_UX' . $width . '_CR0,0,' . $width . ',' . $height . '_AL_.jpg';
    }

    /**
     * Gets the unresized version of this image.
     *
     * @return string Absolute URL.
     */
    public function asFullSized() : string
    {
        return static::$baseURL . $this->id . '@@._V1_.jpg';
    }

    /**
     * Gets the 182px * 268px version of this image.
     *
     * @return string Absolute URL.
     */
    public function asThumbnail() : string
    {
        return $this->asExact(182, 268);
    }

    private static function extractIdFrom(string $url) : string
    {
        if (!preg_match('~' . static::$baseURL . '(\w+)(@|@@)._V1_~', $url, $matches))
            throw new \ParseError("Image has an invalid format [$url]");

        return $matches[1];
    }
}

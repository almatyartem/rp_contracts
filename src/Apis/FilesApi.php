<?php

namespace ApiSdk;

use ApiSdk\Contracts\RequestProvider;

class FilesApi
{
    /**
     * @var RequestProvider
     */
    public $provider;

    public $api;

    /**
     * CoreApi constructor.
     * @param RequestProvider $provider
     * @param null $api
     */
    public function __construct(RequestProvider $provider, $api = null)
    {
        $this->provider = $provider;
        $this->api = $api ?? 'files';
    }

    /**
     * @param $image
     * @return string|null
     */
    public function uploadImage($image, $dir = null) : ?string
    {
        $result = $this->provider->request($this->api , 'post','images/add',  ['image' => $image, 'dir' => $dir]);

        return $result['code'] ?? null;
    }

    /**
     * @param $image
     * @param null $width
     * @param null $height
     * @return string|null
     */
    public function getImageUrl($image, $width = null, $height = null) : ?string
    {
        $add = [];

        if($width)
        {
            $add[] = 'width='.$width;
        }
        if($height)
        {
            $add[] = 'height='.$height;
        }
        return env('IMAGES_URL') . '/' . $image.($add ? '?'.implode('&', $add) : '');
    }

    /**
     * @param $url
     * @param null $dir
     * @param null $stopPhrases
     * @return string|null
     */
    public function saveImageByUrl($url, $dir = null, $stopPhrases = null) : ?string
    {
        $image = file_get_contents($url);

        if($stopPhrases and array_search($stopPhrases, $http_response_header))
        {
            return null;
        }
        else
        {
            $ext = pathinfo($url, PATHINFO_EXTENSION);

            $result = $this->provider->request($this->api ,'post','images/add',  ['image' => base64_encode($image), 'ext' => $ext, 'dir' => $dir]);

            return $result['code'] ?? null;
        }
    }
}

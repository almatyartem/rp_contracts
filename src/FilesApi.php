<?php

namespace ApiSdk;

class FilesApi
{
    /**
     * @var GatewayApi
     */
    public $gatewayApi;

    /**
     * @var string
     */
    protected $filesAppCode = 'files';

    /**
     * CoreApi constructor.
     * @param GatewayApi $gatewayApi
     */
    public function __construct(GatewayApi $gatewayApi)
    {
        $this->gatewayApi = $gatewayApi;
    }

    /**
     * @param $image
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadImage($image) : ?string
    {
        $response = $this->gatewayApi->request($this->filesAppCode , 'post','images/add',  ['image' => $image]);

        $result = $this->gatewayApi->getData($response);

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
}

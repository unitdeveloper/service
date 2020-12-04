<?php


namespace zetsoft\service\reacts;


use zetsoft\system\kernels\ZFrame;
use Spatie\Url\Url;

class SpatieUrl extends ZFrame
{
    public function retrieve_part()
    {
        //  echo 'success';

        $url = Url\fromString('https://spatie.be/opensource');

        echo $url->getScheme(); // 'https'
        echo $url->getHost(); // 'spatie.be'
        echo $url->getPath();
    }

    public function transform()
    {
        $url = Url::fromString('https://spatie.be/opensource');

        echo $url->withHost('github.com')->withPath('spatie');
    }

    public function retrieve_transform()
    {
        $url = Url::fromString('https://spatie.be/opensource?utm_source=github&utm_campaign=packages');

        echo $url->getQuery(); // 'utm_source=github&utm_campaign=packages'
        echo $url->getQueryParameter('utm_source'); // 'github'
        echo $url->withoutQueryParameter('utm_campaign'); // 'https://spatie.be/opensource?utm_source=github'
    }
    public function retrieve()
    {
        $url = Url::fromString('https://spatie.be/opensource/laravel');

        echo $url->getSegment(1); // 'opensource'
        echo $url->getSegment(2); // 'laravel'
    }
}

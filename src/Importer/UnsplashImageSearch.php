<?php

namespace App\Importer;

class UnsplashImageSearch
{
    private string $apiRoot = 'https://source.unsplash.com/1600x900/?';
    private string $theme = 'tech';

    public function __construct(string $theme = null)
    {
        $this->theme = $theme ?? $this->theme;
    }

    public function getImage()
    {
        $url = $this->apiRoot . urlencode($this->theme);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_NOBODY, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_close($curl);

        if (isset(curl_getinfo($curl)['url'])){
            return curl_getinfo($curl)['url'];
        }

        return 'https://source.unsplash.com/1600x900/?tech';
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     */
    public function setTheme(string $theme): void
    {
        $this->theme = $theme;
    }
}

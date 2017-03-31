<?php
namespace Nissi\Traits;

use GuzzleHttp\ClientInterface;

trait DisplaysVideo
{
    /*
    |--------------------------------------------------------------------------
    | YouTube
    |--------------------------------------------------------------------------
     */

    /**
     * Extract a YouTube ID from a URL.
     */
    public function getYouTubeId($url)
    {
        $videoId = null;

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $videoId = $match[1];
        }

        return $videoId;
    }

    /**
     * Get the URL of a YouTube thumbnail image from a URL.
     */
    public function getYouTubeThumbnailUrl($url, $version = '0', $scheme = '')
    {
        // versions: 0, 1, 2, 3, 4, default, hqdefault, mqdefault, sddefault, maxresdefault
        return $scheme . '//i.ytimg.com/vi/' . $this->getYouTubeId($url) . "/$version.jpg";
    }

    /**
     * Get the embed URL for a YouTube video.
     */
    public function getYouTubeEmbedUrl($url, $scheme = '')
    {
        return $scheme . '//www.youtube.com/embed/' . $this->getYouTubeId($url);
    }

    /*
    |--------------------------------------------------------------------------
    | Vimeo
    |--------------------------------------------------------------------------
     */

    /**
     * Extract the Vimeo ID from a URL
     */
    public function getVimeoId($url)
    {
        if ( ! preg_match('%(?:player\.)?vimeo\.com/(?:[a-z]*/)*([0-9]{6,11})%i', $url, $match)) {
            return;
        }

        return $match[1];
    }

    /**
     * Retrieve JSON from the Vimeo oEmbed service.
     */
    public function getVimeoJson($videoUrl)
    {
        $client = app(ClientInterface::class);

        $response = $client->request('GET', 'https://vimeo.com/api/oembed.json', [
            'query' => ['url' => $videoUrl]
        ]);

        return (string) $response->getBody();
    }

    /**
     * Calculate a formatted URL suitable for using as an embedded video.
     */
    public function getVimeoEmbedUrl($url, $opts = null, $scheme = '')
    {
        $qs = (is_array($opts)) ? http_build_query($opts) : rtrim($opts, '?');

        if ( ! $videoId = $this->getVimeoId($url)) {
            return;
        }

        return $scheme . '//player.vimeo.com/video/' . $videoId . '?' . $qs;
    }

    /**
     * Get the URL of a video's thumbnail image.
     */
    public function getVimeoThumbnailUrl($videoUrl)
    {
        $json = $this->getVimeoJson($videoUrl);
        $res  = json_decode($json);

        return $res->thumbnail_url;
    }

}

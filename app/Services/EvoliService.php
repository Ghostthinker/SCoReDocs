<?php

namespace App\Services;

use App\Exceptions\EvoliException;
use Cache;
use Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Log;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpClient\HttpClient;

class EvoliService
{
    private static $ACCESS_TOKEN_CACHE_KEY = 'evoli_access_token';

    public function __construct()
    {
        $this->config = config('evoli');
    }

    /**
     * Method fetches the access token to perform uploads
     *
     * @param bool $forceRefresh
     * @return Response The response of the auth request
     * @throws EvoliException
     */
    public function getAccessToken($forceRefresh = false)
    {
        if (Cache::has(EvoliService::$ACCESS_TOKEN_CACHE_KEY) && !$forceRefresh) {
            $accessToken = Cache::get(EvoliService::$ACCESS_TOKEN_CACHE_KEY);

            //alidate token? with token/validate request to evoli?
            return $accessToken['access_token'];
        }

        $url = $this->getAuthEndpoint('oauth/token');

        $response = Http::post($url, [
            'grant_type' => 'client_credentials',
            'client_id' => $this->config['credentials']['client_id'],
            'client_secret' => $this->config['credentials']['client_secret'],
        ]);

        if (!$response->successful() || $response['access_token'] === null) {
            Cache::forget(EvoliService::$ACCESS_TOKEN_CACHE_KEY);
            Log::error('Failed receiving token.', [$response]);
            throw new EvoliException('Failed receiving token from evoli.');
        }
        $accessTokenResponseBody = $response->json();
        $expiresIn = $accessTokenResponseBody['expires_in'];

        //just to be sure, set the lifetime of the token to half of the
        // allowed max lifetime
        $lifeTime = $expiresIn / 2;
        Cache::put(EvoliService::$ACCESS_TOKEN_CACHE_KEY,
            $accessTokenResponseBody, $lifeTime);

        return $accessTokenResponseBody['access_token'];
    }

    /**
     * @param  UploadedFile  $file
     * @param $attributes
     * @return array|mixed
     * @throws EvoliException
     */
    public function transferMedia(UploadedFile $file, $attributes)
    {
        $accessToken = $this->getAccessToken();

        $localFilePath = $fileHandle = fopen($file->getRealPath(), 'r');
        if ($localFilePath === false) {
            Log::error('Can\'t open the local file to send it to evoli gateway.');
            throw new EvoliException('Can\'t open the local file to send it to evoli gateway.');
        }

        $url = $this->getApiEndpoint('upload');
        $request = Http::withToken($accessToken)
            ->withHeaders([
                'Accept' => 'application/json',
                //  'content Type' => 'multipart/form-data',
            ])
            ->attach('attributes', json_encode($attributes['options']))
            ->attach('upload', $fileHandle, 'test');

        if (!empty($attributes['type'])) {
            $request->attach('type', $attributes['type']);
        }

        $response = $request->post($url . '?XDEBUG_SESSION_START=PHPSTORM', [ //?XDEBUG_SESSION_START=PHPSTORM
            'file' => $file,
            'profile' => 'default',
            'realm' => $this->config['realm'],
        ]);

        if (!$response->successful()) {
            Log::error('Evoli error', ['response' => $response]);

            if($response->status() === 415) {
                throw new EvoliException('Unsupported Media Type', 415);
            }

            if (!array_key_exists('previewURL',
                    $response->json()) || !array_key_exists('streamingURL_720p',
                    $response->json())) {
                throw new EvoliException('Evoli error occurred. See evoli logs for more information. Response: '.$response->body());
            }
        }
        return $response->json();
    }

    /**
     * @param  string  $streamingId
     * @return Response
     * @throws EvoliException
     */
    public function getMediaInformation(string $streamingId)
    {
        $response = $this->sendGet($this->getApiEndpoint('upload').'/'.$streamingId);
        if (!$response->successful()) {
            throw new EvoliException('Can not fetch status for media with streamingId: '.$streamingId);
        }
        return $response->json();
    }

    /**
     * @param  string  $path
     * @return string
     */
    public function getEndpoint($path = '')
    {
        return $this->config['host'].'/'.$path;
    }

    /**
     * @param $url
     * @return int
     * @throws EvoliException
     */
    public function create360VideoSequence($url) {
        $response = $this->sendGet($url);
        if (!$response->successful()) {
            throw new EvoliException('Could not create 360° Video Sequence for download');
        }
        return $response->status();
    }

    /**
     * @param $url
     * @return array|mixed
     * @throws EvoliException
     */
    public function getConversionProgress($url) {
        $response = $this->sendGet($url);
        if (!$response->successful()) {
            throw new EvoliException('Could not get progress');
        }
        return $response->json();
    }

    /**
     * @param $url
     * @param $data
     * @return array|Response
     * @throws EvoliException
     */
    public function postDownloadPlaylist($url, $data) {
        $accessToken = $this->getAccessToken();
        $token = $this->getPlaylistDownloadToken();

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url . '/', $data);

        if (!$response->successful() && $response->status() !== 422) {
            throw new EvoliException('Download playlist failed');
        }

        $response = [
            'status' => $response->status(),
            'data' => $response->json()
        ];

        return $response;
    }

    /**
     * @return string
     * @throws EvoliException
     */
    public function getPlaylistDownloadToken() {
        $accessToken = $this->getAccessToken();
        $url = $this->getEndpoint('download/token');
        $response = Http::withToken($accessToken)->get($url);
        return $response->body();
    }

    /**
     * @param $playlist_id
     * @return array|mixed
     * @throws EvoliException
     */
    public function getPlaylistProgress($playlist_id) {
        $accessToken = $this->getAccessToken();
        $url = $this->getApiEndpoint('download/concatenate/'.$playlist_id.'/progress');
        $response = Http::withToken($accessToken)->get($url);
        $response = $response->json();
        $response['downloadUrl'] = $this->getEndpoint('download/concatenate/'.$playlist_id);
        return $response;
    }

    /**
     * @param $playlist_id
     * @return string
     */
    public function getPlaylistDownloadUrl($playlist_id) {
        $url = $this->getEndpoint('download/concatenate/'.$playlist_id);
        return $url;
    }

    /**
     * A small helper got get requests with tokens
     *
     * @param $url
     * @return Response
     */
    protected function sendGet($url): Response
    {
        $accessToken = $this->getAccessToken();

        return Http::withToken($accessToken)
            ->withHeaders([
                'Accept' => 'application/json',
            ])->get($url);
    }

    public function getApiEndpoint($path = '')
    {
        return $this->config['host'].'/'.$this->config['api_endpoint'].'/'.$path;
    }

    private function getAuthEndpoint($path = 'oauth')
    {
        return $this->config['host'].'/'.$path;
    }
}

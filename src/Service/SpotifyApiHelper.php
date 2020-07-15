<?php

namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use GuzzleHttp\Client;

class SpotifyApiHelper
{
    const API_SERVICE_URL = 'https://api.spotify.com/v1/';
    const AUTH_SERVICE_URL = 'https://accounts.spotify.com/';

    private $apiClientId;
    private $apiClientSecret;

    public function __construct(UrlGeneratorInterface $router, $apiClientId, $apiClientSecret, $scope)
    {
        $this->apiClientId = $apiClientId;
        $this->apiClientSecret = $apiClientSecret;
        $this->scope = $scope;
        $this->redirectUri = $router->generate('spotify-api-auth-callback', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getAuthorizeUrl()
    {
        $url = self::AUTH_SERVICE_URL . 'authorize';
        $params = [
            'client_id=' . $this->apiClientId,
            'response_type=code',
            'redirect_uri=' . $this->redirectUri,
            'scope=' . $this->scope,
        ];

        return $url . '?' . implode('&', $params);
    }

    public function getTokenFromAuthorizationCode($code)
    {
        $client = new Client();
        $response = $client->post(
            self::AUTH_SERVICE_URL . 'api/token',
            [
                'auth' => [$this->apiClientId, $this->apiClientSecret],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                ]
            ]
        );

        return json_decode($response->getBody(), true);
    }
}

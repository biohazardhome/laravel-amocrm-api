<?php

use League\OAuth2\Client\Token\AccessToken;
use Illuminate\Support\Facades\Storage;

define('TOKEN_FILE', 'token_info.json');

/**
 * @param array $accessToken
 */
function saveToken($accessToken)
{
    if (
        isset($accessToken)
        && isset($accessToken['accessToken'])
        && isset($accessToken['refreshToken'])
        && isset($accessToken['expires'])
        && isset($accessToken['baseDomain'])
    ) {
        $data = [
            'accessToken' => $accessToken['accessToken'],
            'expires' => $accessToken['expires'],
            'refreshToken' => $accessToken['refreshToken'],
            'baseDomain' => $accessToken['baseDomain'],
        ];

        $storage = Storage::disk('local');
        $storage->put(TOKEN_FILE, json_encode($data));
    } else {
        exit('Invalid access token ' . var_export($accessToken, true));
    }
}

/**
 * @return AccessToken
 */
function getToken()
{
    $storage = Storage::disk('local');

    if ($storage->missing(TOKEN_FILE)) {
        exit('Access token file not found');
    }

    $accessToken = json_decode($storage->get(TOKEN_FILE), true);

    if (
        isset($accessToken)
        && isset($accessToken['accessToken'])
        && isset($accessToken['refreshToken'])
        && isset($accessToken['expires'])
        && isset($accessToken['baseDomain'])
    ) {
        return new AccessToken([
            'access_token' => $accessToken['accessToken'],
            'refresh_token' => $accessToken['refreshToken'],
            'expires' => $accessToken['expires'],
            'baseDomain' => $accessToken['baseDomain'],
        ]);
    } else {
        exit('Invalid access token ' . var_export($accessToken, true));
    }
}

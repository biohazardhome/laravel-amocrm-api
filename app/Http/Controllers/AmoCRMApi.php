<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Biohazard\AmoCRMApi\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Illuminate\Support\Facades\Storage;

class AmoCRMApi extends Controller
{
    
    public function index() {

    }

    public function getToken(AmoCRMApiClient $amocrm, AccessToken $accessToken) {
        $amocrm->setAccessToken($accessToken)
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) {
                    saveToken([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ]);
                }
            );

        if ($accessToken->hasExpired()) {
            echo 'Истекло время действия токена. Нажмите на кнопку амо и разрешите доступ, затем обновите страницу';

            $state = bin2hex(random_bytes(16));
            echo $amocrm->getOAuthClient()->getOAuthButton([
                'title' => 'Установить интеграцию',
                'compact' => true,
                'class_name' => 'className',
                'color' => 'default',
                'error_callback' => 'handleOauthError',
                'state' => $state,
            ]);

            $tokenFile = file_get_contents('http://f0783886.xsph.ru/tmp/token_info.json');
            
            if ($tokenFile) {
                $accessToken = json_decode($tokenFile, true);
                if (
                    isset($accessToken)
                    && isset($accessToken['accessToken'])
                    && isset($accessToken['refreshToken'])
                    && isset($accessToken['expires'])
                    && isset($accessToken['baseDomain'])
                ) {
                    $accessToken = new AccessToken([
                        'access_token' => $accessToken['accessToken'],
                        'refresh_token' => $accessToken['refreshToken'],
                        'expires' => $accessToken['expires'],
                        'baseDomain' => $accessToken['baseDomain'],
                    ]);

                    if (!$accessToken->hasExpired()) {
                        $storage = Storage::disk('local');
                        $storage->delete(AmoCRMApiClient::TOKEN_FILE);
                        $storage->put(AmoCRMApiClient::TOKEN_FILE, $tokenFile);
                    }
                } else {
                    exit('Invalid access token ' . var_export($accessToken, true));
                }
            }
        }

        // return redirect()->back();
    }

    public function getToken2(AmoCRMApiClient $amocrm, string $code) {
        try {
            if (!empty($code)) {
                $amocrm->setAccountBaseDomain(env('AMOCRM_SUBDOMAIN'));
                $accessToken = $amocrm->getOAuthClient()->getAccessTokenByCode($code);
                
                if (!$accessToken->hasExpired()) {
                    $amocrm->saveTokenFile([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $amocrm->getAccountBaseDomain(),
                    ]);

                    echo 'Успешно сохранен файл token_info.json';
                } else {
                    echo 'Время действия токена истекло, попробуйте еще раз';
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

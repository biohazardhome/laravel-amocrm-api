<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Illuminate\Support\Facades\Storage;

class AmoCRMApi extends Controller
{
    
    public function index() {

    }

    public function getToken() {
        $clientId = $_ENV['AMOCRM_CLIENT_ID'];
        $clientSecret = $_ENV['AMOCRM_CLIENT_SECRET'];
        $redirectUri = $_ENV['AMOCRM_CLIENT_REDIRECT_URI'];
        $subdomain = $_ENV['AMOCRM_CLIENT_SUBDOMAIN'];

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);
        $apiClient->setAccountBaseDomain($subdomain);
        $accessToken = getToken();

        $apiClient->setAccessToken($accessToken)
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
            echo $apiClient->getOAuthClient()->getOAuthButton([
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
                        $storage->delete(TOKEN_FILE);
                        $storage->put(TOKEN_FILE, $tokenFile);
                    }
                } else {
                    exit('Invalid access token ' . var_export($accessToken, true));
                }
            }
        }

        return redirect()->back();
    }

    public function getToken2() {
        $clientId = $_ENV['AMOCRM_CLIENT_ID'];
        $clientSecret = $_ENV['AMOCRM_CLIENT_SECRET'];
        $redirectUri = $_ENV['AMOCRM_CLIENT_REDIRECT_URI'];
        $subdomain = $_ENV['AMOCRM_CLIENT_SUBDOMAIN'];

        $apiClient = new AmoCRMApiClient($clientId, $clientSecret, $redirectUri);

        try {
            $code = request()->get('code');
            if (!empty($code)) {
                $apiClient->setAccountBaseDomain(request()->get('referer'));
                $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($code);

                if (!$accessToken->hasExpired()) {
                    saveToken([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $apiClient->getAccountBaseDomain(),
                    ]);
                }

                echo 'Успешно сохранен файл token_info.json';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}

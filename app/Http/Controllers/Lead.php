<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\AccountModel;

use App\Models\Lead as LeadModel;
use App\Models\Pipeline;
use App\Models\Account;

class Lead extends Controller
{
    
    public function index() {
        $leads = LeadModel::with('account', 'pipeline')
            ->paginate(20);

        return view('index', compact('leads'));
    }

    public function fetch() {        
        $apiClient = $this->initAmoCRMApi();

        try {
            $account = $apiClient->account()->getCurrent(AccountModel::getAvailableWith());

            $leads = $apiClient->leads();
            if ($leads) {
                $leadsCollection = $leads->get();
                foreach ($leadsCollection as $lead) {

                    if (!LeadModel::findByLeadId($lead->id)) {
                        LeadModel::firstOrCreate([
                            'lead_id' => $lead->id,
                            'name' => $lead->name,
                            'group_id' => $lead->groupId,
                            'account_id' => $lead->accountId,
                            'pipeline_id' => $lead->pipelineId,
                            'status_id' => $lead->statusId,
                            'company_id' => $lead->company->id,
                            'price' => $lead->price,
                        ]);
                    }

                    $account = $apiClient->account()->getCurrent([30898054]);
                    if ($account) {
                        dump($account);
                        if (!Account::findByAccountId($account->id)) {
                            Account::create([
                                'account_id' => $account->id,
                                'name' => $account->name,
                                'subdomain' => $account->subdomain,
                                'country' => $account->country,
                                'currency' => $account->currency,
                            ]);
                        }
                    }

                    $pipeline = $apiClient->pipelines()->getOne($lead->pipelineId);
                    if ($pipeline) {
                        if (!Pipeline::findByPipelineId($pipeline->id)) {
                            Pipeline::create([
                                'pipeline_id' => $pipeline->id,
                                'name' => $pipeline->name,
                                'sort' => $pipeline->sort,
                                'account_id' => $pipeline->accountId,
                                'is_main' => $pipeline->isMain,
                                'is_unsorted_on' => $pipeline->isUnsortedOn,
                                'is_archive' => $pipeline->isArchive,
                            ]);
                        }
                    }

                }
            }
        } catch (AmoCRMApiException $e) {
            echo $e->getMessage();
        }
    }

    protected function initAmoCRMApi() {
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
                    saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                }
            );

        return $apiClient;
    }

}

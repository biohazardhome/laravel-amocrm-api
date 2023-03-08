<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Biohazard\AmoCRMApi\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Exceptions\AmoCRMApiException;
use League\OAuth2\Client\Token\AccessToken;

use App\Models\Lead as LeadModel;
use App\Models\Pipeline;
use App\Models\Account;

class Lead extends Controller
{
    
    public function index() {
        $leads = LeadModel::with('account', 'pipeline')
            ->paginate(20);

        return view('lead', compact('leads'));
    }

    public function fetch(AmoCRMApiClient $amocrm, AccessToken $accessToken) {        
        
        if ($accessToken->hasExpired()) {
            return redirect()->route('amocrm-api.get-token');
        }

        try {
            $leads = $amocrm->leads();
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

                        echo 'Сделка id: '. $lead->id .' успешно добавлена '. $lead->name;
                        echo '<br>';
                    }

                    $account = $amocrm->account()->getCurrent([$lead->accountId]);
                    if ($account) {
                        if (!Account::findByAccountId($account->id)) {
                            Account::create([
                                'account_id' => $account->id,
                                'name' => $account->name,
                                'subdomain' => $account->subdomain,
                                'country' => $account->country,
                                'currency' => $account->currency,
                            ]);

                            echo 'Аккаунт id: '. $account->id .' успешно добавлен '. $account->name;
                            echo '<br>';
                        }
                    }

                    $pipeline = $amocrm->pipelines()->getOne($lead->pipelineId);
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

                            echo 'Канал id: '. $pipeline->id .' успешно добавлен '. $pipeline->name;
                            echo '<br>';
                        }
                    }

                }
            }
        } catch (AmoCRMApiException $e) {
            echo $e->getMessage();
        }
    }

}

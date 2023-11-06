<?php

namespace App\Http\Controllers;

use App\Api\Ssev\EDeliveryAuth;
use App\Api\Ssev\EDeliveryService;
use App\Models\PdoiResponseSubject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SsevController extends Controller
{
    public static function getSsevProfile($user, $identityType = '', $identityNumber = ''): int
    {
        $eDeliveryConfig = config('e_delivery');
        if( !$user->ssev_profile_id ) {
            $eDeliveryAuth = new EDeliveryAuth('/ed2*');
            $token = $eDeliveryAuth->getToken();

            if( is_null($token) ) {
                return 0;
            }

            $eDeliveryService = new EDeliveryService($token, 'ed2');

            $identity = [];
            if( (!empty($identityType) && $identityType == User::USER_TYPE_COMPANY) || !empty($user->company_identity) ) {
                $identity[] = [
                    'groupId' => $eDeliveryConfig['group_ids']['company'],
                    'identity' => !empty($identityNumber) ? $identityNumber : $user->eik,
                ];
            }
            if( (!empty($identityType) && $identityType == User::USER_TYPE_PERSON) || !empty($user->person_identity) ) {
                $identity[] = [
                    'groupId' => $eDeliveryConfig['group_ids']['person'],
                    'identity' => !empty($identityNumber) ? $identityNumber : $user->identity,
                ];
            }

            if( !sizeof($identity) ) {
                $user->ssev_profile_id = null;
                $user->save();
                return 0;
            }

            $recipientProfile = 0;
            foreach ($identity as $identityType){
                if( $recipientProfile ) {
                    continue;
                }
                $profileResponse = $eDeliveryService->getProfileData([
                    'groupId' => $identityType['groupId'],
                    'identity' => $identityType['identity'],
                ]);

                if( is_array($profileResponse) && isset($profileResponse['error']) ) {
                    Log::error('Get SSEV (eDelivery) profile info request error: '. $profileResponse['message']);
                    continue;
                }
                $profile = json_decode($profileResponse, true);
                if( !$profile || !isset($profile['profileId']) ) {
                    Log::error('Get SSEV (eDelivery) profile info response error: '. $profileResponse);
                    continue;
                }
                $recipientProfile = $profile['profileId'];
            }

            if( $recipientProfile ) {
                $user->ssev_profile_id = $recipientProfile;
                $user->save();
            } else{
                $user->ssev_profile_id = null;
                $user->save();
            }
        }

        return (int)$user->ssev_profile_id;
    }

    public static function getEgovProfile($pdoiSubjectId, $identityNumber = ''): int
    {
        $pdoiSubject = PdoiResponseSubject::find((int)$pdoiSubjectId);
        if( !$pdoiSubject) {
            return  0;
        }

        $eDeliveryConfig = config('e_delivery');
        if( !$pdoiSubject->ssev_profile_id ) {
            $eDeliveryAuth = new EDeliveryAuth('/ed2*');
            $token = $eDeliveryAuth->getToken();

            if( is_null($token) ) {
                return 0;
            }

            $eDeliveryService = new EDeliveryService($token, 'ed2');

            $identity = [];
            $identity[] = [
                'groupId' => $eDeliveryConfig['group_ids']['egov'],
                'identity' => !empty($identityNumber) ? $identityNumber : $pdoiSubject->eik,
            ];

            if( !sizeof($identity) ) {
                $pdoiSubject->ssev_profile_id = null;
                $pdoiSubject->save();
                return 0;
            }

            $recipientProfile = 0;
            foreach ($identity as $identityType){
                if( $recipientProfile ) {
                    continue;
                }
                $profileResponse = $eDeliveryService->getProfileData([
                    'groupId' => $identityType['groupId'],
                    'identity' => $identityType['identity'],
                ]);

                if( is_array($profileResponse) && isset($profileResponse['error']) ) {
                    Log::error('Get SSEV (eDelivery) profile info request error: '. $profileResponse['message']);
                    continue;
                }
                $profile = json_decode($profileResponse, true);
                if( !$profile || !isset($profile['profileId']) ) {
                    Log::error('Get SSEV (eDelivery) profile info response error: '. $profileResponse);
                    continue;
                }
                $recipientProfile = $profile['profileId'];
            }

            if( $recipientProfile ) {
                $pdoiSubject->ssev_profile_id = $recipientProfile;
                $pdoiSubject->save();
            } else{
                $pdoiSubject->ssev_profile_id = null;
                $pdoiSubject->save();
            }
        }

        return (int)$pdoiSubject->ssev_profile_id;
    }
}

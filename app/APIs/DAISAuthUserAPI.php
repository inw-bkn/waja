<?php

namespace App\APIs;

use Illuminate\Support\Facades\Http;
use App\Contracts\AuthUserAPI;

class DAISAuthUserAPI implements AuthUserAPI
{
    public function getUser($login)
    {
        $response = Http::withHeaders([
                            'APPNAME' => env('DAIS_USER_APPNAME'),
                            'APIKEY' => env('DAIS_USER_APIKEY'),
                        ])->post(env('DAIS_USER_URL'), ['id' => $login]);

        return $response->json();
    }    

    public function authenticate($login = 'john.doe', $password = 'p@ssw0rd')
    {
        $response = Http::withHeaders([
                            'APPNAME' => env('DAIS_AUTH_APPNAME'),
                            'APIKEY' => env('DAIS_AUTH_APIKEY'),
                        ])->post(env('DAIS_AUTH_URL'), ['name' => $login, 'pwd' => $password]);
        return $response->json();
        /*
[
     "found" => true,
     "msg" => "Success",
     "UserInfo" => [
       "UserData" => [
         "sapid" => "10022569",
         "username" => "koramit.pic",
         "full_name" => "นาย กรมิษฐ์ พิชนาหะรี",
         "position" => "นักวิชาการคอมพิวเตอร์",
         "job" => "นักวิชาการคอมพิวเตอร์",
         "office" => "สาขาวิชาวักกะวิทยา",
         "department" => "ภ.อายุรศาสตร์",
         "passwordExpiredDate" => "09/12/2020 00:00:00",
         "daysLeft" => 82,
         "passwordNeverExpire" => false,
         "eng_name" => "Mr. KORAMIT PICHANAHAREE",
         "email" => "koramit.pic@mahidol.ac.th",
         "sn" => "PICHANAHAREE",
         "givenName" => "KORAMIT",
         "ipPhone" => "",
         "pager" => "",
       ],
       "UserToken" => [
         "token" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6ImtvcmFtaXQucGljIiwiaHR0cDovL3NjaGVtYXMueG1sc29hcC5vcmcvd3MvMjAwNS8wNS9pZGVudGl0eS9jbGFpbXMvc3lzdGVtIjoiU0lJVF9NRURTSUNPTiIsInJvbGUiOiIiLCJuYmYiOjE2MDA0MDk2NDMsImV4cCI6MTYwMDQyMDQ0MywiaWF0IjoxNjAwNDA5NjQzfQ.lUKWZ4CkkeOHwE_VixLkejXVDk283kScYcRMBSlVtUY",
         "token_expire_date" => "1600420443",
       ],
       "siRoles" => null,
     ],
   ]
        */

        return [
            'reply_code' => 0,
            'reply_text' => 'granted.',
            'name' => $data['UserInfo']['UserData']['full_name'],
            'username' => $data['UserInfo']['UserData']['username'],
            'email' => $data['UserInfo']['UserData']['email'],
            'org_id' => $data['UserInfo']['UserData']['sapid'],
            'remark' => $data['UserInfo']['UserData']['job'] . " " . $data['UserInfo']['UserData']['office'] . " " . $data['UserInfo']['UserData']['department'],
            'tel_no' => $data['UserInfo']['UserData']['ipPhone'],
            'active' => 1,
            'name_en' => $data['UserInfo']['UserData']['eng_name'],
            'document_id' => "",
            'org_division_id' => null,
            'org_position_id' => null,
            'org_division_name' => $data['UserInfo']['UserData']['department'],
            'org_position_title' => $data['UserInfo']['UserData']['job'],
            'password_days_left' => $data['UserInfo']['UserData']['daysLeft']
        ];
    }

    protected function getUserByOrgId($orgId)
    {
        $functionname = env('SIMHIS_AUTH_FUNCNAME');
        $action = "http://tempuri.org/" . $functionname;

        $strSOAP  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
        $strSOAP .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">";
        $strSOAP .= "<soap:Body>";
        $strSOAP .= "<" . $functionname . " xmlns=\"http://tempuri.org/\">";
        $strSOAP .= "<Userid>" . $orgId . "</Userid>";
        $strSOAP .= "<Password>password</Password>";
        $strSOAP .= "<SystemID>1</SystemID>";
        $strSOAP .= "</" . $functionname . ">";
        $strSOAP .= "</soap:Body>";
        $strSOAP .= "</soap:Envelope>";

        // make request and check the response.
        if (($response = $this->executeCurl($strSOAP, $action, env('SIMHIS_AUTH_URL'))) === FALSE) {
            return null;
        } 

        $response = str_replace('&#x', '', $response);
        $xml = simplexml_load_string($response);
        $namespaces = $xml->getNamespaces(TRUE);

        $response = $xml->children($namespaces['soap'])
                        ->Body
                        ->children($namespaces[""])
                        ->SiITCheckUserResponse
                        ->SiITCheckUserResult
                        ->children($namespaces['diffgr'])
                        ->diffgram
                        ->children()
                        ->NewDataSet
                        ->children()
                        ->GetUsers
                        ->children();
        return $response;

        foreach ($response as $key => $value) {
            $tmp[$key] = implode("", json_decode(json_encode($value, TRUE), TRUE));
        }
    }

    protected function executeCurl($strSOAP, $action, $url)
    {
        $headers = [
            "Host: " . env('SIMHIS_AUTH_HOST'),
            "Content-Type: text/xml; charset=utf-8",
            "SOAPAction: \"" . $action . "\"",
            "Transfer-Encoding: chunked",
        ];

        // Build the cURL session.
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_VERBOSE, true); // for debug
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); // set connection timeout.
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, env('SIMHIS_SERVER_USERNAME') . ":" . env('SIMHIS_SERVER_PASSWORD'));

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false || strpos($response, '<div id="header"><h1>Server Error</h1></div>') !== false) {
            return false;
        }

        return $response;
    }
    
}

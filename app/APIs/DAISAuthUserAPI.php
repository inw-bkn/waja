<?php

namespace App\APIs;

use Illuminate\Support\Facades\Http;
use App\Contracts\AuthUserAPI;

class DAISAuthUserAPI implements AuthUserAPI
{
    public function getUser($login)
    {
        $headers = [
            'APPNAME' => config('app.DAIS_USER_APPNAME'),
            'APIKEY' => config('app.DAIS_USER_APIKEY'),
        ];
        $response = $this->makePost(config('app.DAIS_USER_URL'), ['id' => $login], $headers);

        if (!$response['ok'] || !$response['found']) {
            return $response;
        }

        $profile = $this->getUserByOrgId($response['UserInfo']['ID']);

        return [
            'ok' => true,
            'found' => true,
            'active' => $response['isActive'],
            'login' => $login,
            'org_id' => $response['UserInfo']['ID'],
            'full_name' => $response['UserInfo']['DisplayName'],
            // 'document_id' => $profile['pid'] ?? null,
            'position_id' => $profile['job_key'] ?? null,
            'position_name' => $profile['job_key_desc'] ?? null,
            'division_id' => $profile['org_unit_m'] ?? null,
            'division_name' => $profile['org_unit_m_desc'] ?? null,
            'password_expires_in_days' => (int) str_replace('Password Remain(Day(s)): ', '', $response['body']),
            'remark' => $profile['remark'] ?? null,
        ];
    }

    public function authenticate($login, $password)
    {
        $headers = [
            'APPNAME' => config('app.DAIS_AUTH_APPNAME'),
            'APIKEY' => config('app.DAIS_AUTH_APIKEY'),
        ];
        $response = $this->makePost(config('app.DAIS_AUTH_URL'), ['name' => $login, 'pwd' => $password], $headers);

        if (!$response['ok'] || !$response['found']) {
            return $response;
        }

        $profile = $this->getUserByOrgId($response['UserInfo']['UserData']['sapid']);

        return [
            'ok' => true, // mean user is active
            'found' => true,
            'login' => $login,
            'org_id' => $response['UserInfo']['UserData']['sapid'],
            'full_name' => $response['UserInfo']['UserData']['full_name'],
            'full_name_en' => $response['UserInfo']['UserData']['eng_name'],
            // 'document_id' => $profile['pid'] ?? null,
            'position_id' => $profile['job_key'] ?? null,
            'position_name' => $profile['job_key_desc'] ?? null,
            'division_id' => $profile['org_unit_m'] ?? null,
            'division_name' => $profile['org_unit_m_desc'] ?? null,
            'department_name' => $response['UserInfo']['UserData']['department'],
            'office_name' => $response['UserInfo']['UserData']['office'],
            'email' => $response['UserInfo']['UserData']['email'],
            'password_expires_in_days' => $response['UserInfo']['UserData']['daysLeft'],
            'remark' => $profile['remark'] ?? null,
        ];
    }

    protected function makePost($url, $data, $headers, $options = ['timeout' => 5.0])
    {
        $response = Http::withOptions($options)
                        ->withHeaders($headers)
                        ->post($url, $data);

        if ($response->successful()) {
            $data = $response->json() + ['ok' => true];
            if (isset($data['msg'])) {
                $data['body'] = $data['msg'];
                unset($data['msg']);
            } else {
                $data['body'] = null;
            }
            return $data;
        }

        return [
            'ok' => false,
            'status' => $response->status(),
            'error' => $response->serverError() ? 'server' : 'client',
            'body' => $response->body(),
        ];
    }

    protected function getUserByOrgId($orgId)
    {
        $functionname = config('app.SIMHIS_AUTH_FUNCNAME');
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
        if (($response = $this->executeCurl($strSOAP, $action, config('app.SIMHIS_AUTH_URL'))) === false) {
            return [
                'ok' => false,
                'status' => 500,
                'error' => 'server',
                'body' => 'Server Error'
            ];
        }

        $response = str_replace('&#x', '', $response);
        $xml = simplexml_load_string($response);
        $namespaces = $xml->getNamespaces(true);

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
        return ((array) $response) + ['ok' => true];
    }

    protected function executeCurl($strSOAP, $action, $url)
    {
        $headers = [
            "Host: " . config('app.SIMHIS_AUTH_HOST'),
            "Content-Type: text/xml; charset=utf-8",
            "SOAPAction: \"" . $action . "\"",
            "Transfer-Encoding: chunked",
        ];

        // Build the cURL session.
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_VERBOSE, true); // for debug
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // set connection timeout.
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strSOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
        curl_setopt($ch, CURLOPT_USERPWD, config('app.SIMHIS_SERVER_USERNAME') . ":" . config('app.SIMHIS_SERVER_PASSWORD'));

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false || strpos($response, '<div id="header"><h1>Server Error</h1></div>') !== false) {
            return false;
        }

        return $response;
    }
}

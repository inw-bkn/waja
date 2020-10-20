<?php
namespace App\APIs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class LINEAuthUserAPI
{
    protected $id;
    protected $name;
    protected $nickname;
    protected $email;
    protected $avatar;
    protected $status;
    
    public static function redirect()
    {
        $url  = 'https://access.line.me/oauth2/v2.1/authorize?response_type=code';
        $url .= '&client_id=' . config('services.line.client_id');
        $url .= '&redirect_uri=' . config('services.line.redirect');
        $url .= '&state=' . csrf_token();
        $url .= '&scope=profile openid email';
        $url .= '&nonce=' . Str::random(10);
        return Redirect::to($url);
    }
    
    public function __construct()
    {
        // access denied
        if (Request::has('error')) {
            Log::error('LINE LOGIN: access denied or error => ' . Request::input('error_description'));
            return;
        }

        // response error
        if (!Request::has('code')) {
            Log::error('LINE LOGIN: Callback response error');
            return;
        }

        // access granted then fetch access token
        $response = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type' => 'authorization_code',
            'code' => Request::input('code'),
            'redirect_uri' => config('services.line.redirect'),
            'client_id' => config('services.line.client_id'),
            'client_secret' => config('services.line.client_secret'),
        ]);

        if (!$response->successful()) {
            Log::error('LINE LOGIN: fetch acces token error => ' . $response->body());
            return;
        }

        $profile = explode('.', $response->json()['id_token'])[1]; // => JWT body
        $profile = json_decode(base64_decode($profile), true);
        $this->name = $profile['name'] ?? null;
        $this->email = $profile['email'] ?? null;
        $this->avatar = $profile['picture'] ?? null;

        // fetch profile for other stuffs
        $response = Http::withToken($response->json()['access_token'])->get('https://api.line.me/v2/profile');

        if (!$response->successful()) {
            Log::error('LINE LOGIN: fetch profile error => ' . $response->body());
            return;
        }

        $profile = $response->json();
        $this->id = $profile['userId'];
        $this->nickname = $profile['displayName'] ?? null;
        $this->status = $profile['statusMessage'] ?? null;
    }

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }

    public function getEmail() { return $this->email; }

    public function getAvatar() { return $this->avatar; }

    public function getNickname() { return $this->nickname; }

    public function getStatus() { return $this->status; }
}

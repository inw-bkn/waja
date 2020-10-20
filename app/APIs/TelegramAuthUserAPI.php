<?php

namespace App\APIs;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class TelegramAuthUserAPI
{
    protected $id = null;
    protected $name = null;
    protected $nickname = null;
    protected $email = null;
    protected $avatar = null;
    
    public static function redirect()
    {
        return Redirect::route('login'); // no login page for telegram so redirect user to waja login page
    }
    
    public function __construct()
    {
        $data = Request::all();
        
        $checkHash = $data['hash'];
        unset($data['hash']);
        
        // compose data to TELEGRAM format => https://core.telegram.org/widgets/login
        $dataCheckArray = [];
        foreach ($data as $key => $value) {
            $dataCheckArray[] = $key . '=' . $value;
        }
        sort($dataCheckArray);
        $dataCheckStr = implode("\n", $dataCheckArray);
        
        $secretKey = hash('sha256', config('services.telegram.client_secret'), true);
        $hash = hash_hmac('sha256', $dataCheckStr, $secretKey);
        
        if (strcmp($hash, $checkHash) !== 0) {
            Log::error('TELEGRAM LOGIN: Data is NOT from Telegram');
            return;
        }
        
        if ((time() - $data['auth_date']) > 86400) {
            Log::error('TELEGRAM LOGIN: Data is outdated');
            return;
        }
        $this->id = $data['id'];
        $this->name = $data['first_name'] ?? '' . $data['last_name'] ?? '';
        $this->nickname = $data['username'] ?? null;
        $this->email = null;
        $this->avatar = $data['photo_url'] ?? null;
    }

    public function getId() { return $this->id; }

    public function getName() { return $this->name; }

    public function getEmail() { return $this->email; }

    public function getAvatar() { return $this->avatar; }

    public function getNickname() { return $this->nickname; }
}

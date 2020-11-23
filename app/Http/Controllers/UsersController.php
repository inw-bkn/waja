<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class UsersController extends Controller
{
    public function profile()
    {
        return Inertia::render('Profile');
    }

    public function linkSocial()
    {
        $userId = Request::input('user_id');
        if ($this->userAlreadyLinkedSocial($userId)) {
            return 'Your organization account already linked with social acount';
        }

        Session::put('linkSocialUserId', $userId);
        return Redirect::route('login');
    }

    public function requestLinkSocial()
    {
        $userId = Request::input('user_id');
        if ($this->userAlreadyLinkedSocial($userId)) {
            return [ 'linked' => true ];
        }
        
        return [
            'linked' => false,
            'signed_url' => URL::temporarySignedRoute('linkSocial', now()->addMinutes(30), [
                'user_id' => $userId
            ])
        ];
    }

    protected function userAlreadyLinkedSocial($slug)
    {
        $user = User::whereSlug($slug)->first();
        return $user && ($user->profile['social'] ?? null);
    }

    public function show($userId)
    {
        \Log::info($userId);
        return User::whereSlug($userId)->first() ?? [];
    }
}

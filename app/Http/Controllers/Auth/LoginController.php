<?php

namespace App\Http\Controllers\Auth;

use App\APIs\LINEAuthUserAPI;
use App\APIs\TelegramAuthUserAPI;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Request::has('redirectAuthenticatedUser')) {
            Session::put('redirectAuthenticatedUser', Request::input('redirectAuthenticatedUser'));
        }
        
        return Inertia::render('Auth/Login', [
            'configs' => [
                'telegram' => config('services.telegram')
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        if ($provider === 'line') {
            return LINEAuthUserAPI::redirect();
        } elseif ($provider === 'telegram') {
            return TelegramAuthUserAPI::redirect();
        } else {
            return Socialite::driver($provider)->redirect();
        }
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        try {
            if ($provider === 'line') {
                $user = new LINEAuthUserAPI();
            } elseif ($provider === 'telegram') {
                $user = new TelegramAuthUserAPI();
            } else {
                $user = Socialite::driver($provider)->user();
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return Redirect::route('login'); // SHOULD return WITH NOTICE USER about ERROR
        }

        $userExist = User::where('profile->social->provider', $provider)
                         ->where('profile->social->id', $user->getId())
                         ->first();

        if ($userExist) {
            // UPDATE USER SOCIAL PROFILE NOT YET IMPLEMENT
            
            if ($redirectTo = Session::pull('redirectAuthenticatedUser')) {
                return Inertia::location($redirectTo . '&userId=' . urlencode($userExist->slug));
            }

            Auth::login($userExist);
            return Redirect::intended('profile');
        }

        Session::put('user_social_profile', [
            'provider' => $provider,
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'avatar' => $user->getAvatar(),
            'nickname' => $user->getNickname(),
        ]);
            
        return Redirect::route('register');
    }
}

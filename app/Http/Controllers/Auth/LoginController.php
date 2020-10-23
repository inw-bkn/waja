<?php

namespace App\Http\Controllers\Auth;

use App\APIs\LINEAuthUserAPI;
use App\APIs\TelegramAuthUserAPI;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if ($request->has('redirectAuthenticatedUser')) {
            Session::put('redirectAuthenticatedUser', $request->input('redirectAuthenticatedUser'));
        }
        
        return Inertia::render('Auth/Login', [
            'configs' => [
                'telegram' => config('services.telegram')
            ]
        ]);
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only($this->username(), 'password');

        // if (!Auth::attempt($credentials)) { // THIS IS ALSO LOGIN USER
        // if ($credentials[$this->username()] !== $credentials['password']) {
        if ($credentials['password'] != '1111') {
        
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            // $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }
        $user = User::where($this->username(), $credentials[$this->username()])->first();

        if (!$user) {
            Session::put($this->username(), $request->input($this->username()));
            return redirect('register');
        }

        if ($redirectTo = Session::pull('redirectAuthenticatedUser')) {
            return redirect($redirectTo . '&userId=' . urlencode($user->slug));
        }

        // Login User
        Auth::login($user);

        // return $this->sendLoginResponse($request);

        return redirect()->intended('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }
    

    protected function username()
    {
        return $this->username ?? 'login';
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        // $request->session()->regenerate();

        // $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended();
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
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
    public function handleProviderCallback($provider, Request $request)
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
            return Redirect::intended('dashboard');
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

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if ($request->has('redirectAfterAuthenticated')) {
            Session::put('redirectAfterAuthenticated', $request->input('redirectAfterAuthenticated'));
        }
        
        return view('login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $request->only($this->username(), 'password');

        // if (!Auth::attempt($credentials)) { // THIS IS ALSO LOGIN USER
        if ($credentials[$this->username()] !== $credentials['password']) {
        
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            // $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        }
        $user = User::where($this->username(), $credentials[$this->username()])->first();

        $redirectTo = Session::pull('redirectAfterAuthenticated');

        if ($redirectTo) {
            return redirect($redirectTo . '&userId=' . urlencode($user->slug));
        }

        // Login User
        Auth::login($user);

        return $this->sendLoginResponse($request);

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
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        if (!($socialProfile = Session::get('user_social_profile'))) {
            return Redirect::route('login');
        }

        return Inertia::render('Auth/Register', ['social_profile' => $socialProfile]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        $this->validator(Request::all())->validate();

        event(new Registered($user = $this->create(Request::all())));

        if ($redirectTo = Session::pull('redirectAuthenticatedUser')) {
            return Inertia::location($redirectTo . '&userId=' . urlencode($user->slug));
        }
        
        Auth::login($user);
        return Redirect::intended('profile');
    }

     /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name' => ['string', 'min:4','max:255', 'unique:users'],
            'email' => ['email'],
            'tel_no' => ['digits_between:9,10'],
        ];

        $messages = [
            'name.min' => 'ชื่อบัญชีผู้ใช้งานต้องยาวตั้งแต่ 4 ตัวอักษรแต่ไม่เกิน 255 ตัวอักษร',
            'name.max' => 'ชื่อบัญชีผู้ใช้งานต้องยาวตั้งแต่ 4 ตัวอักษรแต่ไม่เกิน 255 ตัวอักษร',
            'name.unique' => 'ชื่อบัญชีผู้ใช้งานนี้ถูกใช้งานแล้ว โปรดตั้งชื่อใหม่',
            'email.email' => 'ข้อมูลอีเมลไม่ถูกต้องตามรูปแบบ',
            'tel_no.digits_between' => 'หมายเลขโทรศัพท์ต้องเป็นตัวเลข 9 หรือ 10 ตัวเท่านั้น',
        ];
        
        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $socialProfile = Session::pull('user_social_profile');
        $user = new User();
        $user->slug = Str::uuid()->toString();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make(Str::random());
        if ($socialProfile['email']) {
            $user->email_verified_at = now();
        }
        unset($socialProfile['email']);
        $user->profile = [
            'full_name' => $data['full_name'],
            'tel_no' => $data['tel_no'],
            'social' => $socialProfile
        ];
        $user->save();

        return $user;
    }
}

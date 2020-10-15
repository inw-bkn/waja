<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        if (!($login = Session::pull('login'))) {
            return redirect('login');
        }

        // $login = 'no@no.no';

        return view('register', ['login' => $login]);
    }

    public function register(Request $request)
    {
        $user = new User();
        $user->slug = Str::uuid()->toString();
        $user->login = $request->input('login');
        $user->name = $request->input('name');
        $user->password = Hash::make(Str::random());
        $user->profile = [
            'full_name' => $request->input('full_name'),
            'full_name_en' => $request->input('full_name_en'),
        ];
        $user->save();

        if ($redirectTo = Session::pull('redirectAuthenticatedUser')) {
            return redirect($redirectTo . '&userId=' . urlencode($user->slug));
        }
        return redirect('/');
    }
}

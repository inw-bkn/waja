<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Auth\Events\Registered;

class APIRegisterController extends Controller
{
    protected $rules = [
        'name' => ['required', 'string', 'min:4', 'max:255', 'unique:users'],
        'full_name_eng' => ['required', 'string', 'min:4', 'max:255'],
        'tel_no' => ['required', 'digits_between:9,10']
    ];
    
    public function __invoke()
    {
        $data = Request::all();

        \Log::info($data);
        
        $validator = Validator::make($data, $this->rules);
        if ($validator->fails()) { // should have login to validate officer
            return new JsonResponse(['errors' => $validator->errors()], 422);
        }

        event(new Registered($user = $this->create($data)));

        return new JsonResponse(['user' => $user], 200);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = new User();
        $user->slug = Str::uuid()->toString();
        $user->name = $data['name'];
        $user->password = Hash::make(Str::random());
        $user->profile = [
            'full_name' => $data['full_name'],
            'full_name_eng' => $data['full_name_eng'],
            'tel_no' => $data['tel_no'],
            'organization' => $data['organization']
        ];
        $user->save();

        return $user;
    }
}

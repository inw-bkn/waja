<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class AssignRootController extends Controller
{
    public function __invoke($passcode)
    {
        if (Role::withCount('users')->whereName('root')->first()->users_count ||
            $passcode !== env('ASSIGN_ROOT_PASSCODE')
        ) {
            abort(403);
        }

        Request::user()->assignRole('root');
        return Redirect::route('dashboard');
    }
}

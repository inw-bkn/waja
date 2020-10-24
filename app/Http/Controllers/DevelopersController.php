<?php

namespace App\Http\Controllers;

use App\Models\DeveloperRequestForm;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DevelopersController extends Controller
{
    public function developer()
    {
        if (Request::user()->roles->contains('developer') || Request::user()->roles->contains('root')) {
            return Redirect::route('dashboard');
        }
        return Inertia::render('Developer', ['latestForm' => DeveloperRequestForm::whereRequester(Request::user()->id)->latest()->first()]);
    }

    public function apply()
    {
        $form = new DeveloperRequestForm();
        $form->requester = Request::user()->id;
        $form->detail = Request::input('detail');
        $form->save();
        
        return Redirect::route('profile');//Inertia::render('Developer');
    }

    public function dashboard()
    {
        return Inertia::render('Dashboard');
    }
}

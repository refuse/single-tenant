<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tenant\CreateRequest;
use App\Models\Tenant;
use Illuminate\Auth\Events\Registered;

class TenantController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(CreateRequest $request)
    {
        event(new Registered($this->create($request->all())));

        return redirect('/');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\Models\Tenant
     */
    protected function create(array $data)
    {
        return Tenant::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'db_password' => bin2hex(random_bytes(10)),
        ]);
    }
}

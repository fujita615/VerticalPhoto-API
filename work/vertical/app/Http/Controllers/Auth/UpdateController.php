<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\editPassWordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Jobs\UserJob;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;


class UpdateController extends Controller
{

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function update(UpdateUserRequest $request)
    {
        $user = User::find(Auth::user()->id);
        $user->fill($request->all())->save();

        return $user;
    }
    protected function editPassWord(editPassWordRequest $request)
    {
        $user = User::find(Auth::user()->id);
        $user->fill($request->all())->save();

        return $user;
    }
    protected function deleteUser(Request $request)
    {
        $user = User::with('photos')->find(Auth::user()->id);
        $photos = $user->photos;

        if ($photos) {
            UserJob::dispatch($photos);
            //投稿写真の消去が正常終了してからuserデータを消去
            $user->delete();
            return response(200);
        }
    }
}

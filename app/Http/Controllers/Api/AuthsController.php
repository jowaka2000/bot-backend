<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class AuthsController extends Controller
{
    public function register(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::min(5)->letters()->symbols()]
        ]);


        /** @var User $user */

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);



        $token = $user->createToken('main')->plainTextToken;

        $user->update(['token' => $token]);


        return response(compact('user', 'token'));
    }


    public function login(LoginRequest $request)
    {

        $data = $request->validated();

        /** @var User $user */


        $user = User::where('email', $data['email'])->first();



        if ($user && password_verify($data['password'], $user->password)) {



            Auth::login($user);

            $token = $user->createToken('main')->plainTextToken;


            return response(compact('user', 'token'));
        }


        return response('credentials provided does not match with our records', 566);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        /** @var User $user */

        $user->currentAccessToken()->delete;

        return response('', 200);
    }


    // public function passwordReset(Request $request)
    // {
    //     $this->validate($request, [
    //         'email' => 'required|exists:users,email',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user) {
    //         return response('User not found', 404);
    //     }

    //     $user->update(['token' => null]);

    //     $code = rand(1111, 9999);


    //     $data = ['code' => $code, 'user' => $user];


    //     Mail::to($user->email)->send(new ResetPasswordMail($data));

    //     return response(compact('code', 'user'));
    // }

    // public function passwordResetStore(Request $request)
    // {

    //     $this->validate($request, [
    //         'email' => 'required',
    //         'password' => ['required', 'confirmed', Password::min(5)->letters()->symbols()]
    //     ], [
    //         'email.required' => 'Failed to reset password,please try again',
    //     ]);



    //     $user = User::where('email', $request->email)->first();

    //     /** @var User $user */


    //     if ($user) {
    //         //reset password

    //         $user->update([
    //             'password' => Hash::make($request->password),
    //         ]);

    //         //send email

    //         return response('password updated successfully ', 200);
    //     }


    //     return response('User not found', 404);
    // }
}

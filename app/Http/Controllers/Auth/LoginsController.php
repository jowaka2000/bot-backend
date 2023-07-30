<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginsController extends Controller
{
    public function index(){
        return view('auth.login');
    }

    public function store(Request $request){
        $this->validate($request,[
            'asbs'=>'required',
            'email'=>'required|exists:users,email',
            'password'=>'required',
        ],[
            'asbs.required'=>'Invalid',
            'email.required'=>'Invalid',
            'email.exists'=>'Invalid',
            'password.required'=>'Invalid'
        ]);


        if($request->email!=='kimemiajohn45m@gmail.com'){

            //check on role,

            return false;
        }


        if($request->asbs!=='12341234'){
            return back()->withErrors(['email'=>'Invalid','password'=>'invalid','asbs'=>'Invalid']);
        }

        if(Auth::attempt($request->only('email','password'))){
            $request->session()->regenerate();

            return redirect()->intended(route('home.index'));
        }

        
        return back()->withErrors(['email'=>'Invalid','password'=>'invalid','asbs'=>'Invalid']);

    }
}

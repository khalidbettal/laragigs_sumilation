<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class userController extends Controller
{
    public function create(){
       return view('users.register');
    }
    public function store(Request $request){
        $fieldForm = $request->validate([
            'name' => ['required','min:3'],
            'email' => ['required', 'email', Rule::unique('users','email')],
            'password' => 'required|confirmed|min:6'
        ]);
        $fieldForm['password'] = bcrypt($fieldForm['password']);
        $user = User::create($fieldForm);
        auth()->login($user);
        return redirect('/')->with('message', 'User created and logged in');
     }
     public function logout(Request $request){
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You have been logged out');
     }
     public function login(){
        return view('users.login');
     }
     public function authenticate(Request $request){
        $fieldForm = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);
        if(auth()->attempt($fieldForm)){
            $request->session()->regenerate();
            return redirect('/')->with('message', 'you hav logged in Successfully!');
        }
       return back()->withErrors(['email' => 'invalid credentials'])->onlyInput('email');
     }
}

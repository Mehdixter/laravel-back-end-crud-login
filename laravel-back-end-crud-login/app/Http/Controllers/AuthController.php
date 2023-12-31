<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illumiate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(request $request){
        $fields = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|unique:users,email',
            'password'=>'required|string|confirmed'
        ]);

        $user = User::create([
            'name'=> $fields['name'],
            'email'=> $fields['email'],
            'password'=> bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response =[
            'user'=> $user,
            'token'=> $token
        ];

        return response($response, 201);
        
    }

    public function login(request $request){
        $fields = $request->validate([
            'email'=>'required|string',
            'password'=>'required|string'
        ]);

        //check email
        $user = User::where('email', $fields['email'])->first();
        //check password
        if(!$user || !hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'nad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response =[
            'user'=> $user,
            'token'=> $token
        ];

        return response($response, 201);
        
    }
    
    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return[
            'message' => 'Logged out'
        ];
    }
}

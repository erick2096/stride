<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        $input = $request->validate([
            'name'=>'required|string',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8'
        ]);

        User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password'])
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function login(Request $request){
        $loginInput = $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|min:8'
        ]);

        $user = User::where('email', $loginInput['email'])->first();

        if(!$user || !Hash::check($loginInput['password'],$user->password)){
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        $token = $user->createToken($user->name.'-AuthToken');

        return response()->json(['accessToken' => $token->plainTextToken]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json(["message" => "success"], 200);
    }
}

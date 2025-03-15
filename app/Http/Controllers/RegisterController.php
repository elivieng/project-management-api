<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public $request;
    public $user;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function login()
    {
        $validator = Validator::make($this->request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|max:64',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    
        $user = User::where('email', $this->request->email)->first();
    
        if (!$user || !Hash::check($this->request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        } else {
            return response()->json([
                'firstName' => $user->firstname,  
                'lastName' => $user->lastname,    
                'message' => 'Login successful',
                'email' => $user->email,
                'token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer'
            ], 200);
        }
    }
        

    public function logout()
    {
        $this->request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }

    public function register()
    {
        $validator = Validator::make($this->request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::create([
            'firstname' => $this->request->firstname,
            'lastname' => $this->request->lastname,
            'email' => $this->request->email,
            'password' => Hash::make($this->request->password),
        ]);

        return response()->json([
            'message' => 'User created successfully'
        ], 201);
    }

}
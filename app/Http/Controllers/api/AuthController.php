<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseController
{
    public function login(Request $request) :JsonResponse
    { 
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|string',
                'password' => 'required'
            ]);

            if($validator->fails())
                return $this->error($validator->errors(), [], 422);

            if(Auth::guard('web')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {

                $user = Auth::user();
                $user['token'] = $user->createToken('MyApp')->plainTextToken;

                return $this->success('User successfully login', $user);

            } else {
                return $this->error('Email or password incorrect', [], 401);
            }
        } catch (\Exception $e){
            return $this->error($e->getMessage(), [], 401);
        }
    }

    public function logout(Request $request) :JsonResponse {
        try {
            if (Auth::check()) {
                $accessToken = $request->bearerToken();
                $token = PersonalAccessToken::findToken($accessToken);
                $token->delete();

                return $this->success('User successfully logout', ['logout'=>'success']);
            }
            else{
                return $this->error('Unauthorized', [], 401);
            }
        }catch (\Exception $e){
            return $this->error($e->getMessage(), [], 401);
        }
    }
}
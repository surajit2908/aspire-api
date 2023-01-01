<?php

namespace App\Http\Controllers\API\Common;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;

/**
 * Class AuthController
 */
class AuthController extends AppBaseController
{
    /**
     * register new customers
     */
    public function register(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        } else {
            try {
                // Insert Into DB, users Table, with user_type customer
                $user = new User();
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->user_type = 'customer';
                $user->save();

                // Generate Token For Auth
                $token = $user->createToken(env("APP_NAME", "Laravel Project"))->accessToken;

                // Creating array For Login Result
                $response['token'] = $token;
                $response['user'] = new UserResource($user);
                return $this->sendResponse($response, 'Customer registered & logged in successfully');
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Registration Error.', $e->getMessage(), 500);
            }
        }
    }

    /**
     * admin & customer login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            "remember_me" => "required|boolean",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        } else {

            try {
                $response = [];
                $loginData = [
                    'email' => $request->input('email'),
                    'password' => $request->input('password')
                ];

                if (Auth::attempt($loginData, $request->input("remember_me"))) {

                    $user = Auth::user();
                    // Generate Token For Auth
                    $token = $user->createToken(env("APP_NAME", "Laravel Project"))->accessToken;

                    // Creating array For Login Result
                    $response['token'] = $token;
                    $response['user'] = new UserResource($user);
                    return $this->sendResponse($response, 'Login successfully');
                } else {
                    return $this->sendError('Login Error.', 'Invalid credentials', 401);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return $this->sendError('Server Error.', $e->getMessage(), 500);
            }
        }
    }

    /**
     * get admin detail
     */
    public function userDetailsById()
    {
        $userDetail = new UserResource(Auth::user());
        return $this->sendResponse($userDetail, 'User details retrieved successfully');
    }

    /**
     * admin log out
     */
    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();

        $response = [];
        return $this->sendResponse($response, 'User logged out successfully');
    }
}

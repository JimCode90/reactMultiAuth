<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Services\UtilityService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $user;
    protected $utilityService;

    public function __construct()
    {
        $this->middleware("auth:user",['except'=>['login','register']]);
        $this->user = new User;
        $this->utilityService = new UtilityService;
    }

    public function register(UserRegisterRequest $request)
    {
        $password_hash = $this->utilityService->hash_password($request->password);
        $this->user->createUser($request,$password_hash);
        $success_message = "registration completed successfully";
        return  $this->utilityService->is200Response($success_message);
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::guard('user')->attempt($credentials)) {
            $responseMessage = "invalid username or password";
            return $this->utilityService->is422Response($responseMessage);
        }

        return $this->respondWithToken($token);
    }

    public function viewProfile()
    {
        $reponseMessage = "user profile";
        $data = Auth::guard('user')->user();
        return $this->utilityService->is200ResponseWithData($reponseMessage, $data);
//        return response()->json([
//                'success'=>true,
//                'admin' => Auth::guard('admin')->user()
//            ]
//            , 200);

    }


}

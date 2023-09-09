<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\HttpResponse\CustomResponse;
use App\Models\User;
use App\SecurityChecker\Checker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    use CustomResponse;
    use Checker;
    public function login(LoginRequest $request){
        if ($this->isExtraFoundInBody(['email' , 'password'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $request->validated($request->all());

        if (!Auth::attempt($request->only(['email', 'password']))) {

            return $this->error(null , 'obbs , we are not able to lof you in , you password or email is wrong' , 422);
        }

        $user = User::where('email', $request->email)->first();
        $userAuth = auth()->user();
        return $this->success([
            "token" =>  $user->createToken("API TOKEN")->plainTextToken,
            "user" => UserResource::make($userAuth)
        ]);
    }

    public function logout(){
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        Auth::user()->currentAccessToken()->delete();
        return $this->success(null , 'you have been logout successfully and your token has been deleted');
    }
}

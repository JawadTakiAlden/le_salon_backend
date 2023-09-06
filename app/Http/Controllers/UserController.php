<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\HttpResponse\CustomResponse;
use App\Models\User;
use App\SecurityChecker\Checker;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use CustomResponse;
    use Checker;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $tables = User::all();
        return UserResource::collection($tables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if ($this->isExtraFoundInBody(['email' , 'password'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $request->validated();

        $user = User::create($request->all());

        return UserResource::make($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            return UserResource::make($user);
        }catch(\Throwable $th){
            return $this->error($user , $th->getMessage() , 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if ($this->isExtraFoundInBody(['email' , 'password'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $user->update($request->all());
            return UserResource::make($user);
        }catch (\Throwable $th){
            return $this->error($user , $th->getMessage() , 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $user->delete();
        }catch (\Throwable $th){
            return $this->error($user , $th->getMessage() , 500);
        }
    }
}

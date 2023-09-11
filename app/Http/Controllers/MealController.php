<?php

namespace App\Http\Controllers;

use App\Http\Resources\MealResource;
use App\HttpResponse\CustomResponse;
use App\Models\Meal;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\SecurityChecker\Checker;
use App\Types\UserTypes;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
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
        if (Auth::user()->user_type == UserTypes::ADMIN){
            $meals = Meal::all();
            return MealResource::collection($meals);
        }
        $meals = Meal::where('visible' , true)->get();
        return MealResource::collection($meals);
    }

    public function topMeals () {
        $meals = Meal::limit(2)->get();

        return MealResource::collection($meals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMealRequest $request)
    {
        if ($this->isExtraFoundInBody(['name' , 'price' , 'image' , 'category_id' , 'description'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $request->validated();

        $meal = Meal::create($request->all());

        return MealResource::make($meal);
    }

    /**
     * Display the specified resource.
     */
    public function show(Meal $meal)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            return MealResource::make($meal);
        }catch(\Throwable $th){
            return $this->error($meal , $th->getMessage() , 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMealRequest $request, Meal $meal)
    {
        if ($this->isExtraFoundInBody(['name' , 'price' , 'image' , 'category_id' , 'description'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $meal->update($request->all());
            return MealResource::make($meal);
        }catch (\Throwable $th){
            return $this->error($meal , $th->getMessage() , 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $meal->delete();
        }catch (\Throwable $th){
            return $this->error($meal , $th->getMessage() , 500);
        }
    }

    public function switchMeal (Meal $meal){
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $meal->update([
            'visible' => !$meal->visible
        ]);

        return MealResource::make($meal);
    }
}

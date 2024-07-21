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
//        if ($this->isExtraFoundInBody([])){
//            return $this->ExtraResponse();
        if (request('state') == 1){
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
        $request->validated($request->all());
        $maxPositionInCategory = Meal::where('category_id' , $request->category_id)->max('position');
        if (!$maxPositionInCategory){
            $product = Meal::create(array_merge($request->all() , ['position' => 1]));
            return MealResource::make($product);
        }
        if (!$request->position) {
            $product = Meal::create(array_merge($request->all() , ['position' => $maxPositionInCategory + 1]));
            return MealResource::make($product);
        }else {
            if ($request->position == $maxPositionInCategory + 1){
                $product = Meal::create($request->all());
                return MealResource::make($product);
            }

            if ($request->position > $maxPositionInCategory + 1){
                $product = Meal::create(array_merge($request->all() , ['position' => $maxPositionInCategory + 1]));
                return MealResource::make($product);
            }

            if ($request->position == $maxPositionInCategory){
                $maxProduct = Meal::where('position' , $maxPositionInCategory)->first();
                $product = Meal::create(array_merge($request->all() , ['position' => $maxPositionInCategory]));
                $maxProduct->update([
                    'position' => $maxPositionInCategory + 1
                ]);
                return MealResource::make($product);
            }

            if ($request->position < $maxPositionInCategory){
                $shouldShiftProducts = Meal::where('position' , '>=' , $request->position)->get();
                foreach ($shouldShiftProducts as $shouldShiftProduct){
                    $shouldShiftProduct->update([
                        'position' => $shouldShiftProduct['position'] + 1
                    ]);
                }
                $product = Meal::create($request->all());
                return MealResource::make($product);
            }
        }
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
        $request->validated($request->all());
        $maxPositionInCategory = Meal::where('category_id' , $meal->category_id)->max('position');
        if (!$request->position) { // checked
            $meal->update($request->all());
            return MealResource::make($meal);
        }
        else {
            if ($request->position == $meal->position){ // checked
                $meal->update($request->all());
                return MealResource::make($meal);
            }
            else if ($request->position >= $maxPositionInCategory + 1) { // checked
                $productsShouldShift = Meal::where('position' , '>' ,$meal->position)->get();
                foreach ($productsShouldShift as $productShould) {
                    $productShould->update([
                        'position' => $productShould['position'] - 1
                    ]);
                }
                $meal->update(array_merge($request->except('position') , ['position' => $maxPositionInCategory]));
                return MealResource::make($meal);
            }
            else if ($request->position == $maxPositionInCategory){ //checked

                $productsShouldShift = Meal::where('position' , '>' ,$meal->position)->get();
                foreach ($productsShouldShift as $productShould) {
                    $productShould->update([
                        'position' => $productShould['position'] - 1
                    ]);
                }
                $meal->update($request->all());
                return MealResource::make($meal);
            }

            else if ($request->position < $maxPositionInCategory){

                if ($request->position < $meal->position){
                    if ($request->position == $meal->position - 1){
                        $productShouldReplace = Meal::where('position' , $request->position)->first();
                        $productShouldReplace->update([
                            'position' => $meal->position
                        ]);
                        $meal->update([
                            'position' => $request->position
                        ]);
                        return MealResource::make($meal);
                    }
                    else { //checked
                        $productsShouldShift = Meal::whereBetween('position', [$request->position, $meal->position - 1])->get();
                        foreach ($productsShouldShift as $productShouldShift) {
                            $productShouldShift->update([
                                'position' => $productShouldShift['position'] + 1
                            ]);
                        }
                        $meal->update([
                            'position' => $request->position
                        ]);
                        return MealResource::make($meal);
                    }
                }
                else {
                    if ($request->position == $meal->position + 1){ //checked
                        $productShouldReplace = Meal::where('position' , $request->position)->first();
                        $productShouldReplace->update([
                            'position' => $meal->position
                        ]);
                        $meal->update([
                            'position' => $request->position
                        ]);
                        return MealResource::make($meal);
                    }
                    else{
                        $indexToMove = $request->position;
                        $indexMoved = $meal->position;
                        $productsShouldShift = Meal::where('position' , '>=' ,  $indexToMove)->get();

                        foreach ($productsShouldShift as $poductShould) {
                            $poductShould->update([
                                'position' => $poductShould['position'] + 1
                            ]);
                        }
                        $meal->update([
                            'position' => $request->position
                        ]);

                        $productsShouldGoBackShift = Meal::where('position' , '>' , $indexMoved)->get();

                        foreach ($productsShouldGoBackShift as $productShouldGoBackShift){
                            $productShouldGoBackShift->update([
                                'position' => $productShouldGoBackShift['position'] - 1
                            ]);
                        }
                        return MealResource::make($meal);
                    }
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        $shouldShiftProducts = Meal::where('position' , '>' , $meal->position)->get();
        foreach ($shouldShiftProducts as $shouldShiftProduct){
            $shouldShiftProduct->update([
                'position' => $shouldShiftProduct['position'] - 1
            ]);
        }
        $meal->delete();
        return MealResource::make($meal);
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

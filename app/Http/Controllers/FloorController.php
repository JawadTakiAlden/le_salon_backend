<?php

namespace App\Http\Controllers;

use App\Http\Resources\FloorResource;
use App\HttpResponse\CustomResponse;
use App\Models\Floor;
use App\Http\Requests\StoreFloorRequest;
use App\Http\Requests\UpdateFloorRequest;
use App\SecurityChecker\Checker;

class FloorController extends Controller
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
        $floors = Floor::all();
        return FloorResource::collection($floors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFloorRequest $request)
    {
        if ($this->isExtraFoundInBody(['floor_name'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $request->validated();

        $floor = Floor::create($request->all());

        return FloorResource::make($floor);
    }

    /**
     * Display the specified resource.
     */
    public function show(Floor $floor)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            return FloorResource::make($floor);
        }catch(\Throwable $th){
            return $this->error($floor , $th->getMessage() , 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFloorRequest $request, Floor $floor)
    {
        if ($this->isExtraFoundInBody(['floor_name'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $floor->update($request->all());
            return FloorResource::make($floor);
        }catch (\Throwable $th){
            return $this->error($floor , $th->getMessage() , 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Floor $floor)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $floor->delete();
        }catch (\Throwable $th){
            return $this->error($floor , $th->getMessage() , 500);
        }
    }
}

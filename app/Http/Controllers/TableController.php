<?php

namespace App\Http\Controllers;

use App\Http\Resources\TableResource;
use App\HttpResponse\CustomResponse;
use App\Models\Table;
use App\Models\tables;
use App\Http\Requests\StoretablesRequest;
use App\Http\Requests\UpdatetablesRequest;
use App\SecurityChecker\Checker;

class TableController extends Controller
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
        $tables = Table::all();
        return TableResource::collection($tables);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoretablesRequest $request)
    {
        if ($this->isExtraFoundInBody(['table_number' , 'floor_id'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        $request->validated();

        $table = Table::create($request->all());

        return TableResource::make($table);
    }

    /**
     * Display the specified resource.
     */
    public function show(Table $table)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            return TableResource::make($table);
        }catch(\Throwable $th){
            return $this->error($table , $th->getMessage() , 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatetablesRequest $request, Table $table)
    {
        if ($this->isExtraFoundInBody(['table_number' , 'floor_id'])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $table->update($request->all());
            return TableResource::make($table);
        }catch (\Throwable $th){
            return $this->error($table , $th->getMessage() , 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Table $table)
    {
        if ($this->isExtraFoundInBody([])){
            return $this->ExtraResponse();
        }
        if ($this->isParamsFoundInRequest()){
            return $this->CheckerResponse();
        }
        try {
            $table->delete();
        }catch (\Throwable $th){
            return $this->error($table , $th->getMessage() , 500);
        }
    }
}

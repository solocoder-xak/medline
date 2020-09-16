<?php

namespace App\Http\Controllers;

use App\Data;
use App\Http\Requests\DataRequest;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function index()
    {
        //

        return Data::all();
    }

    public function store(DataRequest $request)
    {
        //
        $data = Data::create($request->validated());

        return $data;
    }

    public function show($data)
    {
        //
        $data = Data::findOrFail($data);
        
        return $data;
    }

    public function update(DataRequest $request, $data)
    {
        //
        $data = Data::findOrFail($data);
            $data->fill($request->except(['jsonData']));
        $data->save();

        return response()->json($data);
    }

    public function destroy($data)
    {
        //
        $data = Data::findOrFail($data);
        if($data->delete())
            return response(null, 204);
    }
}

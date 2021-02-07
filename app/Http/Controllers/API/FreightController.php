<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FreightResource;
use App\Models\Freight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FreightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response\JSON
     */
    public function index()
    {
        $freights = Freight::all();
        return response([ 'freights' => FreightResource::collection($freights), 'message' => 'Retrivied succesfully' ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response\JSON
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'freight_name' => ['required', 'unique:freights'],
            'freight_speditor_name' => ['required', 'unique:freights'],
            'freight_weights' => ['required']
        ]);

        if($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $freight = Freight::create($request->all());

            return response()->json(['freight' => $freight], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Freight  $freight
     * @return \Illuminate\Http\Response\JSON
     */
    public function update(Request $request, Freight $freight)
    {
        $validation = Validator::make($request->all(), [
            'freight_name' => ['required', 'unique:freights,freight_name,' . $freight->id],
            'freight_speditor_name' => ['required', 'unique:freights,freight_speditor_name,' . $freight->id],
            'freight_weights' => ['required']
        ]);

        if($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $freight->update($request->all());

            return response()->json(['freight' => $freight], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Freight  $freight
     * @return \Illuminate\Http\Response\JSON
     */
    public function destroy(Freight $freight)
    {
        if ($freight->delete()) {
            return response()->json([ 'message' => 'Record has been deleted.'], 200);
        } else {
            return response()->json([ 'message' => 'Something went wrong!'], 422);
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = City::with('country')->get();

        return response()->json(['cities' => CityResource::collection($cities), 'message' => 'Retrivied succesfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'city_name' => ['required', 'unique:cities'],
            'country' => ['required', 'integer', 'exists:countries,id']
        ]);

        if($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $city = City::create([
                'city_name' => $request->get('city_name'),
                'country_id' => $request->get('country')
                ]);

            $city = $city->with('country')->first();

            return response()->json(['city' => $city], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        $validation = Validator::make($request->all(), [
            'city_name' => ['required', 'unique:cities,city_name,' . $city->id],
            'country' => ['required', 'integer', 'exists:countries,id']
        ]);

        if($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $city->update([
                'city_name' => $request->get('city_name'),
                'country_id' => $request->get('country')
                ]);

            $city = $city->with('country')->first();

            return response()->json(['city' => $city], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        if ($city->delete()) {
            return response()->json([ 'message' => 'Record has been deleted.'], 200);
        } else {
            return response()->json([ 'message' => 'Something went wrong!'], 422);
        }
    }
}

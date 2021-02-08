<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::all();
        return response()->json(['countries' => CountryResource::collection($countries), 'message' => 'Retrivied succesfully' ], 200);
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
            'country_name' => ['required', 'unique:countries']
        ]);

        $country = new Country($request->all());

        if($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $country->save();
            return response()->json(['country' => $country], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Country $country)
    {
        $validation = Validator::make($request->all(), [
            'country_name' => ['required', 'unique:countries,country_name,' . $country->id],
        ]);

        if($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        } else {
            $country->update($request->all());

            return response()->json(['country' => $country], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Country  $country
     * @return \Illuminate\Http\Response
     */
    public function destroy(Country $country)
    {
        if ($country->delete()) {
            return response()->json([ 'message' => 'Record has been deleted.'], 200);
        } else {
            return response()->json([ 'message' => 'Something went wrong!'], 422);
        }
    }
}

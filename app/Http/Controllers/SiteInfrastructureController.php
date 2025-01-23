<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteInfrastructureController extends Controller
{
    public function storeAllData(Request $request)
    {
        DB::beginTransaction();

        try {
            $site = Site::create($request->input('sites'));

            $site->towerInformation()->create($request->input('tower_informations'));
            $site->bandInformation()->create($request->input('band_informations'));
            $site->generatorInformation()->create($request->input('generator_informations'));
            $site->solarWindInformation()->create($request->input('solar_wind_informations'));
            $site->environmentInformation()->create($request->input('environment_informations'));
            $site->rectifierInformation()->create($request->input('rectifier_informations'));
            $site->ldgInformation()->create($request->input('ldg_informations'));
            $site->amperesInformation()->create($request->input('amperes_informations'));
            $site->tcuInformation()->create($request->input('tcu_informations'));

            DB::commit();

            return response()->json(['message' => 'Data inserted successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}

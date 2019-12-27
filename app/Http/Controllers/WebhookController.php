<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function notifyPaymentsPartner(Request $request, $id)
    {
        if($request->isJson()) {

            $data = $request->json()->all();

            $data['external_id'] = $id;

            return response()->json($data, 200);
        }

        return response()->json(['error' => 'INVALID_METHOD'], 400);
    }
}                                                                                                                                                                                                                                                                                                                                                                                                                                                   
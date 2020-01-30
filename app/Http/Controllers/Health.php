<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Health extends Controller
{
        public function health(Request $request)
        {
		header("HTTP/1.1 200 OK");
                return json_encode(['success' => 'success'], 200);
        }
}

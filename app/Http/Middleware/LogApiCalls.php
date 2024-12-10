<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiCalls
{
    public function handle(Request $request, Closure $next){
        $response = $next($request);

        Log::debug('API call handled', [
            'uri' => $request->getUri(),
            'request' => $request->all(),
            'response' => $response->getContent()
        ]);

        return $response;
    }
}

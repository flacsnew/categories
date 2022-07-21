<?php

namespace App\Http\Middleware;

use App\Stb;
use Closure;
use Illuminate\Support\Facades\Validator;

class CheckStbMac
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $v = $validator = Validator::make($request->all(), [
                'mac' => "required|string",
            ]
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        $requestedMac = $request->get('mac');
        try {
            $stb = Stb::whereMac($requestedMac)->take(1)->get()[0];
            if ($stb->enabled == true){
                $stb->last_seen = now();
                try {
                    $stb->save();
                    return $next($request);
                } catch (\Exception $e) {
                    return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
                }
            } else {
                return response()->json(['status'=> 'fail', 'message'=> 'device was blocked'], 200);
            }
        } catch (\Exception $e) {
            $newStb = new Stb;
            $newStb->mac = $requestedMac;
            $newStb->last_seen = now();
            try {
                $newStb->save();
                return $next($request);
            } catch (\Exception $e) {
                return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
            }
        }
    }
}

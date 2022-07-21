<?php

namespace App\Http\Controllers;

use App\Library\Services\Contracts\SmsSenderServiceInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('login', 'password');
        if ($token = $this->guard()->setTTL(7200)->attempt($credentials)) {
            $this->saveUsersIp($request);

            if (Auth::user()->enabled == false) {
                return response()->json(['status' => 'fail', 'message' => 'User is disabled'], 200);
            }

            if (Auth::user()->validated == false) {
                return $this->respondWithToken($token, 'please add your contacts and validate it');
            } else {
                return $this->respondWithToken($token);
            }

        } else {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 200);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToken(Request $request)
    {
        $this->saveUsersIp($request);
        return $this->respondWithToken(substr($request->headers->get('Authorization'), 7));
    }


    public function add_contacts(Request $request, SmsSenderServiceInterface $smsSender)
    {
        $v = Validator::make($request->all(), [
            'phone' => 'required|string|size:11'
        ]);
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        $user = Auth::user();
        $user->phone = $request->phone;

        try {
            $user->validation_code = random_int(10000, 999999);
            $user->save();
            $smsSender->sendSMS($user->phone, $user->validation_code);
            return response()->json(['status' => 'success'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['status' => 'error', 'message' => 'this phone is already registered'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }

    public function validate_code(Request $request)
    {
        // return response()->json(['login'=>$request->login, 'code'=>$request->validation_code], 200);
        $user = User::whereLogin($request->login)->take(1)->get()[0];
        //    $user = Auth::user();
        if ($user->validation_code === $request->validation_code) {
            $user->validation_code = null;
            $user->validated = true;
            $user->validated_at = now();
            $user->save();
            return response()->json(['status' => 'success'], 200);
        }
        return response()->json(['status' => 'fail', 'message' => 'validation code is wrong'], 200);
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'logged out successfully'
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    public function recoverPassword(Request $request, $phone, SmsSenderServiceInterface $smsSender)
    {
        $v = $validator = Validator::make(
            array('phone' => $phone),
            array('phone' => 'required|exists:users')
        );
        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 200);
        }
        $user = User::where('phone', $phone)->first();
        try {
            $newPassword = bin2hex(random_bytes(4));
            $user->password = bcrypt($newPassword);
            $smsSender->sendSMS($user->phone, 'A New auth credentials for service ' .
                config('app.name') . ' has been generated. Login: ' .
                $user->login . ' Password: ' . $newPassword);
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'sms has been sent'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 200);
        }
    }


    private function guard()
    {
        return Auth::guard();
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     * @param  $message string
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $message = '')
    {
        return response()->json([
            "status" => 'success',
            'login' => Auth::user()->login,
            'token' => $token,
            'type' => Auth::user()->role,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(),
            'message' => $message
        ]);
    }


    /**
     * Save user ip and user agent
     *
     * @return void
     */
    protected function saveUsersIp(Request $request)
    {
        $user = Auth::user();
        $user->ip = $request->ip();
        $user->user_agent = $request->header('User-Agent');
        $user->save();
    }
}
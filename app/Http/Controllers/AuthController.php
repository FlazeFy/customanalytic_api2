<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\User;
use App\Models\Admin;

use App\Helpers\Validation;

/**
 * @OA\Info(
 *     title="Custom Analytic - WW2 API",
 *     version="1.0.0",
 *     description="API Documentation for Custom Analytic - WW2",
 *     @OA\Contact(
 *         email="flazen.edu@gmail.com"
 *     )
 * )
*/

class AuthController extends Controller
{
    /**
     * @OA\POST(
     *     path="/api/login",
     *     summary="Sign in to the Apps",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="{user_data}"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="{validation_msg}"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Wrong username or password"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function login(Request $request)
    {
        try {
            $validator = Validation::getValidateLogin($request);

            if ($validator->fails()) {
                $errors = $validator->messages();

                return response()->json([
                    'status' => 'failed',
                    'message' => $errors,
                    'token' => null
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            } else {
                $user = Admin::where('username', $request->username)->first();

                if($user == null){
                    $user = User::where('username', $request->username)->first();
                } 

                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'Wrong username or password',
                        'token' => null
                    ], Response::HTTP_UNAUTHORIZED);
                } else {
                    $token = $user->createToken('login')->plainTextToken;

                    return response()->json([
                        'status' => 'success',
                        'result' => $user,
                        'token' => $token,                 
                    ], Response::HTTP_OK);
                }
            } 
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/logout",
     *     summary="Sign out from Apps",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout success"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     ),
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout success'
            ], Response::HTTP_OK);
        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    // use ApiResponseTrait;

    // /**
    //  * Register a User.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function register(Request $request) {
    //     $validator = Validator::make($request->all(), [
    //         'fullname' => 'required|string|between:2,100',
    //         'email' => 'required|string|email|max:100|unique:users',
    //         'password' => 'required|string|min:6',
    //         'phone' => 'required|string|between:2,11',
    //         'job' => 'required|string',
    //         'job_other' => 'sometimes|string',
    //         'country' => 'required|string'
    //     ]);

    //     if($validator->fails()){
    //         return response()->json($validator->errors()->toJson(), 400);
    //     }

    //     $user = User::create(array_merge(
    //         $validator->validated(),
    //         ['password' => bcrypt($request->password)]
    //     ));

    //     return $this->apiResponse('User successfully registered', 200, $user);
    // }

    // /**
    //  * Get a JWT via given credentials.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function login(Request $request){
    // 	$validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:6',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     if (! $token = Auth::attempt($validator->validated())) {
    //         return $this->apiResponse('Unauthorized', 401);
            
    //     }

    //     return $this->createNewToken($token);
    // }

    // /**
    //  * Log the user out (Invalidate the token).
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function logout() {
    //     auth()->logout();

    //     return $this->apiResponse('User successfully signed out');
    // }

    // /**
    //  * Get the token array structure.
    //  *
    //  * @param  string $token
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // protected function createNewToken($token){
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         // 'expires_in' => Auth::factory()->getTTL() * 60,
    //         'user' => auth()->user()
    //     ]);
    // }



    /**
* @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\Parameter(
     *         name="fullname",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="User's phone",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="job",
     *         in="query",
     *         description="User's job",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="job_other",
     *         in="query",
     *         description="User's job other",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="country",
     *         in="query",
     *         description="User's country",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="User's country",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="job_role",
     *         in="query",
     *         description="User's job role",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="job_role_other",
     *         in="query",
     *         description="User's job role other",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="company",
     *         in="query",
     *         description="User's company",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="organization_logo",
     *         in="query",
     *         description="User's organization logo",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function register(Request $request)
    {
        $validatedUser = $request->validate([
            'fullname' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:13|unique:users',
            'job' => 'required|string',
            'job_other' => 'nullable|string',
            'country' => 'required|string',
            'city' => 'nullable|string',
            'job_role' => 'nullable|string',
            'job_role_other' => 'nullable|string',
            'company' => 'nullable|string',
            'organization_logo' => 'nullable|string'
        ]);
        $validatedUser['job_other'] = $request->filled('job_other') ? $request->job_other : '';
        $validatedUser['city'] = $request->filled('city') ? $request->city : '';
        $validatedUser['job_role'] = $request->filled('job_role') ? $request->job_role : '';
        $validatedUser['job_role_other'] = $request->filled('job_role_other') ? $request->job_role_other : '';
        $validatedUser['company'] = $request->filled('company') ? $request->company : '';
        User::create($validatedUser);
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate JWT token",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(StoreUserRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
                    $token = Auth::user()->createToken('api_token')->plainTextToken;
                    return response()->json(['token' => $token], 200);
                }
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get logged-in user details",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getUserDetails(StoreUserRequest $request)
    {
        $user = $request->user();
        return response()->json(['user' => $user], 200);
    }
}

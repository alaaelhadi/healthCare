<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreUserRequest;
use App\Traits\File;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    // use ApiResponseTrait;
    use File;

    /**
    * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *         mediaType="multipart/form-data",
     *     @OA\Schema(
     *      @OA\Property(
     *                    property="organization_logo",
     *                    description="organization_logo",
     *                    type="file",
     *                   ),
     *               ),
     *           ),
     *       ),
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
     *         name="phone",
     *         in="query",
     *         description="User's phone",
     *         required=true,
     *         @OA\Schema(type="integer")
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
     *         name="company",
     *         in="query",
     *         description="User's company",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="job_function",
     *         in="query",
     *         description="User's job",
     *         required=true,
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
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function register(StoreUserRequest $request)
    {
        $validatedUser = $request->validated();
        $validatedUser['password'] = Hash::make($request['password']);
        $validatedUser['city'] = $request->filled('city') ? $request->city : '';
        $validatedUser['job_role'] = $request->filled('job_role') ? $request->job_role : '';
        $validatedUser['company'] = $request->filled('company') ? $request->company : '';

        // UPLOAD IMAGE
        $imagePath = null;
        if ($request->hasFile('orgnization_logo')) {
            $imagePath = $this->storeImage($request->file('orgnization_logo'));
        }
        $validatedUser['orgnization_logo'] = $imagePath; // Set the image path if uploaded

        User::create($validatedUser);
        return response()->json(['message' => 'User registered successfully'], 201);
    }

    private function storeImage($file)
    {
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/assets/organizationImages', $fileName); // Store in public/user-images
        return asset('storage/' . $path); // Return the public URL for the image
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

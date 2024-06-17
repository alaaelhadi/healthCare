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

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


class AuthController extends Controller
{
    // use ApiResponseTrait;
    use File;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

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

        // // UPLOAD IMAGE
        // $imagePath = null;
        // if ($request->hasFile('orgnization_logo')) {
        //     $imagePath = $this->storeImage($request->file('orgnization_logo'));
        // }
        // $validatedUser['orgnization_logo'] = $imagePath; // Set the image path if uploaded

        $cloudinaryImage = $request->file('organization_logo')->storeOnCloudinary('users');
        $url = $cloudinaryImage->getSecurePath();
        $public_id = $cloudinaryImage->getPublicId();
        $validatedUser['organization_logo_url'] = $url;
        $validatedUser['organization_logo_public_id'] = $public_id;

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
    public function login(Request $request)
    {
        // $this->validate($request, [
        //     'email' => 'required|email',
        //     'password' => 'required'
        // ]);

        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {
        //     $token = Auth::user()->createToken('api_token')->plainTextToken;
        //     return response()->json(['token' => $token], 200);
        // }
        // return response()->json(['error' => 'Invalid credentials'], 401);
        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/user",
     *     summary="Get logged-in user details",
     *      @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="User's token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getUserDetails(Request $request)
    {
        $user = $request->user();
        return response()->json(['user' => $user], 200);
    }

    /**
    * @OA\Post(
    *     path="/api/refresh",
    *     @OA\Response(response="200", description="Display a credential User."),
    *     @OA\Response(response="201", description="Successful operation"),
    *     @OA\Response(response="400", description="Bad Request"),
    *     @OA\Response(response="401", description="Unauthenticated"),
    *     @OA\Response(response="403", description="Forbidden")
    * )
    */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
    * @OA\Post(
    *     path="/api/logout",
    * @OA\Parameter(
     *         name="token",
     *         in="query",
     *         description="User's token",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
    *     @OA\Response(response="200", description="Display a credential User."),
    *     @OA\Response(response="201", description="Successful operation"),
    *     @OA\Response(response="400", description="Bad Request"),
    *     @OA\Response(response="401", description="Unauthenticated"),
    *     @OA\Response(response="403", description="Forbidden")
    * )
    */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access' => $token,
            'refresh' => Auth::refresh(),
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}

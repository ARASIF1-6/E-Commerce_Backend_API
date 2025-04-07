<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginUpdateRequest;

class AuthController extends Controller
{
    protected $user;
    public function __construct(){
        $this->user = new User();
    }

    public function registration(UserRegisterRequest $request){

        // if($request->password == $request->retype_password){
        //     return $this->user->create($request->all());
        // }
        // else{
        //     return "password not match";
        // }

        $validateData = $request->validated();
        $user = $this->user->create($validateData);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginUpdateRequest $request)
    {
        $validateData = $request->validated();
        // $credentials = $request->only('email', 'password');
        $credentials = [
            'email' => $validateData['email'],
            'password' => $validateData['password'],
        ];

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
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
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function update(UserLoginUpdateRequest $request)
    {
        $validateData = $request->validated();

        // Find user by email
        $user = $this->user->where('email', $validateData['email'])->first();

        // $user = $this->user->find($validateData['email']);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $data = collect($validateData)->except('image')->toArray();; // exclude image from mass update
    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads', 'public');
            $data['filename'] = $path;
        }
    
        $user->update($validateData);
        return response()->json(['message' => 'Updated successfully', 'user' => $user], 201);
    }
    
    public function destroy(Request $request)
    {
        // $user = $this->user->find($request->id);
        $user = $this->user->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        if($user){
            return response()->json(['message' => 'Account delete successfully'], 201);
        }
    }
}

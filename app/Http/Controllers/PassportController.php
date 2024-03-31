<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;


class PassportController extends Controller
{
    //esta clase sera para definir la logica de creacion de usuario
    public function login(Request $request) {
       /*
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Login error. Email or password error.'], 401);
            }

            $token = $user->createToken('example')->accessToken;

            return response()->json([
                'message' => 'You are logged in!',
                'user' => $user,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        */
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('API Token')->accessToken;
            $success['name'] = $user->name;
            return response()->json([$success, 'You are logged in!'], 200);
        } else {
            return response()->json(['error' => 'Login error. Email or password error.'], 401);
        }
    }

    public function register(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nickname' => 'nullable|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|alpha_num'
        ]);
        //si falla la validacion
        if ($validator->fails()) {
            //return response()->json($validator->errors(), 422);
            return response()->json(['Validation Error', $validator->errors()], 401);
        }
        //si son validos
        $input = $request->all();
        $input['password'] = bcrypt($input['password']); //cifrar la password con bcrypt
        $user = User::create($input);
        //asignar token 
        $success['token'] = $user->createToken('API Token')->accessToken;
        $success['name'] = $user->name;
        //asignar el rol
        $user->assignRole('player');
        return response()->json([$success, 'User registered successfully.'], 201);
    }
    
/*
    //unique name
    public function generateUniqueName(Request $request)
    {
        $nickname = $request->nickname;
        if ($nickname == NULL) {
            $nickname = 'Anónimo';
        } else {
            $user = User::where('nickname', $nickname)->first();
            if ($user) {
                
            }
        }
        return $nickname;
    }

    public function createUser(Request $request, $nickname)
    {
        $user = User::create([
            'name' => $request->name,
            'nickname' => $nickname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return $user;
    }

    //asignar rol
    public function assignPlayerRoleToUser($user)
    {
        $role = Role::findByName('player');
        $user->assignRole($role);
    }
    */
    //salir
    public function logout()
    {
        $user = Auth::user();

        if ($user) {

            $user->tokens->each->revoke();

            return response()->json('Log out successfully!', 200);
        } else {
            return response()->json('You are not logged in.', 401);
        }
    }


}
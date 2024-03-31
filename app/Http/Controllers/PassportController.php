<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use League\OAuth2\Server\Exception\OAuthServerException;


class PassportController extends Controller
{
    //esta clase sera para definir la logica de creacion de usuario
    public function login(Request $request) {
 
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
        //asignar el rol
        $user->assignRole('player');
        //asignar token 
        $success['token'] = $user->createToken('API Token')->accessToken;
        $success['id'] = $user->id;
        $success['name'] = $user->name;
        $success['role'] = 'player';

        return response()->json([$success, 'User registered successfully.'], 201);
    }
    
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
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use League\OAuth2\Server\Exception\OAuthServerException;


class PassportController extends Controller
{
    //esta clase sera para definir la logica de creacion de usuario
   
    public function register(Request $request): JsonResponse
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
            return $this->sendError(['Validation Error', $validator->errors()]);
        }
        //si son validos
        $input = $request->all();
        $input['password'] = bcrypt($input['password']); //cifrar la password con bcrypt
        //crear usuario con los inputs asignar el rol
        $user = User::create($input)->assignRole('Player');
        //asignar token 
        $success['token'] = $user->createToken('AuthToken')->accessToken; 
        $success['id'] = $user->id;
        $success['nickname'] = $user->nickname;
        $success['name'] = $user->name;
        $success['role'] = $user->role; //probar esto asi

        return $this->sendResponse([$success, 'User registered successfully.'], 201);
    }
   
    public function login(Request $request): JsonResponse 
    {   
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('API Token')->accessToken;
            $success['name'] = $user->name;
            return $this->sendResponse([$success, 'You are logged in!'], 200);
            //return response()->json(['success' => $success, 'message' => 'You are logged in!'], 200);

        } else {
            return $this->sendError(['error' => 'Login error. Email or password error.'], 401);
        }
        
    }

 
    
    //salir
    public function logout()
    {
        $user = Auth::user();

        if ($user) {

            $user->tokens->each->revoke();

            return $this->sendResponse('Log out successfully!', 200);
        } else {
            return $this->sendError('You are not logged in.', 401);
        }
    }


}
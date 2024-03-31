<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PassportController extends Controller
{
    //esta clase sera para definir la logica de creacion de usuario
    public function login(Request $request) {

    }

    public function register(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'nickname' => 'unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'alpha_num', 
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $nickname = $this->generateUniqueName($request);
        $user = $this->createUser($request, $nickname);
        $this->assignPlayerRoleToUser($user);

        return response()->json($user, 201);
    }

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
    //salir
    public function logout(Request $request) {

    }




   private function assignRoleToUser($user)
   {
       $role = Role::findByName('player');
       $user->assignRole($role);
   }










}
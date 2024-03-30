<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Filters\UserFilter;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateUserRequest;


class UserController extends Controller
{
   //register
   public function register(Request $request)
   {
       try {
           $this->validateRegistrationData($request);

           $name = $this->generateName($request);

           $user = User::create([
               'name' => $name,
               'email' => $request->email,
               'password' => Hash::make($request->password),
           ]);

           $this->assignRoleToUser($user);

           return response()->json($user, 201);
       } catch (ValidationException $e) {
           return response()->json(['errors' => $e->errors()], 422);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Error al crear el usuario.'], 500);
       }
   }
/*
   private function validateRegistrationData(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'name' => 'nullable|unique:users',
           'email' => 'required|email|unique:users',
           'password' => 'required|regex:/^(?=.*[A-Z])(?=.*[!@#\$%\^&\*]).{9,}$/'
       ]);

       $validator->setAttributeNames([
           'email' => 'correo electrónico',
           'password' => 'contraseña'
       ]);

       $validator->setCustomMessages([
           'required' => 'El campo :attribute es obligatorio.',
           'email' => 'El campo :attribute debe ser una dirección de correo válida.',
           'unique' => 'Este :attribute ya está en uso.',
           'regex' => 'La :attribute debe contener al menos una mayúscula y un carácter especial y tener al menos 9 caracteres de longitud.'
       ]);

       $validator->validate();
   }
*/
   private function generateName(Request $request)
   {
       $name = $request->name ?: 'ANONYMOUS';

       if ($existingUser = User::where('name', $name)->first()) {
           throw ValidationException::withMessages(['name' => 'El nombre ya está en uso, intente con otro.']);
       }

       return $name;
   }

   private function assignRoleToUser($user)
   {
       $role = Role::findByName('player');
       $user->assignRole($role);
   }

   //login

   //logout

   //index -- lista de usuarios
   public function index(Request $request)
   {
       //$users = User::all();
       //return new UserCollection($users);
       
       $filter = new UserFilter();
       $queryItems = $filter->transform($request);
       $includeGames = $request->query('includeGames');
       $users = User::where($queryItems);
       if($includeGames) {
           $users = $users->with('games');
       }
       return new UserCollection($users->paginate()->appends($request->query()));

   }
   public function show(User $user)
   {
       //
       $includeGames = request()->query('includeGames');
       if ($includeGames) {
           return new UserResource($user->loadMissing('games'));
       }
       return new UserResource($user);
   }
   public function store(Request $request){
    return new UserResource(User::create($request->all()));
   }
   //getWorstPlayer

   //getBestPlater

   //update
   public function update(Request $request, User $user){
    $user->update($request->all());
   }
   //getPlayersRanking
}

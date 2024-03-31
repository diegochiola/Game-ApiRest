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
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateUserRequest;
use GuzzleHttp\Psr7\Response;


class UserController extends Controller
{
 //separate responsabilities, login logic -> passport controller

 //create user
 public function create(Request $request, $nickname)
 {
     $user = User::create([
         'name' => $request->name,
         'nickname' => $nickname,
         'email' => $request->email,
         'password' => Hash::make($request->password),
     ]);
     return $user;
 }

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
   public function store(StoreUserRequest $request){
    return new UserResource(User::create($request->all()));
   }
 
   public function update(UpdateUserRequest $request, User $user){
    $user->update($request->all());
   }
   public function updateName(Request $request, $id)
   {
       $user = Auth::user();
       if ($user->id != $id) {
           return response()->json(['error' => 'You are not authorized for this action'], 403);
       }

       $this->validate($request, [
           'name' => 'required',
           'nickname' => 'required'
       ]);
       $user->name = $request->input('name');
       $user->nickname = $request->input('nickname');
       $user->save();
       return response()->json(['message' => 'Name updated successfully!', 'user' => $user], 200);
   }
   //getPlayersRanking
   /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    //getWorstPlayer

   //getBestPlater

   //update
}

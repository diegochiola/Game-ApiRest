<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use App\Filters\GameFilter;
use Illuminate\Http\Request;
use App\Http\Resources\GameCollection;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;


class GameController extends Controller
{

    //logicas:
    //deletePlayerGames
    //throwDice
    //getPlayerGames

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        //$games = Game::all();
        //return new GameCollection($games);
        
        // Crea una instancia de GameFilter
        $filter = new GameFilter();
        // Transforma la solicitud de filtrado en una matriz de elementos de consulta
        $queryItems = $filter->transform($request);
        if(count($queryItems) == 0) {
            return new GameCollection(Game::paginate());
        } else {
            $games = Game::where($queryItems)->paginate();
            return new GameCollection($games->paginate()->appends($request->query()));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $userId)
    {
        // A partir del usser
        $user = User::find($userId);
       if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        //lanzamiento de dados
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $sum = $dice1 + $dice2;
        //operador ternario para saber si es true o false que se guarda en la variablw $won
        $won = $sum === 7 ? true : false;
        //nuevo registro
        $game = Game::create([
            'user_id' => $user->id,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'won' => $won
        ]);
        return response()->json(['message' => 'Game created successfully', 'game' => $game], 201);
    }
    //metodo para obtener games
    public function getGames($userId)
    {
        // A partir del usser
        $user = User::findOrFail($userId);
        // Obtengo all games asociados al user
        $games = $user->games;

        return response()->json($games, 200);
    }

    //porcentaje de wins para un user especifico
    public function percentageOfWins($userId) 
    {
        //buscar al user
        $user = User::find($userId);
        if (!$user) {
            return  response()->json(['error' => 'User not found'], 404);
        }
        $totalGames = $user->games()->count();
        $totalWins = $user->games()->where('won', true)->count();
        if ($totalGames === 0) { 
            $percentageOfWins = 0;
        } else { //si al menos jugo una
            $percentageOfWins = ($totalWins / $totalGames) * 100;
        }
        return response()->json([
            'user_id' => $user->id,
            'percentageOfWins' => $percentageOfWins,
            'total_games' => $totalGames,
            'total_wins' => $totalWins

        ]);
    }

    //percentage of wins by user
    private function calculatePercentageOfWins($userId)
{
    $totalGames = Game::where('user_id', $userId)->count();

    if ($totalGames === 0) {
        return 0;
    }

    $wonGames = Game::where('user_id', $userId)->where('won', true)->count();

    return ($wonGames / $totalGames) * 100;
}

//calcular el porcentaje de wins de todos los users
public function allUsersPercentageOfWins()
    {
        $users = User::all();
        $usersWithPercentage = [];
        foreach ($users as $user) {
            $winPercentage = $this->calculatePercentageOfWins($user->id);
            $usersWithPercentage[] = [
                'user' => $user,
                'win_percentage' => $winPercentage
            ];
        }
        return response()->json(['users' => $usersWithPercentage]);
    }
    //calcular el porcentaje total de wins en todos los games
    private function calculateTotalPercentageOfWins()
    {
        $totalGames = Game::count();

        if ($totalGames === 0) {
            return 0;
        }

        $wonGames = Game::where('won', true)->count();

        return ($wonGames / $totalGames) * 100;
    }
    //porcentaje total de wins en todos los games
    public function getTotalPercentageOfWins()
    {
        $percentage = $this->calculateTotalPercentageOfWins();

        return response()->json(['win_percentage' => $percentage]);
    }
    
    //user`s ranking
    public function ranking()
    {
        $users = User::all();
        $usersWithPercentage = [];
        foreach ($users as $user) {
            $winPercentage = $this->calculatePercentageOfWins($user->id);
            $usersWithPercentage[] = [
                'user' => $user,
                'win_percentage' => $winPercentage
            ];
        }
        $usersWithPercentageCollection = collect($usersWithPercentage);
        $usersWithPercentageSorted = $usersWithPercentageCollection->sortByDesc('win_percentage')->values()->all();

        return response()->json(['users' => $usersWithPercentageSorted]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        //
    }
    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    //destroy es para eliminar todos los juegos del user especifico
    public function destroy($userId)
    {
        //encontrar user por id
        $user = User::find($userId);
        //si el susuario no se encuentra
        if  (!$user) {
            return  response()->json(['error' => 'User not found'], 404);
        }
        //delete all games del user
        $user->games()->delete();
        return response()->json(['message' => 'All games for user deleted successfully!']);
    }
    
}

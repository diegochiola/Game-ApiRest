<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Resources\GameCollection;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;
use App\Filters\GameFilter;
use App\Models\User;

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
    public function destroy(Game $game)
    {
        //
    }
}

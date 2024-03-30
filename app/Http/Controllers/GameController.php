<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Resources\GameCollection;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;
use App\Filters\GameFilter;

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
    public function create()
    {
        //
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

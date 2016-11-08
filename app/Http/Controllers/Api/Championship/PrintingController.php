<?php

namespace App\Http\Controllers\Api\Championship;

use App\Http\Controllers\Controller;
use App\Models\Championship\Game;
use App\Models\Championship\Player;
use App\Models\Championship\PlayerRelation;
use App\Models\Championship\PlayerRelationable;
use App\Models\Championship\Team;
use App\Models\Championship\Tournament;
use Illuminate\Http\Request;
use App\Models\WpUser;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use App\Http\Requests\TournamentRequest;

class PrintingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd($_GET);
        return View::make('game.print');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function printGame(Game $game)
    {
        $playerList = $game->getPlayersInfoBy(['game'=>$game->id, 'order_by'=>'player_username']);
        return View::make('game.print')->with('playerList', $playerList);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private function returnPrintable($keys)
    {
//        $playerList = PlayerRelationable::playersRelationsToAnArrayOfObjectsOfTeamsAndTournamentsAndGames($keys);
//        $playerList = PlayerRelationable::getPlayersInfoBy($keys);
//        return $playerList;
    }

}

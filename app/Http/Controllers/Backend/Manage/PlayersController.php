<?php

namespace App\Http\Controllers\Backend\Manage;

use App\Models\Championship\IndividualPlayer;
use App\Models\Championship\Player;
use App\Models\Championship\PlayerRelation;
use App\Models\Championship\PlayerRelationable;
use App\Models\Championship\Team;
use App\Models\Championship\Tournament;
use App\Models\WpUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use App\Http\Requests\PlayerRequest;

class PlayersController extends Controller
{
//    protected $gamesDBConnection = "";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('game/player');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  Player  $player
     * @return \Illuminate\Http\Response
     */
    public function store(PlayerRequest $request)
    {
        $player = new Player();
        $player->username = $request['username'];
        $player->email = $request['email'];
        $player->name = $request['name'];
        $player->phone = $request['phone'];
        $player->updated_by =  $this->getUserId();
        $player->updated_on = Carbon::now("CST");
        $player->save();
        return redirect('manage/player')->with('success',"The Player ".$player->fresh()->name." was added");

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Player  $player
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Player $player)
    {
        dd("Are you trying to hack us? ip_address:".$_SERVER['REMOTE_ADDR']);
//        $updatedBy = $this->getUserId();
//        $updatedOn = Carbon::now("CST");
//        $toUpdate = array_merge($request->all(), [
//            'updated_by' => $updatedBy,
//            'updated_on' => $updatedOn
//        ] );
//        unset($toUpdate['_token']);
//        unset($toUpdate['_method']);
//        unset($toUpdate['id']);
//        unset($toUpdate['reset']);
//        unset($toUpdate['submit']);
//        Player::save($toUpdate);
    }

    /**
     * Display the specified resource.
     *
     * @param  Player  $player
     * @return \Illuminate\Http\Response
     */
    public function show(Player $player)
    {
        return View::make('game/player')->with("thePlayer", $player);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Player  $player
     * @return \Illuminate\Http\Response
     */
    public function edit(Player $player)
    {
        $pla = $player->getThisPlayerInfoBy();
        return View::make('game/player')->with("thePlayer", $pla);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param   Player  $player
     * @return \Illuminate\Http\Response
     */
    public function update(PlayerRequest $request, Player $player) //have to update it to my request
    {
        $theTeam = $theTournament = $theGame = '';
        $request =$request->toArray();
        if(isset($request['tournament_id'])) {
            $theTournament = $request['tournament_id'];
            unset($request['tournament_id']);
        }
        if(isset($request['team_id'])) {
            $theTeam = $request['team_id'];
            unset($request['team_id']);
        }
        if(isset($request['game_id'])) {
            $theGame = $request['game_id'];
            unset($request['game_id']);
        }
        unset($request['_token']);
        unset($request['_method']);
        unset($request['submit']);
        $request['updated_by'] = $this->getUserId();
        $request['updated_on'] = Carbon::now("CST");
        $player->name = $request['name'];
        $player->username = $request['username'];
        $player->email = $request['email'];
        $player->phone = $request['phone'];
        $player->save();
        $playerArray = $player->getThisPlayerInfoBy();
        $parameters = [];
        $result =[];
        $parameters['player'] = $playerArray['player_id'];
//        var_dump($playerArray);
//        var_dump($theTeam);
//        var_dump($theTournament);
//        var_dump($theGame);
        if($theTeam != '' and $theTeam != $playerArray['team_id']) {
            $parameters['team'] = $theTeam;
        }
        if($theTournament != '' and $theTournament != $playerArray['tournament_id']) {
            $parameters['tournament'] = $theTournament;
        }
        if($theGame != '' and $theGame != $playerArray['game_id']) {
            $parameters['game'] = $theGame;
        }
//        var_dump($parameters);
//        var_dump(count($parameters));
        if(count($parameters) > 1){
            $result = Player::createRelation($parameters);
        }
//        var_dump($result);
//        die();

        $playerArray = $player->getThisPlayerInfoBy();
        $success = '';
        $errors = '';
            if(isset($result) and $result!=[]) {
                foreach ($result as $key => $val) {
//                    dd($result);
                    if ($val) {
                        $success .= "The player " . $playerArray['player_name'] . " was successfully attached to the " . $key . "\r\n";
                    }else{
                        $errors .= "The player " . $playerArray['player_name'] . " couldn't be attached to the " . $key ." s/he could be already assigned to this ".$key. "\r\n";
                    }
                }
            }
            if($success!='' and $errors!=''){
                return Redirect::back()
                    ->with('success', $success)
                    ->with('error', $errors)
                    ->with("thePlayer", $playerArray);
            }elseif ($success!=''){
                return Redirect::back()
                    ->with('success', $success)
                    ->with("thePlayer", $playerArray);
            }else{
                return Redirect::back()
                    ->with('error', $errors)
                    ->with("thePlayer", $playerArray);
            }
    }

//    /**
//     * Remove the specified player from the team and move it to the single player list.
//     *
//     * @param  Player  $player
//     * @return \Illuminate\Http\Response
//     */
//    public function assignPlayerToTeam($player, $relation) //todo
//    {
//
//        if(Team::find($team_id)->isTeamNotFull()){
//            $playerToChange = PlayerRelation::having('player_relations.player_id', '=', $player['player_id'])
//                ->having('player_relations.relation_id', '=', $player['team_id'])
//                ->having('player_relations.relation_type', '=', ;
//            $playerToChange->relation_id = $team_id;
//            $playerToChange->save();
//        }else{
//            return Redirect::back()->withErrors(array('msg'=>'The team has the maximum amount of players. Please pick a different team.'));
//        }
//
//    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  Player  $player, Team $team
     * @return \Illuminate\Http\Response
     */
    public function remove(Player $player, Team $team)
    {
        PlayerRelation::where('player_id', '=', $player->getRouteKey())
            ->where('relation_id', '=', $team->getRouteKey())
            ->where('relation_type', '=', Team::class)
            ->delete();
        return Redirect::back();
    }
    /**
     * Destroy the specified resource from storage.
     *
     * @param  Player  $player
     * @return \Illuminate\Http\Response
     */
    public function destroy(Player $player)
    {
        PlayerRelation::where('player_id', '=', $player->getRouteKey())->delete();
        $player->where('id', $player->getRouteKey())->delete();
        return Redirect::back();
    }
    /**
     * Display the specified resource.
     *
     * @param  Request $ids
     * @return \Illuminate\Http\Response
     */
    public function filter(Request $ids)
    {
        $filterArray = [];
        if(isset($ids->team_sort) and trim($ids->team_sort) != "" and trim($ids->team_sort) != "---" and $ids->team_sort!=[]) {
            $filterArray['team'] = trim($ids->team_sort);
        }
        if(isset($ids->tournament_sort) and trim($ids->tournament_sort) != "" and trim($ids->tournament_sort) != "---" and $ids->tournament_sort!=[]) {
            $filterArray['tournament'] = trim($ids->tournament_sort);
        }
        if(isset($ids->game_sort) and trim($ids->game_sort) != "" and trim($ids->game_sort) != "---" and $ids->game_sort!=[]) {
            $filterArray['game'] = trim($ids->game_sort);
        }
        $players = new Player();
        $playerList = $players->getPlayersInfoBy($filterArray);
        return View::make('game/player')->with("players_filter", $playerList)->with('sorts',$ids);

    }
}


@extends('game.base')

@section('css')
    .deletingForms, .toForms{
        display:inline-block;
    }
    .teamName{
        display:inline-block;
        min-width:300px;
    }
@endsection
@section('content')
    @if(!isset($maxNumOfPlayers)) {{--*/ $maxNumOfPlayers = 5; /*--}}@endif
    @if(isset($tournaments) || $tournaments != [])
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(isset($cont_updated) and  $cont_updated)
            <div class="alert alert-success"><strong>Success!</strong> You have updated this Team.</div>
        @endif
        @if(isset($theTeam->name))
            {{ Form::open(array('id' => "teamForm", 'action' => array('Backend\Manage\TeamsController@update', $theTeam->id))) }}
        @else
            {{  Form::open(array('id' => "teamForm", 'action' => array('Backend\Manage\TeamsController@create'))) }}
        @endif
        <div class="form-group">
            @if(isset($theTeam->name))
                <input name="_method" type="hidden" value="PUT">
            @else
                <input name="_method" type="hidden" value="POST">
            @endif
            <div class="form-group">
                <label for="name" style="width:120px; text-align:right;">Team Name: </label> &nbsp; <input type="text" name="name" id="name" style="width:350px; text-align:left;" placeholder="The name of the team" @if(isset($theTeam->name))value="{{$theTeam->name}}"@endif/>
            </div>
                <div class="form-group">
                    <label for="emblem" style="width:120px; text-align:right;">Team Emblem: </label> &nbsp; <input type="text" name="emblem" id="emblem" style="width:350px; text-align:left;" placeholder="The url to the emblem of the team" @if(isset($theTeam->emblem))value="{{$theTeam->emblem}}"@endif/>
                </div>
                <div class="form-group">
                    <label for="captain" style="width:120px; text-align:right;">Team Captain: </label>
                    <select type="text" name="captain" id="captain"  style="width:350px; text-align:left;">
                        @if(isset($theTeam->name))
                            <option>---</option>
                        @else
                            <option>If the team is a new team please add players to choose a captain</option>
                        @endif
                        @foreach($players as $key => $player)
                            @if(isset($theTeam->name))
                                @if($theTeam->id == $player['team_id'])
                                    <option value="{{$player['player_id']}}" @if( isset($theTeam->captain) and $theTeam->captain==$player['player_id']) selected @endif>{{ $player['username'] }}</div>
                                @endif
                            @endif
                        @endforeach
                    </select>
                </div>
            <div class="form-group">
                <label for="tournament_id" style="width:120px; text-align:right;">Team Tournament ID: </label> &nbsp;
                <select type="text" name="tournament_id" id="tournament_id"  style="width:350px; text-align:left;">
                    <option>---</option>
                    @foreach($tournaments as $key => $tournament)
                        <option value="{{$tournament['id']}}" @if(isset($theTeam['tournament_id']) and $theTeam['tournament_id'] == $tournament['id']) selected @endif>{{ $tournament['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
            <div class="form-group">
                <input type="submit" name="submit" id="submit" class='btn btn-default' value="Save">
                {{ Html::link('/manage/team/', 'Clear', array('id' => 'reset', 'class' => 'btn btn-default'))}}
            </div>

        </div>
        </form>
        {{ Form::open(array('id' => "teamFilter", 'action' => array('Backend\Manage\TeamsController@filter'))) }}
        <input name="_method" type="hidden" value="POST">
        <label for="game_sort" style="width:120px; text-align:right;">Show options only for this Game: </label> <select name="game_sort" id="game_sort" style="width:350px; text-align:right;">

            <option> --- </option>
            @foreach($games as $g)
                <option id="t_option{{$g['id']}}" value="{{$g['id']}}" class="gameSelector"
                        @if(isset($sorts) and isset($sorts->game_sort) and ($g['id'] == $sorts->game_sort or $g['name'] == $sorts->game_sort)) selected="selected" @endif
                >{{$g['name']}}</option>
            @endforeach
        </select>
        <br />
        <label for="tournament_sort" style="width:180px; text-align:right;">Filter by Tournament: </label> <select name="tournament_sort" id="tournament_sort" style="width:280px; text-align:left;">
            <option> --- </option>
            @foreach($tournaments as $g)
                <option id="t_option{{$g['game_id']}}_{{$g['id']}}" value="{{$g['id']}}"
                        @if(isset($sorts) and isset($sorts->tournament_sort) and ($g['id'] == $sorts->tournament_sort or $g['name'] == $sorts->tournament_sort)) selected="selected" @endif
                >{{$g['name']}}</option>
            @endforeach
        </select>
        {!! Form::submit( 'Filter', array('class'=>'btn btn-default list fa fa-search', 'style'=>'width:70px; text-align:center;')) !!}
        {{ Form::close() }}
        <ul id="listOfTeams" class="listing">
            @if(!isset($teams_filter))
                @if(!isset($teams) || $teams == [])
                    <li>There are no Teams yet</li>
                @else
                    @foreach($teams as $id => $team)
                        @include('game.partials.teams_displayer')
                    @endforeach
                @endif
            @elseif($teams_filter == [] or $teams_filter == [ ])
                <li>There are no results with the selected filter.</li>
            @else
                <li>Filtered results: </li>
                @foreach($teams_filter as $id => $team)
                    @include('game.partials.teams_displayer')
                @endforeach
            @endif
        </ul>

    @else
        <h1>Sorry, no tournaments where found on the database!, please create a tournament before proceding with a team</h1>
        {{ Html::link('/manage/tournament/', 'Create a Tournament', array('id' => 'new_tournament', 'class' => 'btn btn-default'))}}
    @endif

@endsection
@section('js')
    $(document).ready(function() {
        $('#game_sort').on("change", function() {
            var val_g = $('#game_sort option:selected').val();
            var d_id = $('#game_sort option[value="'+val_g+'"]').attr("id");
            $('#tournament_sort option').prop("disabled", true);
            $('#tournament_sort option[id^="'+d_id+'_"]').prop("disabled", false).attr('disabled',false).removeProp('disabled').removeAttr("disabled");
            $('#tournament_sort option[id^="'+d_id+'_"]:first-child').attr("selected","selected");
            $('#tournament_sort').select2({
                allowClear: true
            });
        });
        $('.fa-times').click(function() {
            var conf = confirm('Are you sure? This will delete the team and move the players to the Individual Players list');
            if (conf) {
                var url = $(this).attr('href');
                $(document).load(url);
            }else{
                return false;
            }
        });
        $('.fa-times2').click(function() {
            var conf = confirm('Are you sure? This will remove the team and players from the database for all tournaments and games');
            if (conf) {
                var url = $(this).attr('href');
                $(document).load(url);
            }else{
                return false;
            }
        });
    });
@endsection
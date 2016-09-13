<?php

namespace App\Models\Championship;

use Illuminate\Database\Eloquent\Model;

class Player_Tournament extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mysql_champ';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'player_tournament';

    /**
     * @var array
     */
    protected $fillable = ['player_id', 'tournament_id'];


    /**
     * Get player's team
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tournaments()
    {
        return $this->hasMany('App\Models\Championship\Tournaments');
    }
    /**
     * Get player's team
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function players()
    {
        return $this->hasMany('App\Models\Championship\Players');
    }
}

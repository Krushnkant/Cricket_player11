<form class="form-valide" action="" id="MatchPlayerform" method="post">
    <div id="cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}
    
    <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12  justify-content-center mt-2">
        

        <div class="form-group">
            <label class="col-form-label" for="match_player1"> Team 1 Player ({{ $match->team1->name }}) <span class="text-danger">*</span>
            </label>
            <input type="hidden" name="team1_id" value="{{ $match->team1->id }}"> 
            <select class="form-control" id="match_player1" name="match_player1[]" multiple>
                @foreach($seriesteamplayer1 as $serie)
                <option value="{{ $serie->player->id }}" @if(isset($matchplayer1) && in_array($serie->player->id,$matchplayer1)) selected @endif >{{ $serie->player->name }}</option>
                @endforeach
            </select>
            <label id="match_player1-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="match_player2">Team 2 Player ({{ $match->team2->name }}) <span class="text-danger">*</span>
            </label>
            <input type="hidden" name="team2_id" value="{{ $match->team2->id }}"> 
            <select class="form-control" id="match_player2" name="match_player2[]" multiple>
                @foreach($seriesteamplayer2 as $serie)
                <option value="{{ $serie->player->id }}" @if(isset($matchplayer2) && in_array($serie->player->id,$matchplayer2)) selected @endif>{{ $serie->player->name }}</option>
                @endforeach
            </select>
            <label id="match_player2-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>

        
    </div>

    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 justify-content-center mt-4">
        <input type="hidden" name="match_id" value="{{ isset($match)?($match->id):'' }}">
        <button type="button" class="btn btn-outline-primary mt-4" id="save_newmatchplayerBtn" data-action="{{ isset($match)? 'update' : 'add' }}" >Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
        <button type="button" class="btn btn-primary mt-4" id="save_closematchplayerBtn" data-action="{{ isset($match)? 'update' : 'add' }}">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
    </div>
</form>

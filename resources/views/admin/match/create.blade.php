<form class="form-valide" action="" id="Matchform" method="post">
    <div id="cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}

    <div class="col-lg-8 col-md-8 col-sm-10 col-xs-12  justify-content-center mt-2">
        <div class="form-group">
            <label class="col-form-label" for="serie_id">Series <span class="text-danger">*</span>
            </label>
            <select class="form-control" id="serie_id" name="serie_id">
                <option></option>
                @foreach($series as $serie)
                <option value="{{ $serie->id }}" @if(isset($match) && ($serie->id == $match->series_id)) selected @endif >{{ $serie->name }}</option>
                @endforeach
            </select>
            <label id="serie_id-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="team1_id"> Teams 1 <span class="text-danger">*</span>
            </label>
            <select class="form-control" id="team1_id" name="team1_id">
                <option></option>
                @foreach($teams as $team)
                <option value="{{ $team->id }}" @if(isset($match) && ($team->id == $match->team1_id)) selected @endif>{{ $team->name }}</option>
                @endforeach
            </select>
            <label id="team1_id-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="team2_id"> Teams 2 <span class="text-danger">*</span>
            </label>
            <select class="form-control" id="team2_id" name="team2_id">
                <option></option>
                @foreach($teams as $team)
                <option value="{{ $team->id }}" @if(isset($match) && ($team->id == $match->team2_id)) selected @endif>{{ $team->name }}</option>
                @endforeach
            </select>
            <label id="team2_id-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="match_type">Match Type <span class="text-danger">*</span>
            </label>
            <select class="form-control" id="match_type" name="match_type">
                <option></option>
                <option value="1" @if(isset($match) && (1 == $match->match_type)) selected @endif>T20</option>
                <option value="2" @if(isset($match) && (2 == $match->match_type)) selected @endif>ODI</option>
                <option value="3" @if(isset($match) && (3 == $match->match_type)) selected @endif>Both</option>
            </select>
            <label id="match_type-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="stadium_id">Stadium <span class="text-danger">*</span>
            </label>
            <select class="form-control" id="stadium_id" name="stadium_id">
                <option></option>
                @foreach($stadiums as $stadium)
                <option value="{{ $stadium->id }}" @if(isset($match) && ($stadium->id == $match->stadium_id)) selected @endif>{{ $stadium->name }}</option>
                @endforeach
            </select>
            <label id="stadium_id-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="Question">Start Date <span class="text-danger">*</span>
            </label>
            <input type="datetime-local" class="form-control input-flat"  id="start_date" name="start_date" value="<?php if(isset($match)){ echo Date('Y-m-d\TH:i',$stadium->start_date); } ?>">
            <label id="start_date-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
        </div>


        @if(isset($match))
            <div class="form-group">
                <label class="col-form-label" for="winner_team_id">Winner Team 
                </label>
                <select class="form-control" id="winner_team_id" name="winner_team_id">
                    <option></option>
                    @foreach($winnerteams as $winner)
                    <option value="{{ $winner->id }}" @if(isset($match) && ($winner->id == $match->winner_team_id)) selected @endif>{{ $winner->name }}</option>
                    @endforeach
                </select>
                <label id="winner_team_id-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
            </div>

            <div class="form-group">
                <label class="col-form-label" for="team1_score">Team 1 Score 
                </label>
                <input type="text" class="form-control input-flat"  id="team1_score" name="team1_score" value="{{ isset($match)?($match->team1_score):'' }}">
                <label id="team1_score-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
            </div>

            <div class="form-group">
                <label class="col-form-label" for="team2_score">Team 2 Score 
                </label>
                <input type="text" class="form-control input-flat"  id="team2_score" name="team2_score" value="{{ isset($match)?($match->team2_score):'' }}">
                <label id="team2_score-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
            </div>

            <div class="form-group">
                <label class="col-form-label" for="winning_statement">Winning Statement 
                </label>
                <input type="text" class="form-control input-flat"  id="winning_statement" name="winning_statement" value="{{ isset($match)?($match->winning_statement):'' }}">
                <label id="winning_statement-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
            </div>
        @endif
        
    </div>

    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 justify-content-center mt-4">
        <input type="hidden" name="match_id" value="{{ isset($match)?($match->id):'' }}">
        <button type="button" class="btn btn-outline-primary mt-4" id="save_newmatchBtn" data-action="{{ isset($match)? 'update' : 'add' }}" >Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
        <button type="button" class="btn btn-primary mt-4" id="save_closematchBtn" data-action="{{ isset($match)? 'update' : 'add' }}">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
    </div>
</form>

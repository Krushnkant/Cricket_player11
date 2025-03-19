@extends('admin.layout')

@section('content')

    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Match</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->
    <input type="hidden" id="match_id" value="{{ isset($id)?$id:0 }}">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">

                        @if(isset($action) && $action=='list')
                            <div class="action-section">
                                <div class="d-flex">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.match.list')->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                    <button type="button" class="btn btn-primary" id="AddMatchBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="Matchformtable" class="table zero-configuration customNewtable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Series</th>
                                        <th>Team</th>
                                        <th>Stadium</th>
                                        <th>Match Type</th>
                                        <th>Start date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>No.</th>
                                        <th>Series</th>
                                        <th>Team</th>
                                        <th>Stadium</th>
                                        <th>Match Type</th>
                                        <th>Start date</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                        @if(isset($action) && $action=='create')
                            @include('admin.match.create')
                        @endif

                        @if(isset($action) && $action=='edit')
                            @include('admin.match.create')
                        @endif

                        @if(isset($action) && $action=='matchplayer')
                            @include('admin.match.matchplayer')
                        @endif

                        @if(isset($action) && $action=='matchcommentry')
                        <div class="table-responsive">
                            <table id="MatchCommentrytable" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Ball No.</th>
                                    <th>Batsman</th>
                                    <th>Bowler</th>
                                    <th>Ball Type</th>
                                    <th>Run</th>
                                    <th>Ball Status</th>
                                    <th>Out</th>
                                    <th>Commentry</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Ball No.</th>
                                    <th>Batsman</th>
                                    <th>Bowler</th>
                                    <th>Ball Type</th>
                                    <th>Run</th>
                                    <th>Ball Status</th>
                                    <th>Out</th>
                                    <th>Commentry</th>  
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif

                        @if(isset($action) && $action=='matchscoreboard')
                        <div class="table-responsive">
                            <table id="MatchScoreboardtable" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Player</th>
                                    <th>Ball</th>
                                    <th>Run</th>
                                    <th>Four</th>
                                    <th>Six</th>
                                    <th>Strike Rate</th>
                                    <th>Over</th>
                                    <th>Ball Run</th>
                                    <th>Maiden</th>
                                    <th>Wicket</th>
                                    <th>Wide</th>
                                    <th>Noball</th>
                                    <th>Economy Rate</th>
                                    <th>Fantasy Point</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Player</th>
                                    <th>Ball</th>
                                    <th>Run</th>
                                    <th>Four</th>
                                    <th>Six</th>
                                    <th>Strike Rate</th>
                                    <th>Over</th>
                                    <th>Ball Run</th>
                                    <th>Maiden</th>
                                    <th>Wicket</th>
                                    <th>Wide</th>
                                    <th>Noball</th>
                                    <th>Economy Rate</th>
                                    <th>Fantasy Point</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeletematchModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Match</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Match?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemovematchSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- Social Platform JS start -->
<script type="text/javascript">

$(document).ready(function() {
    matchs_table(true);
    match_commentries(true);
    match_scoreboard(true);

});

$('#serie_id').select2({
    width: '100%',
    placeholder: "Select ...",
    allowClear: true
});

$('#team1_id').select2({
    width: '100%',
    placeholder: "Select ...",
    allowClear: true
});

$('#team2_id').select2({
    width: '100%',
    placeholder: "Select ...",
    allowClear: true
});

$('#match_type').select2({
    width: '100%',
    placeholder: "Select ...",
    allowClear: true
});

$('#stadium_id').select2({
    width: '100%',
    placeholder: "Select ...",
    allowClear: true
});

$('#winner_team_id').select2({
    width: '100%',
    placeholder: "Select ...",
    allowClear: true
});

$('#match_player1').select2({
    width: '100%',
    multiple: true,
    placeholder: "Select...",
    allowClear: true,
    autoclose: false,
    closeOnSelect: false,
});

$('#match_player2').select2({
    width: '100%',
    multiple: true,
    placeholder: "Select...",
    allowClear: true,
    autoclose: false,
    closeOnSelect: false,
});


$('body').on('click', '#AddMatchBtn', function () {
    location.href = "{{ route('admin.match.add') }}";
});

$('body').on('click', '#save_newmatchBtn', function () {
    save_match($(this),'save_close');
});

$('body').on('click', '#save_closematchBtn', function () {
    save_match($(this),'save_new');
});

function save_match(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();
    $('#name-error').hide().text("");
    $('#custom_fields-error').hide().text("");

    var action  = $(btn).attr('data-action');
    var formData = new FormData($("#Matchform")[0]);
    formData.append('action',action);


    $.ajax({
            type: 'POST',
            url: "{{ route('admin.match.save') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status == "failed") {
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();

                    if (res.errors.start_date) {
                        $('#start_date-error').show().text(res.errors.start_date);
                    } else {
                        $('#start_date-error').hide();
                    }

                    if (res.errors.serie_id) {
                        $('#serie_id-error').show().text(res.errors.serie_id);
                    } else {
                        $('#serie_id-error').hide();
                    }

                    if (res.errors.team1_id) {
                        $('#team1_id-error').show().text(res.errors.team1_id);
                    } else {
                        $('#team1_id-error').hide();
                    }

                    if (res.errors.team2_id) {
                        $('#team2_id-error').show().text(res.errors.team2_id);
                    } else {
                        $('#team2_id-error').hide();
                    }

                    if (res.errors.match_type) {
                        $('#match_type-error').show().text(res.errors.match_type);
                    } else {
                        $('#match_type-error').hide();
                    }

                    if (res.errors.stadium_id) {
                        $('#stadium_id-error').show().text(res.errors.stadium_id);
                    } else {
                        $('#stadium_id-error').hide();
                    }

                    
                }
                else if (res.status == 200) {
                    if (btn_type == 'save_close') {
                        $(btn).prop('disabled', false);
                        $(btn).find('.loadericonfa').hide();
                        location.href = "{{ route('admin.match.list')}}";
                        if (res.action == 'add') {
                            toastr.success("Match Added", 'Success', {timeOut: 5000});
                        }
                        if (res.action == 'update') {
                            toastr.success("Match Updated", 'Success', {timeOut: 5000});
                        }
                    }
                    if (btn_type == 'save_new') {
                        $(btn).prop('disabled', false);
                        $(btn).find('.loadericonfa').hide();
                        location.href = "{{ route('admin.match.add')}}";
                        if (res.action == 'add') {
                            toastr.success("Match Added", 'Success', {timeOut: 5000});
                        }
                        if (res.action == 'update') {
                            toastr.success("Match Updated", 'Success', {timeOut: 5000});
                        }
                    }
                }
            },
            error: function (data) {
                console.log(data);
                $(btn).prop('disabled', false);
                $(btn).find('.loadericonfa').hide();
                toastr.error("Please try again", 'Error', {timeOut: 5000});
            }
    });
}



$('body').on('click', '#save_newmatchplayerBtn', function () {
    save_matchplayer($(this),'save_close');
});

$('body').on('click', '#save_closematchplayerBtn', function () {
    save_matchplayer($(this),'save_new');
});

function save_matchplayer(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();
    $('#name-error').hide().text("");
    $('#custom_fields-error').hide().text("");

    var action  = $(btn).attr('data-action');
    var formData = new FormData($("#MatchPlayerform")[0]);
    formData.append('action',action);


    $.ajax({
            type: 'POST',
            url: "{{ route('admin.matchplayer.save') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status == "failed") {
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();

                    if (res.errors.match_player1) {
                        $('#match_player1-error').show().text(res.errors.match_player1);
                    } else {
                        $('#match_player1-error').hide();
                    }
                    if (res.errors.match_player2) {
                        $('#match_player2-error').show().text(res.errors.match_player2);
                    } else {
                        $('#match_player2-error').hide();
                    }
                }
                else if (res.status == 200) {
                    if (btn_type == 'save_close') {
                        $(btn).prop('disabled', false);
                        $(btn).find('.loadericonfa').hide();
                        location.href = "{{ route('admin.match.list')}}";
                        if (res.action == 'add') {
                            toastr.success("Match Player Added", 'Success', {timeOut: 5000});
                        }
                        if (res.action == 'update') {
                            toastr.success("Match Player Updated", 'Success', {timeOut: 5000});
                        }
                    }
                    if (btn_type == 'save_new') {
                        $(btn).prop('disabled', false);
                        $(btn).find('.loadericonfa').hide();
                       // location.href = "{{ route('admin.match.add')}}";
                        if (res.action == 'add') {
                            toastr.success("Match Player Added", 'Success', {timeOut: 5000});
                        }
                        if (res.action == 'update') {
                            toastr.success("Match Player Updated", 'Success', {timeOut: 5000});
                        }
                        location.reload();
                    }
                }
            },
            error: function (data) {
                console.log(data);
                $(btn).prop('disabled', false);
                $(btn).find('.loadericonfa').hide();
                toastr.error("Please try again", 'Error', {timeOut: 5000});
            }
    });
}

function matchs_table(is_clearState=false){
    if(is_clearState){
        $('#Matchformtable').DataTable().state.clear();
    }

    $('#Matchformtable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        'stateSave': function(){
            if(is_clearState){
                return false;
            }
            else{
                return true;
            }
        },
        "ajax":{
            "url": "{{ url('admin/allmatchlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}'},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "10%", "targets": 0 },
            { "width": "15%", "targets": 1 },
            { "width": "15%", "targets": 2 },
            { "width": "15%", "targets": 3 },
            { "width": "13%", "targets": 4 },
            { "width": "12%", "targets": 5 },
            { "width": "20%", "targets": 6 },
        ],
        "columns": [
            {data: 'id', question: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'series', name: 'series', orderable: false},
            {data: 'team', name: 'team', class: "text-left multirow" , orderable: false},
            {data: 'stadium', name: 'stadium', class: "text-left multirow" , orderable: false},
            {data: 'match_type', name: 'match_type', class: "text-left multirow" , orderable: false},
            {data: 'start_date', name: 'start_date', class: "text-left multirow" },
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

function match_commentries(is_clearState=false){
    if(is_clearState){
        $('#MatchCommentrytable').DataTable().state.clear();
    }
    var match_id = $('#match_id').val();
    $('#MatchCommentrytable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        'stateSave': function(){
            if(is_clearState){
                return false;
            }
            else{
                return true;
            }
        },
        "ajax":{
            "url": "{{ url('admin/allmatchcommentrylist') }}" + "/" + match_id,
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}'},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "5px", "targets": 0 },
            { "width": "10px", "targets": 1 },
            { "width": "30px", "targets": 2 },
            { "width": "30px", "targets": 3 },
            { "width": "10px", "targets": 4 },
            { "width": "10px", "targets": 5 },
            { "width": "10px", "targets": 6 },
            { "width": "10px", "targets": 7 },
            { "width": "200px", "targets": 8 }
        ],
        "columns": [
            {data: 'id', question: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'ball_number', name: 'ball_number', orderable: false},
            {data: 'batsman', name: 'batsman', class: "text-left multirow" , orderable: false},
            {data: 'bowler', name: 'bowler', class: "text-left multirow" , orderable: false},
            {data: 'ball_type', name: 'ball_type', class: "text-left multirow" , orderable: false},
            {data: 'run', name: 'run', class: "text-left multirow" , orderable: false},
            {data: 'ball_status', name: 'ball_status', class: "text-left multirow" , orderable: false},
            {data: 'out', name: 'out', class: "text-left multirow" , orderable: false},
            {data: 'commentry', name: 'commentry', class: "text-left multirow" , orderable: false},
        ]
    });
}

function match_scoreboard(is_clearState=false){
    if(is_clearState){
        $('#MatchScoreboardtable').DataTable().state.clear();
    }
    var match_id = $('#match_id').val();
    $('#MatchScoreboardtable').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        'stateSave': function(){
            if(is_clearState){
                return false;
            }
            else{
                return true;
            }
        },
        "ajax":{
            "url": "{{ url('admin/allmatchscoreboardlist') }}" + "/" + match_id,
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}'},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "5px", "targets": 0 },
            { "width": "30px", "targets": 1 },
            { "width": "10px", "targets": 2 },
            { "width": "10px", "targets": 3 },
            { "width": "10px", "targets": 4 },
            { "width": "10px", "targets": 5 },
            { "width": "10px", "targets": 6 },
            { "width": "10px", "targets": 7 },
            { "width": "10px", "targets": 8 },
            { "width": "10px", "targets": 9 },
            { "width": "10px", "targets": 10 },
            { "width": "10px", "targets": 11 },
            { "width": "10px", "targets": 12 },
            { "width": "10px", "targets": 13 },
            { "width": "10px", "targets": 14 },
        ],
        "columns": [
            {data: 'id', question: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'player_id', name: 'player_id', orderable: false},
            {data: 'ball', name: 'ball', class: "text-left multirow" , orderable: false},
            {data: 'run', name: 'run', class: "text-left multirow" , orderable: false},
            {data: 'four', name: 'four', class: "text-left multirow" , orderable: false},
            {data: 'six', name: 'six', class: "text-left multirow" , orderable: false},
            {data: 'strike_rate', name: 'strike_rate', class: "text-left multirow" , orderable: false},
            {data: 'over', name: 'over', class: "text-left multirow" , orderable: false},
            {data: 'ball_run', name: 'ball_run', class: "text-left multirow" , orderable: false},
            {data: 'maiden', name: 'maiden', class: "text-left multirow" , orderable: false},
            {data: 'wicket', name: 'wicket', class: "text-left multirow" , orderable: false},
            {data: 'wide', name: 'wide', class: "text-left multirow" , orderable: false},
            {data: 'noball', name: 'noball', class: "text-left multirow" , orderable: false},
            {data: 'economy_rate', name: 'economy_rate', class: "text-left multirow" , orderable: false},
            {data: 'fantasy_point', name: 'fantasy_point', class: "text-left multirow" , orderable: true},
        ]
    });
}

$('body').on('click', '#editmatchBtn', function () {
    var match_id = $(this).attr('data-id');
    var url = "{{ url('admin/match') }}" + "/" + match_id + "/edit";
    window.open(url,"_blank");
});

$('body').on('click', '#editmatchplayerBtn', function () {
    var matchplayer_id = $(this).attr('data-id');
    var url = "{{ url('admin/matchplayer') }}" + "/" + matchplayer_id;
    window.open(url,"_blank");
});

$('body').on('click', '#viewMatchCommentriesBtn', function () {
    var match_id = $(this).attr('data-id');
    var url = "{{ url('admin/matchcommentry') }}" + "/" + match_id;
    window.open(url,"_blank");
});

$('body').on('click', '#viewMatchScoreboardsBtn', function () {
    var match_id = $(this).attr('data-id');
    var url = "{{ url('admin/matchscoreboard') }}" + "/" + match_id;
    window.open(url,"_blank");
});



$('body').on('click', '#deletematchBtn', function (e) {

    // e.preventDefault();
    var match_id = $(this).attr('data-id');
    $("#DeletematchModal").find('#RemovematchSubmit').attr('data-id',match_id);
});

$('#DeletematchModal').on('hidden.bs.modal', function () {
    $(this).find("#RemovematchSubmit").removeAttr('data-id');
});

$('body').on('click', '#RemovematchSubmit', function (e) {
    $('#RemovematchSubmit').prop('disabled',true);
    $(this).find('.removeloadericonfa').show();
    e.preventDefault();
    var match_id = $(this).attr('data-id');
    
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/match') }}" +'/' + match_id +'/delete',
        success: function (res) {
            if(res.status == 200){
                $("#DeletematchModal").modal('hide');
                $('#RemovematchSubmit').prop('disabled',false);
                $("#RemovematchSubmit").find('.removeloadericonfa').hide();
                matchs_table();
                toastr.success("match Deleted",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $("#DeletematchModal").modal('hide');
                $('#RemovematchSubmit').prop('disabled',false);
                $("#RemovematchSubmit").find('.removeloadericonfa').hide();
                matchs_table();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#DeletematchModal").modal('hide');
            $('#RemovematchSubmit').prop('disabled',false);
            $("#RemovematchSubmit").find('.removeloadericonfa').hide();
            matchs_table();
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$('body').on('click', '#countFantasyPoints', function (e) {
    $('#countFantasyPoints').prop('disabled',true);
    $(this).find('#fantasy_btn_icon').hide();
    $(this).find('#fantasy_btn_loader').show();
    e.preventDefault();
    var match_id = $(this).attr('data-id');
    
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/match') }}" +'/' + match_id +'/countfantaypoint',
        success: function (res) {
            if(res.status == 200){
                $('#countFantasyPoints').prop('disabled',false);
                $(this).find('#fantasy_btn_icon').show();
                $(this).find('#fantasy_btn_loader').hide();
                matchs_table();
                toastr.success("Fantasy Points has been Calculated successfully",'Success',{timeOut: 5000});
            }

            if(res.status == 404 || res.status == 400){
                $('#countFantasyPoints').prop('disabled',false);
                $(this).find('#fantasy_btn_icon').show();
                $(this).find('#fantasy_btn_loader').hide();
                matchs_table();
                toastr.error(res.msg,'Error',{timeOut: 5000});
            }
            
        },
        error: function (data) {
            $('#countFantasyPoints').prop('disabled',false);
            $(this).find('#fantasy_btn_icon').show();
            $(this).find('#fantasy_btn_loader').hide();
            matchs_table();
            toastr.error("Something went wrong. Please try again",'Error',{timeOut: 5000});
        }
    });
});
</script>
<!-- Social Platform JS end -->
@endsection


@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Player</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{--<h4 class="card-title">User List</h4>--}}

                        <div class="action-section row">
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url',\Illuminate\Support\Facades\Route::currentRouteName())->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#playerModel" id="AddplayerBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                
                            </div>
                            
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_player" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Player Type</th>
                                    <th>Batting Style</th>
                                    <th>Bowling Style</th>
                                    <th>Bowling Arm</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Other</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Player Type</th>
                                    <th>Batting Style</th>
                                    <th>Bowling Style</th>
                                    <th>Bowling Arm</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Other</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="playerModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="playerform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add Player</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control input-flat" id="country_id" name="country_id" placeholder="" value="{{ $id }}">
                        
                        
                        <div class="form-group ">
                            <label class="col-form-label" for="name">Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="name" name="name" placeholder="">
                            <div id="name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="thumb_img">Image
                            </label>
                            <input type="file" class="form-control-file" id="thumb_img" onchange="" name="thumb_img">
                            <div id="thumb_img-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            <img src="{{ asset('photos/default_avatar.jpg') }}" class="" id="thumb_img_image_show" height="50px" width="50px" style="margin-top: 5px;width:50px;">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="player_type">Player Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="player_type" name="player_type">
                                <option></option>
                                <option value="1">Batsman</option>
                                <option value="2">Bowler</option>
                                <option value="3">Wk Batsman</option>
                                <option value="4">All rounder</option>
                            </select>
                            <label id="player_type-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="batting_style">Batting Style <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="batting_style" name="batting_style">
                                <option></option>
                                <option value="1">Right Hand</option>
                                <option value="2">Left Hand</option>
                            </select>
                            <label id="batting_style-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="bowling_style">Bowling Style <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="bowling_style" name="bowling_style">
                                <option></option>
                                <option value="1">Fast</option>
                                <option value="2">Spinner</option>
                                <option value="3">Medium</option>
                                <option value="4">None</option>
                            </select>
                            <label id="bowling_style-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="bowling_arm">bowling Arm <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="bowling_arm" name="bowling_arm">
                                <option></option>
                                <option value="1">Left Arm</option>
                                <option value="2">Right Arm</option>
                                <option value="3">Both</option>
                                <option value="4">None</option>
                            </select>
                            <label id="bowling_arm-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="player_id" id="player_id">
                        <button type="button" class="btn btn-outline-primary" id="save_newplayerBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closeplayerBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteplayerModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Player</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Player?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveplayerSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        player_page_tabs('',true);

        $('#player_type').select2({
            width: '100%',
            placeholder: "Select ...",
            allowClear: true
        });
        $('#batting_style').select2({
            width: '100%',
            placeholder: "Select ...",
            allowClear: true
        });
        $('#bowling_style').select2({
            width: '100%',
            placeholder: "Select ...",
            allowClear: true
        });
        $('#bowling_arm').select2({
            width: '100%',
            placeholder: "Select ...",
            allowClear: true
        });
       
    });

    

    function save_player(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#playerform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdateplayer') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    
                    if (res.errors.name) {
                        $('#name-error').show().text(res.errors.name);
                    } else {
                        $('#name-error').hide();
                    }
                    if (res.errors.player_type) {
                        $('#player_type-error').show().text(res.errors.player_type);
                    } else {
                        $('#player_type-error').hide();
                    }
                    if (res.errors.batting_style) {
                        $('#batting_style-error').show().text(res.errors.batting_style);
                    } else {
                        $('#batting_style-error').hide();
                    }
                    if (res.errors.bowling_style) {
                        $('#bowling_style-error').show().text(res.errors.bowling_style);
                    } else {
                        $('#bowling_style-error').hide();
                    }
                    if (res.errors.bowling_arm) {
                        $('#bowling_arm-error').show().text(res.errors.bowling_arm);
                    } else {
                        $('#bowling_arm-error').hide();
                    }

                 

                
                }

                if(res.status == 200){
                    if(btn_type == 'save_close'){
                        $("#playerModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            player_page_tabs();
                            toastr.success("player Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            player_page_tabs();
                            toastr.success("player Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#playerModel").find('form').trigger('reset');
                        $("#playerModel").find("#save_newplayerBtn").removeAttr('data-action');
                        $("#playerModel").find("#save_closeplayerBtn").removeAttr('data-action');
                        $("#playerModel").find("#save_newplayerBtn").removeAttr('data-id');
                        $("#playerModel").find("#save_closeplayerBtn").removeAttr('data-id');
                        $('#player_id').val("");
                        $('#name-error').html("");
                        $('#player_type-error').html("");
                        $('#batting_style-error').html("");
                        $('#bowling_style-error').html("");
                        $('#bowling_arm-error').html("");
            
                      
                    
                        $("#name").focus();
                        if(res.action == 'add'){
                            player_page_tabs();
                            toastr.success("Player Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            player_page_tabs();
                            toastr.success("Player Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#playerModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    player_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#playerModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                player_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newplayerBtn', function () {
        save_player($(this),'save_new');
    });

    $('body').on('click', '#save_closeplayerBtn', function () {
        save_player($(this),'save_close');
    });

    $('#playerModel').on('shown.bs.modal', function (e) {
        $("#name").focus();
    });

   

    $('#playerModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newplayerBtn").removeAttr('data-action');
        $(this).find("#save_closeplayerBtn").removeAttr('data-action');
        $(this).find("#save_newplayerBtn").removeAttr('data-id');
        $(this).find("#save_closeplayerBtn").removeAttr('data-id');
        $('#player_id').val("");
        $('#name-error').html("");
        $('#player_type-error').html("");
        $('#batting_style-error').html("");
        $('#bowling_style-error').html("");
        $('#bowling_arm-error').html("");

        var default_image = "{{ asset('photos/default_avatar.jpg') }}";
        $('#thumb_img_image_show').attr('src', default_image);
     
        
    });

    $('#DeleteplayerModel').on('hidden.bs.modal', function () {
        $(this).find("#RemoveplayerSubmit").removeAttr('data-id');
    });

    function player_page_tabs(tab_type='',is_clearState=false) {
        var country_id =  "{{ $id }}";
        if(is_clearState){
            $('#all_player').DataTable().state.clear();
        }

        $('#all_player').DataTable({
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
                "url": "{{ url('admin/allplayerslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type,country_id:country_id},
                // "dataSrc": ""
            },
            'columnDefs': [
                { "width": "50px", "targets": 0 },
                { "width": "145px", "targets": 1 },
                { "width": "75px", "targets": 2 },
                { "width": "120px", "targets": 3 },
                { "width": "115px", "targets": 4 },
                { "width": "115px", "targets": 5 },
                { "width": "115px", "targets": 6 },
                { "width": "115px", "targets": 7 },
                { "width": "115px", "targets": 8 },
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'name', name: 'name', class: "text-left multirow"},
                {data: 'player_type', name: 'player_type', class: "text-left multirow"},
                {data: 'batting_style', name: 'batting_style', class: "text-left multirow"},
                {data: 'bowling_style', name: 'bowling_style', class: "text-left multirow"},
                {data: 'bowling_arm', name: 'bowling_arm', class: "text-left multirow"},
                {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
            ]
        });
    }


    function changeplayerStatus(player_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changeplayerstatus') }}" +'/' + player_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#playerstatuscheck_"+player_id).val(2);
                    $("#playerstatuscheck_"+player_id).prop('checked',false);
                    player_page_tabs();
                    toastr.success("player Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#playerstatuscheck_"+player_id).val(1);
                    $("#playerstatuscheck_"+player_id).prop('checked',true);
                    player_page_tabs();
                    toastr.success("Player activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddplayerBtn', function (e) {
        $("#playerModel").find('.modal-title').html("Add player");
        
    });

    $('body').on('click', '#editplayerBtn', function () {
        var player_id = $(this).attr('data-id');
        $.get("{{ url('admin/player') }}" +'/' + player_id +'/edit', function (data) {
            $('#playerModel').find('.modal-title').html("Edit player");
            $('#playerModel').find('#save_closeplayerBtn').attr("data-action","update");
            $('#playerModel').find('#save_newplayerBtn').attr("data-action","update");
            $('#playerModel').find('#save_closeplayerBtn').attr("data-id",player_id);
            $('#playerModel').find('#save_newplayerBtn').attr("data-id",player_id);
            $('#player_id').val(data.id);
            $("#sr_no").val(data.sr_no);
            $('#name').val(data.name);
            $("#player_type").val(data.player_type).trigger('change');
            $("#batting_style").val(data.batting_style).trigger('change');
            $("#bowling_style").val(data.bowling_style).trigger('change');
            $("#bowling_arm").val(data.bowling_arm).trigger('change');
           
            if(data.thumb_img==null){
                var default_image = "{{ asset('photos/default_avatar.jpg') }}";
                $('#thumb_img_image_show').attr('src', default_image);
            }
            else{
                var thumb_img = "{{ url('images/player' ) }}"+ "/" + data.thumb_img;
                
                $('#thumb_img_image_show').attr('src', thumb_img);
            }
            
        })
    });

    $('body').on('click', '#deleteplayerBtn', function (e) {
        var delete_player_id = $(this).attr('data-id');
        $("#DeleteplayerModel").find('#RemoveplayerSubmit').attr('data-id',delete_player_id);
    });

    $('body').on('click', '#RemoveplayerSubmit', function (e) {
        $('#RemoveplayerSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_player_id = $(this).attr('data-id');
          
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/player') }}" +'/' + remove_player_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeleteplayerModel").modal('hide');
                    $('#RemoveplayerSubmit').prop('disabled',false);
                    $("#RemoveplayerSubmit").find('.removeloadericonfa').hide();
                    player_page_tabs();
                    toastr.success("Player Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeleteplayerModel").modal('hide');
                    $('#RemoveplayerSubmit').prop('disabled',false);
                    $("#RemoveplayerSubmit").find('.removeloadericonfa').hide();
                    player_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeleteplayerModel").modal('hide');
                $('#RemoveplayerSubmit').prop('disabled',false);
                $("#RemoveplayerSubmit").find('.removeloadericonfa').hide();
                player_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('#thumb_img').change(function(){
        $('#thumb_img-error').hide();
        var file = this.files[0];
        var fileType = file["type"];
        var validImageTypes = ["image/jpeg", "image/png", "image/jpg"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            $('#thumb_img-error').show().text("Please provide a Valid Extension Image(e.g: .jpg .png)");
            var default_image = "{{ asset('photos/default_avatar.jpg') }}";
            $('#thumb_img_image_show').attr('src', default_image);
        }
        else {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#thumb_img_image_show').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

  
</script>
<!-- user list JS end -->
@endsection


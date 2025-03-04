@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Team</a></li>
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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#teamModel" id="AddteamBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                
                            </div>
                            
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_team" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Short Name</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Other</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Short Name</th>
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

    <div class="modal fade" id="teamModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="teamform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add Team</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control input-flat" id="tournament_id" name="tournament_id" placeholder="" value="{{ $id }}">
                        <div class="form-group">
                            <label class="col-form-label" for="Serial_No">Serial No <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control input-flat" id="sr_no" name="sr_no" placeholder="" value="1">
                            <div id="srno-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group ">
                            <label class="col-form-label" for="name">Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="name" name="name" placeholder="">
                            <div id="name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="short_name">Short Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="short_name" name="short_name" placeholder="">
                            <div id="short_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="thumb_img">Image
                            </label>
                            <input type="file" class="form-control-file" id="thumb_img" onchange="" name="thumb_img">
                            <div id="thumb_img-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            <img src="{{ asset('photos/default_avatar.jpg') }}" class="" id="thumb_img_image_show" height="50px" width="50px" style="margin-top: 5px;width:50px;">
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="team_id" id="team_id">
                        <button type="button" class="btn btn-outline-primary" id="save_newteamBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closeteamBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteteamModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Team</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Team?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveteamSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        team_page_tabs('',true);
    });

    

    function save_team(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#teamform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdateteam') }}",
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

                 

                
                }

                if(res.status == 200){
                    if(btn_type == 'save_close'){
                        $("#teamModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            team_page_tabs();
                            toastr.success("Team Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            team_page_tabs();
                            toastr.success("Team Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#teamModel").find('form').trigger('reset');
                        $("#teamModel").find("#save_newteamBtn").removeAttr('data-action');
                        $("#teamModel").find("#save_closeteamBtn").removeAttr('data-action');
                        $("#teamModel").find("#save_newteamBtn").removeAttr('data-id');
                        $("#teamModel").find("#save_closeteamBtn").removeAttr('data-id');
                        $('#team_id').val("");
                        $('#name-error').html("");
            
                      
                    
                        $("#name").focus();
                        if(res.action == 'add'){
                            team_page_tabs();
                            toastr.success("Team Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            team_page_tabs();
                            toastr.success("Team Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#teamModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    team_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#teamModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                team_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newteamBtn', function () {
        save_team($(this),'save_new');
    });

    $('body').on('click', '#save_closeteamBtn', function () {
        save_team($(this),'save_close');
    });

    $('#teamModel').on('shown.bs.modal', function (e) {
        $("#start_price").focus();
    });

   

    $('#teamModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newteamBtn").removeAttr('data-action');
        $(this).find("#save_closeteamBtn").removeAttr('data-action');
        $(this).find("#save_newteamBtn").removeAttr('data-id');
        $(this).find("#save_closeteamBtn").removeAttr('data-id');
        $('#team_id').val("");
        $('#name-error').html("");

        var default_image = "{{ asset('photos/default_avatar.jpg') }}";
        $('#thumb_img_image_show').attr('src', default_image);
     
        
    });

    $('#DeleteteamModel').on('hidden.bs.modal', function () {
        $(this).find("#RemoveteamSubmit").removeAttr('data-id');
    });

    function team_page_tabs(tab_type='',is_clearState=false) {
        var tournament_id =  "{{ $id }}";
        if(is_clearState){
            $('#all_team').DataTable().state.clear();
        }

        $('#all_team').DataTable({
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
                "url": "{{ url('admin/allteamslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type,tournament_id:tournament_id},
                // "dataSrc": ""
            },
            'columnDefs': [
                { "width": "50px", "targets": 0 },
                { "width": "145px", "targets": 1 },
                { "width": "75px", "targets": 2 },
                { "width": "120px", "targets": 3 },
                { "width": "115px", "targets": 4 },
                { "width": "115px", "targets": 5 }
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'name', name: 'name', class: "text-left multirow"},
                {data: 'shot_name', name: 'shot_name', class: "text-left multirow"},
                {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
            ]
        });
    }


    function changeteamStatus(team_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changeteamstatus') }}" +'/' + team_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#teamstatuscheck_"+team_id).val(2);
                    $("#teamstatuscheck_"+team_id).prop('checked',false);
                    team_page_tabs();
                    toastr.success("Team Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#teamstatuscheck_"+team_id).val(1);
                    $("#teamstatuscheck_"+team_id).prop('checked',true);
                    team_page_tabs();
                    toastr.success("Team activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddteamBtn', function (e) {
        $("#teamModel").find('.modal-title').html("Add Team");
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/team/sr_no/'.$id) }}",
            success: function (res) {
                $("#sr_no").val(res.sr_no);
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('body').on('click', '#editteamBtn', function () {
        var team_id = $(this).attr('data-id');
        $.get("{{ url('admin/team') }}" +'/' + team_id +'/edit', function (data) {
            $('#teamModel').find('.modal-title').html("Edit team");
            $('#teamModel').find('#save_closeteamBtn').attr("data-action","update");
            $('#teamModel').find('#save_newteamBtn').attr("data-action","update");
            $('#teamModel').find('#save_closeteamBtn').attr("data-id",team_id);
            $('#teamModel').find('#save_newteamBtn').attr("data-id",team_id);
            $('#team_id').val(data.id);
            $("#sr_no").val(data.sr_no);
            $('#name').val(data.name);
            $('#short_name').val(data.short_name);
           
            if(data.thumb_img==null){
                var default_image = "{{ asset('photos/default_avatar.jpg') }}";
                $('#thumb_img_image_show').attr('src', default_image);
            }
            else{
                var thumb_img = "{{ url('images/team' ) }}"+ "/" + data.thumb_img;
                
                $('#thumb_img_image_show').attr('src', thumb_img);
            }
            
        })
    });

    $('body').on('click', '#deleteteamBtn', function (e) {
        var delete_team_id = $(this).attr('data-id');
        $("#DeleteteamModel").find('#RemoveteamSubmit').attr('data-id',delete_team_id);
    });

    $('body').on('click', '#RemoveteamSubmit', function (e) {
        $('#RemoveteamSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_team_id = $(this).attr('data-id');
          
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/team') }}" +'/' + remove_team_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeleteteamModel").modal('hide');
                    $('#RemoveteamSubmit').prop('disabled',false);
                    $("#RemoveteamSubmit").find('.removeloadericonfa').hide();
                    team_page_tabs();
                    toastr.success("team Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeleteteamModel").modal('hide');
                    $('#RemoveteamSubmit').prop('disabled',false);
                    $("#RemoveteamSubmit").find('.removeloadericonfa').hide();
                    team_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeleteteamModel").modal('hide');
                $('#RemoveteamSubmit').prop('disabled',false);
                $("#RemoveteamSubmit").find('.removeloadericonfa').hide();
                team_page_tabs();
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


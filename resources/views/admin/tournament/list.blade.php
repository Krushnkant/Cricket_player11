@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Tournament</a></li>
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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tournamentModel" id="AddtournamentBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                
                            </div>
                            
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_tournament" class="table zero-configuration customNewtable" style="width:100%">
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

    <div class="modal fade" id="tournamentModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="tournamentform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add Tournament</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
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
                        <input type="hidden" name="tournament_id" id="tournament_id">
                        <button type="button" class="btn btn-outline-primary" id="save_newtournamentBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closetournamentBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeletetournamentModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Tournament</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Tournament?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemovetournamentSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        tournament_page_tabs('',true);
    });

    

    function save_tournament(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#tournamentform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdatetournament') }}",
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
                        $("#tournamentModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            tournament_page_tabs();
                            toastr.success("tournament Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            tournament_page_tabs();
                            toastr.success("Tournament Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#tournamentModel").find('form').trigger('reset');
                        $("#tournamentModel").find("#save_newtournamentBtn").removeAttr('data-action');
                        $("#tournamentModel").find("#save_closetournamentBtn").removeAttr('data-action');
                        $("#tournamentModel").find("#save_newtournamentBtn").removeAttr('data-id');
                        $("#tournamentModel").find("#save_closetournamentBtn").removeAttr('data-id');
                        $('#tournament_id').val("");
                        $('#name-error').html("");
            
                      
                    
                        $("#name").focus();
                        if(res.action == 'add'){
                            tournament_page_tabs();
                            toastr.success("Tournament Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            tournament_page_tabs();
                            toastr.success("Tournament Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#tournamentModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    tournament_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#tournamentModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                tournament_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newtournamentBtn', function () {
        save_tournament($(this),'save_new');
    });

    $('body').on('click', '#save_closetournamentBtn', function () {
        save_tournament($(this),'save_close');
    });

    $('#tournamentModel').on('shown.bs.modal', function (e) {
        $("#start_price").focus();
    });

   

    $('#tournamentModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newtournamentBtn").removeAttr('data-action');
        $(this).find("#save_closetournamentBtn").removeAttr('data-action');
        $(this).find("#save_newtournamentBtn").removeAttr('data-id');
        $(this).find("#save_closetournamentBtn").removeAttr('data-id');
        $('#tournament_id').val("");
        $('#name-error').html("");

        var default_image = "{{ asset('photos/default_avatar.jpg') }}";
        $('#thumb_img_image_show').attr('src', default_image);
     
        
    });

    $('#DeletetournamentModel').on('hidden.bs.modal', function () {
        $(this).find("#RemovetournamentSubmit").removeAttr('data-id');
    });

    function tournament_page_tabs(tab_type='',is_clearState=false) {
        if(is_clearState){
            $('#all_tournament').DataTable().state.clear();
        }

        $('#all_tournament').DataTable({
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
                "url": "{{ url('admin/alltournamentslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
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


    function changetournamentStatus(tournament_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changetournamentstatus') }}" +'/' + tournament_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#tournamentstatuscheck_"+tournament_id).val(2);
                    $("#tournamentstatuscheck_"+tournament_id).prop('checked',false);
                    tournament_page_tabs();
                    toastr.success("Tournament Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#tournamentstatuscheck_"+tournament_id).val(1);
                    $("#tournamentstatuscheck_"+tournament_id).prop('checked',true);
                    tournament_page_tabs();
                    toastr.success("Tournament activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddtournamentBtn', function (e) {
        $("#tournamentModel").find('.modal-title').html("Add Tournament");
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/tournament/sr_no') }}",
            success: function (res) {
                $("#sr_no").val(res.sr_no);
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('body').on('click', '#edittournamentBtn', function () {
        var tournament_id = $(this).attr('data-id');
        $.get("{{ url('admin/tournament') }}" +'/' + tournament_id +'/edit', function (data) {
            $('#tournamentModel').find('.modal-title').html("Edit tournament");
            $('#tournamentModel').find('#save_closetournamentBtn').attr("data-action","update");
            $('#tournamentModel').find('#save_newtournamentBtn').attr("data-action","update");
            $('#tournamentModel').find('#save_closetournamentBtn').attr("data-id",tournament_id);
            $('#tournamentModel').find('#save_newtournamentBtn').attr("data-id",tournament_id);
            $('#tournament_id').val(data.id);
            $("#sr_no").val(data.sr_no);
            $('#name').val(data.name);
            $('#short_name').val(data.short_name);
           
            if(data.thumb_img==null){
                var default_image = "{{ asset('photos/default_avatar.jpg') }}";
                $('#thumb_img_image_show').attr('src', default_image);
            }
            else{
                var thumb_img = "{{ url('images/tournament' ) }}"+ "/" + data.thumb_img;
                
                $('#thumb_img_image_show').attr('src', thumb_img);
            }
            
        })
    });

    $('body').on('click', '#deletetournamentBtn', function (e) {
        var delete_tournament_id = $(this).attr('data-id');
        $("#DeletetournamentModel").find('#RemovetournamentSubmit').attr('data-id',delete_tournament_id);
    });

    $('body').on('click', '#RemovetournamentSubmit', function (e) {
        $('#RemovetournamentSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_tournament_id = $(this).attr('data-id');
          
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/tournament') }}" +'/' + remove_tournament_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeletetournamentModel").modal('hide');
                    $('#RemovetournamentSubmit').prop('disabled',false);
                    $("#RemovetournamentSubmit").find('.removeloadericonfa').hide();
                    tournament_page_tabs();
                    toastr.success("tournament Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeletetournamentModel").modal('hide');
                    $('#RemovetournamentSubmit').prop('disabled',false);
                    $("#RemovetournamentSubmit").find('.removeloadericonfa').hide();
                    tournament_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeletetournamentModel").modal('hide');
                $('#RemovetournamentSubmit').prop('disabled',false);
                $("#RemovetournamentSubmit").find('.removeloadericonfa').hide();
                tournament_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    
    $('body').on('change', '#type', function () {
        if($(this).val() == 1){
            $("#Amount_label").html("Percentage (%) <span class='text-danger'>*</span>");
        }
        else if($(this).val() == 2){
            $("#Amount_label").html("Amount <span class='text-danger'>*</span>");
        }
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

    $('body').on('click', '#viewteamBtn', function () {

            var tournament_id = $(this).attr('data-id');
            var url = "{{ url('admin/team') }}" + "/" + tournament_id;
            window.open(url);
    });
</script>
<!-- user list JS end -->
@endsection


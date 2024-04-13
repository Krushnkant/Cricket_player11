@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Series Team</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_seriesteam" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Other</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
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

    <div class="modal fade" id="seriesteamModel">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="seriesteamform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add seriesteam</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control input-flat" id="series_id" name="series_id" placeholder="" value="{{ $id }}">
                
                        <div class="form-group">
                            <label class="col-form-label" for="series_team_players">Series Team Players<span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="series_team_players" name="series_team_players[]" multiple>
                                @foreach($players as $player)
                                <option value="{{ $player->id }}">{{ $player->name }}</option>
                                @endforeach
                            </select>
                            <label id="series_team_players-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="seriesteam_id" id="seriesteam_id">
                        <!-- <button type="button" class="btn btn-outline-primary" id="save_newseriesteamBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button> -->
                        <button type="button" class="btn btn-primary" id="save_closeseriesteamBtn">Save <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteseriesteamModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove seriesteam</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this seriesteam?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveseriesteamSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        seriesteam_page_tabs('',true);

        $('#series_team_players').select2({
            width: '100%',
            multiple: true,
            placeholder: "Select...",
            allowClear: true,
            autoclose: false,
            closeOnSelect: false,
        });
    });
    
    function save_seriesteam(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#seriesteamform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdateseriesteam') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    
                    if (res.errors.series_team_players) {
                        $('#series_team_players-error').show().text(res.errors.series_team_players);
                    } else {
                        $('#series_team_players-error').hide();
                    }

                 

                
                }

                if(res.status == 200){
                    if(btn_type == 'save_close'){
                        $("#seriesteamModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            seriesteam_page_tabs();
                            toastr.success("Series Team Player Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            seriesteam_page_tabs();
                            toastr.success("Series Team Player Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#seriesteamModel").find('form').trigger('reset');
                        $("#seriesteamModel").find("#save_newseriesteamBtn").removeAttr('data-action');
                        $("#seriesteamModel").find("#save_closeseriesteamBtn").removeAttr('data-action');
                        $("#seriesteamModel").find("#save_newseriesteamBtn").removeAttr('data-id');
                        $("#seriesteamModel").find("#save_closeseriesteamBtn").removeAttr('data-id');
                        $('#seriesteam_id').val("");
                        $('#series_team_players-error').html("");
            
                      
                    
                        $("#name").focus();
                        if(res.action == 'add'){
                            seriesteam_page_tabs();
                            toastr.success("Series Team Player Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            seriesteam_page_tabs();
                            toastr.success("Series Team Player Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#seriesteamModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    seriesteam_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#seriesteamModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                seriesteam_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newseriesteamBtn', function () {
        save_seriesteam($(this),'save_new');
    });

    $('body').on('click', '#save_closeseriesteamBtn', function () {
        save_seriesteam($(this),'save_close');
    });

    $('#seriesteamModel').on('shown.bs.modal', function (e) {
        $("#start_price").focus();
    });

   

    $('#seriesteamModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newseriesteamBtn").removeAttr('data-action');
        $(this).find("#save_closeseriesteamBtn").removeAttr('data-action');
        $(this).find("#save_newseriesteamBtn").removeAttr('data-id');
        $(this).find("#save_closeseriesteamBtn").removeAttr('data-id');
        $('#seriesteam_id').val("");
        $('#series_team_players-error').html("");

        var default_image = "{{ asset('photos/default_avatar.jpg') }}";
        $('#thumb_img_image_show').attr('src', default_image);
     
        
    });

    $('#DeleteseriesteamModel').on('hidden.bs.modal', function () {
        $(this).find("#RemoveseriesteamSubmit").removeAttr('data-id');
    });

    function seriesteam_page_tabs(tab_type='',is_clearState=false) {
        var series_id =  "{{ $id }}";
        if(is_clearState){
            $('#all_seriesteam').DataTable().state.clear();
        }

        $('#all_seriesteam').DataTable({
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
                "url": "{{ url('admin/allseriesteamslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type,series_id:series_id},
                // "dataSrc": ""
            },
            'columnDefs': [
                { "width": "50px", "targets": 0 },
                { "width": "145px", "targets": 1 },
                { "width": "75px", "targets": 2 },
                { "width": "120px", "targets": 3 }
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'name', name: 'name', class: "text-left multirow"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
            ]
        });
    }


  

    $('body').on('click', '#editseriesteamBtn', function () {
        var seriesteam_id = $(this).attr('data-id');
        $.get("{{ url('admin/seriesteam') }}" +'/' + seriesteam_id +'/edit', function (data) {
            console.log(data);
            $('#seriesteamModel').find('.modal-title').html("Add Edit Series Team Players");
            $('#seriesteamModel').find('#save_closeseriesteamBtn').attr("data-action","update");
            $('#seriesteamModel').find('#save_newseriesteamBtn').attr("data-action","update");
            $('#seriesteamModel').find('#save_closeseriesteamBtn').attr("data-id",seriesteam_id);
            $('#seriesteamModel').find('#save_newseriesteamBtn').attr("data-id",seriesteam_id);
            $('#seriesteam_id').val(data.seriesteamid);
            var selectedOptions = data.seriesteamplayer;
            for(var i in selectedOptions) {
                var optionVal = selectedOptions[i];
                $("#series_team_players").find("option[value="+optionVal+"]").prop("selected", "selected").trigger('change');
            }
           
            $('#series_team_players').select2({
            width: '100%',
            multiple: true,
            placeholder: "Select...",
            allowClear: true,
            autoclose: false,
            closeOnSelect: false,
            });
            
        })
    });

    $('body').on('click', '#deleteseriesteamBtn', function (e) {
        var delete_seriesteam_id = $(this).attr('data-id');
        $("#DeleteseriesteamModel").find('#RemoveseriesteamSubmit').attr('data-id',delete_seriesteam_id);
    });

    $('body').on('click', '#RemoveseriesteamSubmit', function (e) {
        $('#RemoveseriesteamSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_seriesteam_id = $(this).attr('data-id');
          
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/seriesteam') }}" +'/' + remove_seriesteam_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeleteseriesteamModel").modal('hide');
                    $('#RemoveseriesteamSubmit').prop('disabled',false);
                    $("#RemoveseriesteamSubmit").find('.removeloadericonfa').hide();
                    seriesteam_page_tabs();
                    toastr.success("Series Team Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeleteseriesteamModel").modal('hide');
                    $('#RemoveseriesteamSubmit').prop('disabled',false);
                    $("#RemoveseriesteamSubmit").find('.removeloadericonfa').hide();
                    seriesteam_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeleteseriesteamModel").modal('hide');
                $('#RemoveseriesteamSubmit').prop('disabled',false);
                $("#RemoveseriesteamSubmit").find('.removeloadericonfa').hide();
                seriesteam_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });



 

  
</script>
<!-- user list JS end -->
@endsection


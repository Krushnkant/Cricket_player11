@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Series</a></li>
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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#seriesModel" id="AddseriesBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                
                            </div>
                            
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_series" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Tournament</th>
                                    <th>Series Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Other</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Tournament</th>
                                    <th>Series Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
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

    <div class="modal fade" id="seriesModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="seriesform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add Series</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        
                        
                        <div class="form-group ">
                            <label class="col-form-label" for="name">Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="name" name="name" placeholder="">
                            <div id="name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-form-label" for="tournament_id">Tournament <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="tournament_id" name="tournament_id">
                                <option></option>
                                @foreach($tournaments as $tournament)
                                <option value="{{ $tournament->id }}">{{ $tournament->name }}</option>
                                @endforeach
                            </select>
                            <label id="tournament_id-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="series_type">Series Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="series_type" name="series_type">
                                <option></option>
                                <option value="1" @if(isset($match) && (1 == $match->series_type)) selected @endif>T20</option>
                                <option value="2" @if(isset($match) && (2 == $match->series_type)) selected @endif>ODI</option>
                                <option value="3" @if(isset($match) && (3 == $match->series_type)) selected @endif>Both</option>
                            </select>
                            <label id="series_type-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="start_date">Start Date <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control input-flat"  id="start_date" name="start_date" value="<?php if(isset($match)){ echo Date('Y-m-d\TH:i',$match->start_date); } ?>">
                            <label id="start_date-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label" for="end_date">End Date <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control input-flat"  id="end_date" name="end_date" value="<?php if(isset($match)){ echo Date('Y-m-d\TH:i',$match->end_date); } ?>">
                            <label id="end_date-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label" for="series_team">Series Team <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="series_team" name="series_team[]" multiple>
                                @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            <label id="series_team-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="series_id" id="series_id">
                        <button type="button" class="btn btn-outline-primary" id="save_newseriesBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closeseriesBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteseriesModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Series</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Series?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveseriesSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        series_page_tabs('',true);

        $('#tournament_id').select2({
            width: '100%',
            placeholder: "Select...",
            allowClear: true
        });

        $('#series_type').select2({
            width: '100%',
            placeholder: "Select...",
            allowClear: true
        });

        $('#series_team').select2({
            width: '100%',
            multiple: true,
            placeholder: "Select...",
            allowClear: true,
            autoclose: false,
            closeOnSelect: false,
        });
    });
   
    

    function save_series(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#seriesform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdateseries') }}",
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

                    if (res.errors.start_date) {
                        $('#start_date-error').show().text(res.errors.start_date);
                    } else {
                        $('#start_date-error').hide();
                    }

                    if (res.errors.end_date) {
                        $('#end_date-error').show().text(res.errors.end_date);
                    } else {
                        $('#end_date-error').hide();
                    }

                    if (res.errors.series_type) {
                        $('#series_type-error').show().text(res.errors.series_type);
                    } else {
                        $('#series_type-error').hide();
                    }

                    
                    if (res.errors.tournament_id) {
                        $('#tournament_id-error').show().text(res.errors.tournament_id);
                    } else {
                        $('#tournament_id-error').hide();
                    }

        
                
                }

                if(res.status == 200){
                    if(btn_type == 'save_close'){
                        $("#seriesModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            series_page_tabs();
                            toastr.success("Series Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            series_page_tabs();
                            toastr.success("Series Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#seriesModel").find('form').trigger('reset');
                        $("#seriesModel").find("#save_newseriesBtn").removeAttr('data-action');
                        $("#seriesModel").find("#save_closeseriesBtn").removeAttr('data-action');
                        $("#seriesModel").find("#save_newseriesBtn").removeAttr('data-id');
                        $("#seriesModel").find("#save_closeseriesBtn").removeAttr('data-id');
                        $('#series_id').val("");
                        $('#name-error').html("");
                        $('#tournament_id-error').html("");
                        $('#series_type-error').html("");
                        $('#start_date-error').html("");
                        $('#end_date-error').html("");
            
                      
                    
                        $("#name").focus();
                        if(res.action == 'add'){
                            series_page_tabs();
                            toastr.success("Series Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            series_page_tabs();
                            toastr.success("Series Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#seriesModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    series_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#seriesModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                series_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newseriesBtn', function () {
        save_series($(this),'save_new');
    });

    $('body').on('click', '#save_closeseriesBtn', function () {
        save_series($(this),'save_close');
    });

    $('#seriesModel').on('shown.bs.modal', function (e) {
        $("#name").focus();
    });

   

    $('#seriesModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newseriesBtn").removeAttr('data-action');
        $(this).find("#save_closeseriesBtn").removeAttr('data-action');
        $(this).find("#save_newseriesBtn").removeAttr('data-id');
        $(this).find("#save_closeseriesBtn").removeAttr('data-id');
        $('#series_id').val("");
        $('#name-error').html("");
        $('#tournament_id-error').html("");
        $('#series_type-error').html("");
        $('#start_date-error').html("");
        $('#end_date-error').html("");

        var default_image = "{{ asset('photos/default_avatar.jpg') }}";
        $('#thumb_img_image_show').attr('src', default_image);
     
        
    });

    $('#DeleteseriesModel').on('hidden.bs.modal', function () {
        $(this).find("#RemoveseriesSubmit").removeAttr('data-id');
    });

    function series_page_tabs(tab_type='',is_clearState=false) {
       
        if(is_clearState){
            $('#all_series').DataTable().state.clear();
        }

        $('#all_series').DataTable({
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
                "url": "{{ url('admin/allseriesslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
                // "dataSrc": ""
            },
            'columnDefs': [
                { "width": "50px", "targets": 0 },
                { "width": "145px", "targets": 1 },
                { "width": "150px", "targets": 2 },
                { "width": "90px", "targets": 3 },
                { "width": "115px", "targets": 4 },
                { "width": "115px", "targets": 5 },
                { "width": "115px", "targets": 6 },
                { "width": "115px", "targets": 7 },
                { "width": "115px", "targets": 8 }
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'name', name: 'name', class: "text-left multirow"},
                {data: 'tournament', name: 'tournament', class: "text-left multirow"},
                {data: 'series_type', name: 'series_type', class: "text-left multirow"},
                {data: 'start_date', name: 'start_date', class: "text-left multirow"},
                {data: 'end_date', name: 'end_date', class: "text-left multirow"},
                {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
            ]
        });
    }


    function changeseriesStatus(series_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changeseriesstatus') }}" +'/' + series_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#seriesstatuscheck_"+series_id).val(2);
                    $("#seriesstatuscheck_"+series_id).prop('checked',false);
                    series_page_tabs();
                    toastr.success("Series Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#seriesstatuscheck_"+series_id).val(1);
                    $("#seriesstatuscheck_"+series_id).prop('checked',true);
                    series_page_tabs();
                    toastr.success("Series activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddseriesBtn', function (e) {
        $("#seriesModel").find('.modal-title').html("Add Series");
        $('#series_team').select2({
            width: '100%',
            multiple: true,
            placeholder: "Select...",
            allowClear: true,
            autoclose: false,
            closeOnSelect: false,
        });

        $('#tournament_id').select2({
            width: '100%',
            placeholder: "Select...",
            allowClear: true
        });

        $('#series_type').select2({
            width: '100%',
            placeholder: "Select...",
            allowClear: true
        });
        
    });

    $('body').on('click', '#editseriesBtn', function () {
        var series_id = $(this).attr('data-id');
        $.get("{{ url('admin/series') }}" +'/' + series_id +'/edit', function (data) {
        
            $('#seriesModel').find('.modal-title').html("Edit series");
            $('#seriesModel').find('#save_closeseriesBtn').attr("data-action","update");
            $('#seriesModel').find('#save_newseriesBtn').attr("data-action","update");
            $('#seriesModel').find('#save_closeseriesBtn').attr("data-id",series_id);
            $('#seriesModel').find('#save_newseriesBtn').attr("data-id",series_id);
            $('#series_id').val(data.series.id);
            $("#sr_no").val(data.series.sr_no);
            $('#name').val(data.series.name);
            $('#start_date').val(data.series.start_date);
            $('#end_date').val(data.series.end_date);
            $("#tournament_id").val(data.series.tournament_id).trigger('change');
            $("#series_type").val(data.series.series_type).trigger('change');
            var selectedOptions = data.seriesteam;
            for(var i in selectedOptions) {
                var optionVal = selectedOptions[i];
                $("#series_team").find("option[value="+optionVal+"]").prop("selected", "selected").trigger('change');
            }
            if(data.thumb_img==null){
                var default_image = "{{ asset('photos/default_avatar.jpg') }}";
                $('#thumb_img_image_show').attr('src', default_image);
            }
            else{
                var thumb_img = "{{ url('images/series' ) }}"+ "/" + data.thumb_img;
                
                $('#thumb_img_image_show').attr('src', thumb_img);
            }

            $('#series_team').select2({
            width: '100%',
            multiple: true,
            placeholder: "Select...",
            allowClear: true,
            autoclose: false,
            closeOnSelect: false,
            });

            $('#tournament_id').select2({
            width: '100%',
            placeholder: "Select...",
            allowClear: true
            });

            $('#series_type').select2({
                width: '100%',
                placeholder: "Select...",
                allowClear: true
            });
            
        })
    });

    $('body').on('click', '#deleteseriesBtn', function (e) {
        var delete_series_id = $(this).attr('data-id');
        $("#DeleteseriesModel").find('#RemoveseriesSubmit').attr('data-id',delete_series_id);
    });

    $('body').on('click', '#RemoveseriesSubmit', function (e) {
        $('#RemoveseriesSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_series_id = $(this).attr('data-id');
          
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/series') }}" +'/' + remove_series_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeleteseriesModel").modal('hide');
                    $('#RemoveseriesSubmit').prop('disabled',false);
                    $("#RemoveseriesSubmit").find('.removeloadericonfa').hide();
                    series_page_tabs();
                    toastr.success("Series Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeleteseriesModel").modal('hide');
                    $('#RemoveseriesSubmit').prop('disabled',false);
                    $("#RemoveseriesSubmit").find('.removeloadericonfa').hide();
                    series_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeleteseriesModel").modal('hide');
                $('#RemoveseriesSubmit').prop('disabled',false);
                $("#RemoveseriesSubmit").find('.removeloadericonfa').hide();
                series_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    


    $('body').on('click', '#viewplayerBtn', function () {
        var tournament_id = $(this).attr('data-id');
        var url = "{{ url('admin/player') }}" + "/" + tournament_id;
        window.open(url);
    });

 

  
</script>
<!-- user list JS end -->
@endsection


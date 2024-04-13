@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Stadium</a></li>
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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#stadiumModel" id="AddstadiumBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                
                            </div>
                            
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_stadium" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Short Name</th>
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>City</th>
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
                                    <th>Country</th>
                                    <th>State</th>
                                    <th>City</th>
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

    <div class="modal fade" id="stadiumModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="stadiumform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add Stadium</h5>
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
                        <div class="form-group ">
                            <label class="col-form-label" for="short_name">Short Name 
                            </label>
                            <input type="text" class="form-control input-flat" id="short_name" name="short_name" placeholder="">
                            <div id="short_name-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="country_id">Country <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" id="country_id" name="country_id">
                                <option></option>
                                @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <label id="country_id-error" class="error invalid-feedback animated fadeInDown" for="question" style="color: red"></label>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="state">State <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="state" name="state" placeholder="">
                            <div id="state-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group ">
                            <label class="col-form-label" for="city">City <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="city" name="city" placeholder="">
                            <div id="city-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="stadium_id" id="stadium_id">
                        <button type="button" class="btn btn-outline-primary" id="save_newstadiumBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closestadiumBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeletestadiumModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Stadium</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Stadium?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemovestadiumSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        stadium_page_tabs('',true);

        $('#country_id').select2({
            width: '100%',
            placeholder: "Select ...",
            allowClear: true
        });
    });
   
    

    function save_stadium(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#stadiumform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdatestadium') }}",
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

                    if (res.errors.country_id) {
                        $('#country_id-error').show().text(res.errors.country_id);
                    } else {
                        $('#country_id-error').hide();
                    }

                    if (res.errors.state) {
                        $('#state-error').show().text(res.errors.state);
                    } else {
                        $('#state-error').hide();
                    }

                    if (res.errors.city) {
                        $('#city-error').show().text(res.errors.city);
                    } else {
                        $('#city-error').hide();
                    }
                }

                if(res.status == 200){
                    if(btn_type == 'save_close'){
                        $("#stadiumModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            stadium_page_tabs();
                            toastr.success("Stadium Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            stadium_page_tabs();
                            toastr.success("Stadium Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#stadiumModel").find('form').trigger('reset');
                        $("#stadiumModel").find("#save_newstadiumBtn").removeAttr('data-action');
                        $("#stadiumModel").find("#save_closestadiumBtn").removeAttr('data-action');
                        $("#stadiumModel").find("#save_newstadiumBtn").removeAttr('data-id');
                        $("#stadiumModel").find("#save_closestadiumBtn").removeAttr('data-id');
                        $('#stadium_id').val("");
                        $('#name-error').html("");
                        $('#country_id-error').html("");
                        $('#state-error').html("");
                        $('#city-error').html("");
            
                      
                    
                        $("#name").focus();
                        if(res.action == 'add'){
                            stadium_page_tabs();
                            toastr.success("Stadium Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            stadium_page_tabs();
                            toastr.success("Stadium Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#stadiumModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    stadium_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#stadiumModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                stadium_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newstadiumBtn', function () {
        save_stadium($(this),'save_new');
    });

    $('body').on('click', '#save_closestadiumBtn', function () {
        save_stadium($(this),'save_close');
    });

    $('#stadiumModel').on('shown.bs.modal', function (e) {
        $("#name").focus();
    });

   

    $('#stadiumModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newstadiumBtn").removeAttr('data-action');
        $(this).find("#save_closestadiumBtn").removeAttr('data-action');
        $(this).find("#save_newstadiumBtn").removeAttr('data-id');
        $(this).find("#save_closestadiumBtn").removeAttr('data-id');
        $('#stadium_id').val("");
        $('#name-error').html("");
        $('#country_id-error').html("");
        $('#state-error').html("");
        $('#city-error').html("");

        var default_image = "{{ asset('photos/default_avatar.jpg') }}";
        $('#thumb_img_image_show').attr('src', default_image);
     
        
    });

    $('#DeletestadiumModel').on('hidden.bs.modal', function () {
        $(this).find("#RemovestadiumSubmit").removeAttr('data-id');
    });

    function stadium_page_tabs(tab_type='',is_clearState=false) {
       
        if(is_clearState){
            $('#all_stadium').DataTable().state.clear();
        }

        $('#all_stadium').DataTable({
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
                "url": "{{ url('admin/allstadiumslist') }}",
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
                {data: 'shot_name', name: 'shot_name', class: "text-left multirow"},
                {data: 'country', name: 'country', class: "text-left multirow"},
                {data: 'state', name: 'state', class: "text-left multirow"},
                {data: 'city', name: 'city', class: "text-left multirow"},
                {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
            ]
        });
    }


    function changestadiumStatus(stadium_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changestadiumstatus') }}" +'/' + stadium_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#stadiumstatuscheck_"+stadium_id).val(2);
                    $("#stadiumstatuscheck_"+stadium_id).prop('checked',false);
                    stadium_page_tabs();
                    toastr.success("Stadium Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#stadiumstatuscheck_"+stadium_id).val(1);
                    $("#stadiumstatuscheck_"+stadium_id).prop('checked',true);
                    stadium_page_tabs();
                    toastr.success("Stadium activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddstadiumBtn', function (e) {
        $("#stadiumModel").find('.modal-title').html("Add Stadium");
        
    });

    $('body').on('click', '#editstadiumBtn', function () {
        var stadium_id = $(this).attr('data-id');
        $.get("{{ url('admin/stadium') }}" +'/' + stadium_id +'/edit', function (data) {
            $('#stadiumModel').find('.modal-title').html("Edit Stadium");
            $('#stadiumModel').find('#save_closestadiumBtn').attr("data-action","update");
            $('#stadiumModel').find('#save_newstadiumBtn').attr("data-action","update");
            $('#stadiumModel').find('#save_closestadiumBtn').attr("data-id",stadium_id);
            $('#stadiumModel').find('#save_newstadiumBtn').attr("data-id",stadium_id);
            $('#stadium_id').val(data.id);
            $("#sr_no").val(data.sr_no);
            $('#name').val(data.name);
            $('#short_name').val(data.short_name);
            $('#state').val(data.state);
            $('#city').val(data.city);
            $("#country_id").val(data.country_id).trigger('change');
            if(data.thumb_img==null){
                var default_image = "{{ asset('photos/default_avatar.jpg') }}";
                $('#thumb_img_image_show').attr('src', default_image);
            }
            else{
                var thumb_img = "{{ url('images/stadium' ) }}"+ "/" + data.thumb_img;
                
                $('#thumb_img_image_show').attr('src', thumb_img);
            }
            
        })
    });

    $('body').on('click', '#deletestadiumBtn', function (e) {
        var delete_stadium_id = $(this).attr('data-id');
        $("#DeletestadiumModel").find('#RemovestadiumSubmit').attr('data-id',delete_stadium_id);
    });

    $('body').on('click', '#RemovestadiumSubmit', function (e) {
        $('#RemovestadiumSubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_stadium_id = $(this).attr('data-id');
          
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/stadium') }}" +'/' + remove_stadium_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeletestadiumModel").modal('hide');
                    $('#RemovestadiumSubmit').prop('disabled',false);
                    $("#RemovestadiumSubmit").find('.removeloadericonfa').hide();
                    stadium_page_tabs();
                    toastr.success("Stadium Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeletestadiumModel").modal('hide');
                    $('#RemovestadiumSubmit').prop('disabled',false);
                    $("#RemovestadiumSubmit").find('.removeloadericonfa').hide();
                    stadium_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeletestadiumModel").modal('hide');
                $('#RemovestadiumSubmit').prop('disabled',false);
                $("#RemovestadiumSubmit").find('.removeloadericonfa').hide();
                stadium_page_tabs();
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

 

  
</script>
<!-- user list JS end -->
@endsection


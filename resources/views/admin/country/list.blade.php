@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Country</a></li>
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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#countryModel" id="AddcountryBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                
                            </div>
                            
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_user_tab">
                            <table id="all_country" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Other</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
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

    <div class="modal fade" id="countryModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="countryform" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add Country</h5>
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
                            <label class="col-form-label" for="thumb_img">Image
                            </label>
                            <input type="file" class="form-control-file" id="thumb_img" onchange="" name="thumb_img">
                            <div id="thumb_img-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                            <img src="{{ asset('photos/default_avatar.jpg') }}" class="" id="thumb_img_image_show" height="50px" width="50px" style="margin-top: 5px;width:50px;">
                        </div>
                        
                        
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="country_id" id="country_id">
                        <button type="button" class="btn btn-outline-primary" id="save_newcountryBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closecountryBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeletecountryModel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Country</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Country?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemovecountrySubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- user list JS start -->
<script type="text/javascript">
    $(document).ready(function() {
        country_page_tabs('',true);
    });

    

    function save_country(btn,btn_type){
        $(btn).prop('disabled',true);
        $(btn).find('.loadericonfa').show();

        var action  = $(btn).attr('data-action');

        var formData = new FormData($("#countryform")[0]);

        formData.append('action',action);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/addorupdatecountry') }}",
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
                        $("#countryModel").modal('hide');
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        if(res.action == 'add'){
                            country_page_tabs();
                            toastr.success("Country Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            country_page_tabs();
                            toastr.success("Country Updated",'Success',{timeOut: 5000});
                        }
                    }

                    if(btn_type == 'save_new'){
                        $(btn).prop('disabled',false);
                        $(btn).find('.loadericonfa').hide();
                        $("#countryModel").find('form').trigger('reset');
                        $("#countryModel").find("#save_newcountryBtn").removeAttr('data-action');
                        $("#countryModel").find("#save_closecountryBtn").removeAttr('data-action');
                        $("#countryModel").find("#save_newcountryBtn").removeAttr('data-id');
                        $("#countryModel").find("#save_closecountryBtn").removeAttr('data-id');
                        $('#country_id').val("");
                        $('#name-error').html("");
            
                      
                    
                        $("#name").focus();
                        if(res.action == 'add'){
                            country_page_tabs();
                            toastr.success("Country Added",'Success',{timeOut: 5000});
                        }
                        if(res.action == 'update'){
                            country_page_tabs();
                            toastr.success("Country Updated",'Success',{timeOut: 5000});
                        }
                    }
                }

                if(res.status == 400){
                    $("#countryModel").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    country_page_tabs();
                    if(res.message == ""){
                      toastr.error("Please try again",'Error',{timeOut: 5000});
                    }else{
                        toastr.error(res.message,'Error',{timeOut: 5000});  
                    }
                }
            },
            error: function (data) {
                $("#countryModel").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                country_page_tabs();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#save_newcountryBtn', function () {
        save_country($(this),'save_new');
    });

    $('body').on('click', '#save_closecountryBtn', function () {
        save_country($(this),'save_close');
    });

    $('#countryModel').on('shown.bs.modal', function (e) {
        $("#start_price").focus();
    });

   

    $('#countryModel').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $(this).find("#save_newcountryBtn").removeAttr('data-action');
        $(this).find("#save_closecountryBtn").removeAttr('data-action');
        $(this).find("#save_newcountryBtn").removeAttr('data-id');
        $(this).find("#save_closecountryBtn").removeAttr('data-id');
        $('#country_id').val("");
        $('#name-error').html("");
        var default_image = "{{ asset('photos/default_avatar.jpg') }}";
        $('#thumb_img_image_show').attr('src', default_image);
     
        
    });

    $('#DeletecountryModel').on('hidden.bs.modal', function () {
        $(this).find("#RemovecountrySubmit").removeAttr('data-id');
    });

    function country_page_tabs(tab_type='',is_clearState=false) {
        if(is_clearState){
            $('#all_country').DataTable().state.clear();
        }

        $('#all_country').DataTable({
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
                "url": "{{ url('admin/allcountryslist') }}",
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
                { "width": "115px", "targets": 4 }
            ],
            "columns": [
                {data: 'id', name: 'id', class: "text-center", orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {data: 'name', name: 'name', class: "text-left multirow"},
                {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
                {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
                {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
            ]
        });
    }


    function changecountryStatus(country_id) {
        //var tab_type = get_users_page_tabType();
       
        $.ajax({
            type: 'GET',
            url: "{{ url('admin/changecountrystatus') }}" +'/' + country_id,
            success: function (res) {
                if(res.status == 200 && res.action=='deactive'){
                    $("#countrystatuscheck_"+country_id).val(2);
                    $("#countrystatuscheck_"+country_id).prop('checked',false);
                    country_page_tabs();
                    toastr.success("Country Deactivated",'Success',{timeOut: 5000});
                }
                if(res.status == 200 && res.action=='active'){
                    $("#countrystatuscheck_"+country_id).val(1);
                    $("#countrystatuscheck_"+country_id).prop('checked',true);
                    country_page_tabs();
                    toastr.success("Country activated",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }

    $('body').on('click', '#AddcountryBtn', function (e) {
        $("#countryModel").find('.modal-title').html("Add Country");
    });

    $('body').on('click', '#editcountryBtn', function () {
        var country_id = $(this).attr('data-id');
        $.get("{{ url('admin/country') }}" +'/' + country_id +'/edit', function (data) {
            $('#countryModel').find('.modal-title').html("Edit Country");
            $('#countryModel').find('#save_closecountryBtn').attr("data-action","update");
            $('#countryModel').find('#save_newcountryBtn').attr("data-action","update");
            $('#countryModel').find('#save_closecountryBtn').attr("data-id",country_id);
            $('#countryModel').find('#save_newcountryBtn').attr("data-id",country_id);
            $('#country_id').val(data.id);
            
            $('#name').val(data.name);
           
            if(data.thumb_img==null){
                var default_image = "{{ asset('photos/default_avatar.jpg') }}";
                $('#thumb_img_image_show').attr('src', default_image);
            }
            else{
                var thumb_img = "{{ url('images/country' ) }}"+ "/" + data.thumb_img;
                
                $('#thumb_img_image_show').attr('src', thumb_img);
            }
            
        })
    });

    $('body').on('click', '#deletecountryBtn', function (e) {
        var delete_country_id = $(this).attr('data-id');
        $("#DeletecountryModel").find('#RemovecountrySubmit').attr('data-id',delete_country_id);
    });

    $('body').on('click', '#RemovecountrySubmit', function (e) {
        $('#RemovecountrySubmit').prop('disabled',true);
        $(this).find('.removeloadericonfa').show();
        e.preventDefault();
        var remove_country_id = $(this).attr('data-id');
          
        //var tab_type = get_users_page_tabType();

        $.ajax({
            type: 'GET',
            url: "{{ url('admin/country') }}" +'/' + remove_country_id +'/delete',
            success: function (res) {
                if(res.status == 200){
                    $("#DeletecountryModel").modal('hide');
                    $('#RemovecountrySubmit').prop('disabled',false);
                    $("#RemovecountrySubmit").find('.removeloadericonfa').hide();
                    country_page_tabs();
                    toastr.success("Country Deleted",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#DeletecountryModel").modal('hide');
                    $('#RemovecountrySubmit').prop('disabled',false);
                    $("#RemovecountrySubmit").find('.removeloadericonfa').hide();
                    country_page_tabs();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#DeletecountryModel").modal('hide');
                $('#RemovecountrySubmit').prop('disabled',false);
                $("#RemovecountrySubmit").find('.removeloadericonfa').hide();
                country_page_tabs();
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

    $('body').on('click', '#viewplayerBtn', function () {
        var tournament_id = $(this).attr('data-id');
        var url = "{{ url('admin/player') }}" + "/" + tournament_id;
        window.open(url);
    });
</script>
<!-- user list JS end -->
@endsection


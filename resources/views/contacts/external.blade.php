@extends('layouts.master')
@section('title')
    External Contact | {{ \App\Configuration::first()->hotel_name.' '.\App\Configuration::first()->app_title }}
@endsection
@section('content')

    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h1>External Contact Upload</h1>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="body">
                                <form id="saveBlast" action="saveexternalcontact" method="post" enctype="multipart/form-data">
                                    {!! Form::token() !!}
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('file','Import Email Address') }}
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                                            <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                                                {{ Form::file('file') }}
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','') }}
                                        </div>
                                        <div class="form-group">
                                           New Category <input type="checkbox" class="js-switch" name="getcategory" id="getcategory"   onchange="selectCategory()"/>  Use Existing Category
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row" id="newcategory">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('new_category','Category') }}
                                        </div>
                                        <div class="col-sm-4 col-md-4 col-lg-4 col-xs-4" id="parent">
                                            <div class="form-group {{ $errors->has('new_category') ? 'has-error' : '' }} input-group">
                                                {{ Form::text('new_category[]',null,['class'=>'form-control','id'=>'new_category.0','placeholder'=>'New Category' ]) }}
                                                <span class="input-group-btn">
                                                <a href="#" onclick="event.preventDefault()" id="addField" class="btn btn-success"><i class="fa fa-plus" ></i></a>
                                                </span>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row" id="pickcategory">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('pick_category','Pick Category') }}
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                                            <div class="form-group {{ $errors->has('pick_category') ? 'has-error' : '' }}">
                                                {{ Form::select('pick_category[]',\App\ExternalContactCategory::pluck('category','id')->all(),null,['class'=>'form-control selectpicker','multiple','id'=>'pick_category','actionsBox'=>'true','data-live-search'=>'true']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <button class="btn btn-sm btn-success" id="saveEmailBlast" >Upload</button>
                                </form>
                            </div>
                        </div>
                        <hr>
                        <div class="card">
                            <div class="header">
                                <h1>External Contact </h1>
                            </div>
                            <div class="body">
                                <table class="table table-bordered table-striped table-hover  responsive " id="categoryList">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Category</th>
                                        <th>Contacts</th>
                                        <th>Action</th>

                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{--@foreach(\App\ExternalEmailCampaign::all() as $key=>$blast)--}}
                    {{--<div  class="modal " id="myModal{{ $blast->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">--}}
                        {{--<div class="modal-dialog modal-lg" role="document">--}}
                            {{--<div class="modal-content" >--}}
                                {{--<div class="modal-header">--}}
                                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                                    {{--<h4 class="modal-title" id="myModalLabel">{{ $blast->campaign_name }}</h4>--}}
                                {{--</div>--}}
                                {{--<div class="modal-body">--}}
                                    {{--{!! \App\MailEditor::find($blast->template_id)->content !!}--}}
                                {{--</div>--}}
                                {{--<div class="modal-footer">--}}
                                    {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}

                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div  class="modal " id="recepientModal{{ $blast->id }}" tabindex="-1" role="dialog" aria-labelledby="recepientModalLabel">--}}
                        {{--<div class="modal-dialog  modal-lg " role="document">--}}
                            {{--<div class="modal-content" >--}}
                                {{--<div class="modal-header">--}}
                                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                                    {{--<h4 class="modal-title" id="recepientModalLabel"> Campaign {{ $blast->campaign_name }} </h4>--}}
                                {{--</div>--}}
                                {{--<div class="modal-body">--}}
                                    {{--<table class="table table-bordered table-striped table-hover datatable responsive js-basic-example"  width="100%" id="datatable-responsive{{$blast->id}}">--}}
                                        {{--<thead>--}}
                                        {{--<tr>--}}
                                            {{--<th width="20%"> No </th>--}}
                                            {{--<th width="60%"> Name </th>--}}
                                            {{--<th width="20%"> Email </th>--}}
                                        {{--</tr>--}}
                                        {{--</thead>--}}
                                        {{--<tbody>--}}
                                        {{--@foreach($blast->email as $key=>$email)--}}
                                            {{--<tr>--}}
                                                {{--<td>{{ $key+1 }}</td>--}}
                                                {{--<td>{{ $email->fname.' '.$email->lname }}</td>--}}
                                                {{--<td>{{ $email->email }}</td>--}}
                                            {{--</tr>--}}
                                        {{--@endforeach--}}
                                        {{--</tbody>--}}
                                    {{--</table>--}}
                                {{--</div>--}}
                                {{--<div class="modal-footer">--}}
                                    {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--@endforeach--}}
            </div>


        </section>
    </div>
@endsection
@section('script')
    <script>




        function setSwitchery(switchElement, checkedBool) {
            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
                switchElement.setPosition(true);
                switchElement.handleOnchange(true);
            }
        }
        var mySwitch = new Switchery($('#getcategory')[0], {
            size:"small",
            color: '#0D74E9'
        });
        setSwitchery(mySwitch, false);
        $('#newcategory').show();
        $('#pickcategory').hide();
        </script>
    <script>
        function selectCategory(){

            var $this=$('#getcategory');
            if($this.is(':checked')){
                setSwitchery(mySwitch, true);
                $('#newcategory').hide();
                $('#pickcategory').show();
            }else {
                setSwitchery(mySwitch, false);
                $('#newcategory').show();
                $('#pickcategory').hide();
            }
        }
        $('#schedule').datetimepicker({
            format: 'DD MMMM YYYY hh:mm',
            minDate: new Date(),
            showClear:true,

        })

        function delElement() {
            $('form input, form select').removeClass('error');
            $('span.text-danger').remove();
        }
        $(document).ready(function(){
            var t=$('#categoryList').DataTable({
                //"deferRender":    true,
                //  "scrollY":        500,
                //  "scrollCollapse": true,
                //	"scroller":       true,
                "paging":true,
                //  "lengthChange": false,
                "stateSave":true,
                "ajax":{
                    "url":"{{ route('loadcategory') }}",
                    "dataSrc":"",
                    "type":"POST",
                    "data":{
                        "_token":"{{ csrf_token() }}"
                    }
                },
                "processing": true,
                "language":{
                    "processing":"<i class='fa fa-spinner fa-spin fa-3x fa-fw'></i><span class='sr-only'>Loading...</span> "
                },
                "columns": [
                    {"data":null},
                    { "data": "category" },
                    { "data": null },
                    { "data": null },


                ],
                "columnDefs": [
                    {
                        "targets": 0,
                        "data": "id",
                    },{
                        "targets":1,
                        "render":function (data,type,row) {
                           return data
                        }
                    },{
                        "targets":2,
                        "render":function (data,type,row) {
                                    var tr=''
                                  var table='<table id="tb'+data.id+'" ><thead>' +
                                      '<tr>' +
                                      '<th>No</th>' +
                                      '<th>Name</th>'+
                                      '<th>Contact</th></tr>'+

                                      '</thead><tbody>'
                                $.each(data.email,function (i,v) {
                                   // console.log(i)


                                   tr+='<tr><td>'+(i+1)+'</td><td>'+v.fname+' '+v.lname+'</td><td>'+v.email+'</td></tr>'
                                   // console.log(tr)
                                })
                                 table+=tr+'</tbody></table>'
                                 var modal='<div class="modal fade" id="contactModal'+data.id+'" tabindex="-1" role="dialog" aria-labelledby="contactModalTitle" aria-hidden="true">\n' +
                                     '  <div class="modal-dialog modal-dialog-centered" role="document">\n' +
                                     '    <div class="modal-content">\n' +
                                     '      <div class="modal-header">\n' +
                                     '        <h5 class="modal-title" id="contactModalTitle">Modal title</h5>\n' +
                                     '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
                                     '          <span aria-hidden="true">&times;</span>\n' +
                                     '        </button>\n' +
                                     '      </div>\n' +
                                     '      <div class="modal-body">\n' +
                                     '        '+table+'' +
                                     '      </div>\n' +
                                     '      <div class="modal-footer">\n' +
                                     '        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>\n' +
                                     '      </div>\n' +
                                     '    </div>\n' +
                                     '  </div>\n' +
                                     '</div>'
                                $('#contactModal'+data.id).append(table)
                                $('.content').append(modal)
                            $('#contactModal'+data.id+' table').addClass('table table-bordered table-striped table-hover  responsive')
                                var tb=$('#contactModal'+data.id+' table').not('.modal-body table').remove()

                            return '<a href="#" class="btn btn-default" data-toggle="modal" data-target="#contactModal'+data.id+'">Show Contacts</a>'

                        }
                    },{
                        "targets":3,
                        "render":function (data,type,row) {
                            return '<a href="#" onclick="event.preventDefault(); delCategory(this.id)" class="btn btn-default"  id="category'+data.id+'"><i class="fa fa-trash"></i> </a>'
                        }

                    }
                    ],
                "pageLength":20,
                "createdRow": function( row, data, dataIndex){
                    $('td',row).eq(1).css('text-transform', "capitalize")
                },

            })
            t.on( 'order.dt search.dt ', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();




            var counter=0;
            $('#addField').on('click',function () {
                counter+=1
                var el=$(document.createElement('div')).attr('class','form-group  input-group remField'+counter+'');
                var el2='<input class="form-control" id="new_category.'+counter+'" placeholder="New Category" name="new_category[]" type="text"><span class="input-group-btn"> <a href="" class="btn btn-danger" onclick="event.preventDefault();remField(this.id)" id="remField'+counter+'"><i class="fa fa-minus"></i> </a>  </span>'
                el.append(el2)
                var par=$('#parent').append(el)
            })
            //based on: http://stackoverflow.com/a/9622978

            $('#saveBlast').on('submit', function(e){
                e.preventDefault();
                delElement()
                var form = e.target;
                var data = new FormData(form);
                $.ajax({
                    url: form.action,
                    method: form.method,
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function(data){
                        if (data.errors){
                            $.each(data.errors,function(i,v){
                                console.log(i)
                                var msg = '<span class="text-danger" id="'+i+'">'+v+'</span>';
                                $('input[name="' + i + '"], select[id="' + i + '"],input[id="' + i + '"]').addClass('error').after(msg);
                            })
                        } else {
                            swal({title:'Success',text:'Email blast has been added',type:'success'},function () {
                                location.reload()
                            })
                        }
                    }
                })
            })
        })
        function remField(id) {
            var el=$('.'+id).remove()

        }
        function delCategory(id) {
            swal({title:'Delete Confirmation',text:'This Category will permanently deleted',type:'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText:'Delete',
                    cancelButtonText: 'No',
                    closeOnConfirm: false,
                    closeOnCancel: false
                },
                function(isConfirm){
                    if (isConfirm) {
                        var idnum=id.replace('category','')
                        $.ajax({
                            url:'{{ route('delcategory') }}',
                            type:'POST',
                            data:{
                                _token:'{{ csrf_token() }}',
                                id:idnum
                            },success:function (d) {
                                if(d==='success') {
                                    location.reload()
                                }
                            }
                        })
                    } else {
                        swal('Cancelled', 'Delete Template Cancelled','error');
                    }
                })
        }
    </script>
@endsection

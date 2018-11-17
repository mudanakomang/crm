@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            @foreach($template as $item)
                <div  class="modal " id="myModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content" >
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">{{ $item->name }}</h4>
                            </div>
                            <div class="modal-body">
                                {!! $item->content !!}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="cloneTemplate{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="cloneTemplateLable" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cloneTemplateLable{{ $item->id }}">Clone Template</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="templateClone{{ $item->id }}" >
                                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                                        {{ Form::label('tname','New Template Name') }}
                                    </div>
                                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <div class="form-line">
                                                {{ Form::text('tname',null,['class'=>'form-control','id'=>'tname','data-live-search'=>'true','required','placeholder'=>'New Template Name']) }}
                                            </div>
                                            <span class="text-danger">
                                            <strong id="tname-error">
                                            </strong>
                                            </span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="template_id" id="template_id{{$item->id}}" value="{{$item->id}}">
                                    <div class="col-lg-3 col-md-3 col-sm-8 col-xs-8 form-control-label">
                                        {{ Form::label('','Preview:') }}
                                    </div>
                                    {!! $item->content !!}
                                    <div class="previewtemplate">

                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                                <a href="#" id="{{ $item->id }}" class="btn btn-sm btn-success" onclick="return submitClone(this.id)">Save</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel title">
                            <div class="x_title">
                                <h2>Create Email Template</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                                <a href="{{ url('email/create') }}" title="Create New Template" class=" btn btn-sm btn-success"> <i class="fa fa-plus"></i> Create New Template</a>
                            </div>
                            <div class="x_content">
                                <table class="table table-bordered table-striped table-hover dataTable js-basic-example">
                                    <thead>
                                    <th width="10px">No</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th >Manage</th>
                                    </thead>
                                    <tbody>
                                    @foreach($template as $key=>$tem)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td> {{ $tem->name }} </td>
                                            <td>{{ $tem->type }}</td>
                                            <td>{!! Form::open(['method' => 'DELETE','route' => ['email.destroy', $tem->id],'id'=>$tem->id]) !!}
                                                {!! Form::close() !!}
                                                <a href="#" title="Delete Template" onclick="return swal({title:'Delete Confirmation',text:'This Template will permanently deleted',type:'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#DD6B55',
                                                        confirmButtonText:'Delete',
                                                        cancelButtonText: 'No',
                                                        closeOnConfirm: false,
                                                        closeOnCancel: false
                                                        },
                                                        function(isConfirm){
                                                        if (isConfirm) {
                                                        $('#{{$tem->id}}').submit();
                                                        } else {
                                                        swal('Cancelled', 'Delete Template Cancelled','error');
                                                        }
                                                        });"><i class="fa  fa-trash" style="font-size: 1.5em"></i> </a> <a href="{{ url('email/'.$tem->id.'/edit') }}" title="Edit"><i class="fa  fa-edit" style="font-size: 1.5em"></i> </a>  <a href="#myModal{{$tem->id}}" data-toggle="modal" data-target="#myModal{{$tem->id}}"><i class="fa  fa-eye" style="font-size: 1.5em"></i> </a>
                                                         <a href="#cloneTemplate{{$tem->id}}" data-toggle="modal" data-target="#cloneTemplate{{$tem->id}}" id="copyTempalte" title="Copy Template" ><i class="fa fa-copy" style="font-size: 1.5em"></i> </a>

                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
@section('script')
    <script>
        function submitClone(id) {

            var form=$('#templateClone'+id)
            var name=form.find('#tname');
            $.ajax({
                url:'saveclone',
                type:'post',
                data:{
                    tid:id,
                    name:name.val(),
                    _token:'{{ csrf_token() }}'
                },success:function (data) {
                    if(data==='success'){
                        swal({
                            title: "Sucess",
                            text: "Template Cloned",
                            type: "success",
                        },function() {
                            window.location.reload();
                        });
                    }else {
                        if(data.errors.name){
                            swal('', data.errors.name[0], 'warning')
                        }
                    }
                }
            })
        }
        $('.dataTable').dataTable();
    </script>
    @endsection
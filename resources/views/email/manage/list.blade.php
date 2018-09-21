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
                                                        });"><i class="fa fa-trash"></i> </a> | <a href="{{ url('email/'.$tem->id.'/edit') }}" title="Edit"><i class="fa fa-edit"></i> </a> | <a href="#myModal{{$tem->id}}" data-toggle="modal" data-target="#myModal{{$tem->id}}"><i class="fa fa-eye"></i> </a>

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

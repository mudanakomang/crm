@extends('layouts.app')
@section('main-content')

    @foreach($campaigns as $item)
        <!-- Button trigger modal -->
        <!-- Modal -->
        <div  class="modal " id="myModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">{{ $item->name }}</h4>
                    </div>
                    <div class="modal-body">
                        {!!  $item->template[0]->content !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    </div>
                </div>
            </div>
        </div>
        @if($item->status=='Draft' || $item->status=='Scheduled')
            <div  class="modal " id="ScheduleModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="ScheduleModalLabel">
                <div class="modal-dialog " role="document">
                    <div class="modal-content" >
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="ScheduleModalLabel">{{ $item->name }} Schedule</h4>
                        </div>
                        <div class="modal-body">
                            <label for="schedule{{$item->id}}}" class="control-label">Schedule</label>
                            <input type="text" class="form-control datetimepicker" name="schedule{{$item->id}}" id="schedule{{ $item->id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" id="scheduleSave{{$item->id}}">Save</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        </div>
                    </div>
                </div>
            </div>
            @endif

    @endforeach

<section class="content">
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Create New Campaign</h2>

                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="{{ url('campaign/create') }}" title="Create New Campaign" class="waves-effect btn btn-sm bg-teal"> <i class="material-icons">add</i> Create New Campaign</a>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <table class="table  table-striped table-hover dataTable js-basic-example">
                        <thead>
                        <th width="10px">No</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th >Manage</th>
                        </thead>
                        <tbody>
                        @foreach($campaigns as $key=>$tem)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $tem->name }} </td>
                                <td class="status{{ $tem->id }}"> {{ $tem->status }} {{ $tem->status=='Scheduled' ? '@'.\Carbon\Carbon::parse($tem->schedule->schedule)->format('d M Y H:i'):'' }}</td>
                                <td>{!! Form::open(['method' => 'DELETE','route' => ['campaign.destroy', $tem->id],'id'=>$tem->id]) !!}
                                    {!! Form::close() !!}
                                        <a href="#myModal{{$tem->id}}" data-toggle="modal" title="Preview Template" data-target="#myModal{{$tem->id}}"><i class="material-icons">pageview</i> </a>
                                             @if($tem->status=='Draft' || $tem->status=='Scheduled')
                                            <a href="#ScheduleModal{{$tem->id}}" data-toggle="modal" data-target="#ScheduleModal{{$tem->id}}" title="Set Schedule"> <i class="material-icons">access_alarm</i></a>
                                            @endif
                                            <a href="#" title="Duplicate Campaign" id="duplicate{{$tem->id}}"><i class="material-icons">file_copy</i></a>
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
                                            });"><i class="material-icons">delete_forever</i> </a>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection
@section('script')
    <script>
        $('[id^=scheduleSave]').on('click',function () {
            var id_=this.id;
            id_=id_.replace('scheduleSave','')
            var val=$('#schedule'+id_).val();
            $.ajax({
                url:'setschedule',
                type:'POST',
                data:{
                    id:id_,
                    _token:'{{ csrf_token() }}',
                    value:val,
                },
                success:function () {
                 location.reload(true);
                }
            })
        })
    </script>
@endsection

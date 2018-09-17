@extends('layouts.app')
@section('main-content')
    <section class="content">
        <div class="container">
            <div class="row clearfix">
                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10">
                    <div class="card">
                        <div class="header">
                            <h2>Import Contacts</h2>
                        </div>
                        <div class="body">
                            <a href="{{ url('contacts/template/contact') }}" class="btn btn-xs btn-flat bg-teal">Download Template</a>
                            <div class="row clearfix">
                                {{ Form::open(['url'=>'contacts/upload/contact','class'=>'form-horizontal','files'=>'true']) }}
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                    {{ Form::label('file','Select File') }}
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                    {{ Form::file('file') }}
                                </div>
                                <div class="row clearfix">
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                        {{ Form::submit('upload',['class'=>'btn btn-xs btn-flat bg-teal']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            <div class="row clearfix">
                                @if(!empty($create))
                                    <div class="modal fade" id="largeModal1" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="largeModalLabel">{{ $create }} rows Added</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table dataTable js-basic-example">
                                                        <thead class="">
                                                        <tr>
                                                            <th class="align-center">Full Name</th>
                                                            <th class="align-center">Salutation</th>
                                                            <th class="align-center">Gender</th>
                                                            <th class="align-center">Birthday</th>
                                                            <th class="align-center">Country</th>
                                                            <th class="align-center">Booking Source</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($created_data as $data)
                                                            <tr>
                                                                <td>{{ $data['fname'].' '.$data['lname'] }}</td>
                                                                <td>{{ $data['salutation'] }}</td>
                                                                <td>{{ $data['gender'] }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($data['birthday'])->format('d M Y') }}</td>
                                                                <td>{{ \App\Country::where('id','=',$data['country_id'])->value('country') }}</td>
                                                                <td>{{ $data['source_booking'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($update))
                                        <div class="modal fade" id="largeModal2" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="largeModalLabel">{{ $update }} rows Updated</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table dataTable js-basic-example">
                                                            <thead class="">
                                                            <tr>
                                                                <th class="align-center">Full Name</th>
                                                                <th class="align-center">Salutation</th>
                                                                <th class="align-center">Gender</th>
                                                                <th class="align-center">Birthday</th>
                                                                <th class="align-center">Country</th>
                                                                <th class="align-center">Booking Source</th>


                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach($updated_data as $data)
                                                                <tr>
                                                                    <td>{{ $data['fname'].' '.$data['lname'] }}</td>
                                                                    <td>{{ $data['salutation'] }}</td>
                                                                    <td>{{ $data['gender'] }}</td>
                                                                    <td>{{ \Carbon\Carbon::parse($data['birthday'])->format('d M Y') }}</td>
                                                                    <td>{{ \App\Country::where('id','=',$data['country_id'])->value('country') }}</td>
                                                                    <td>{{ $data['source_booking'] }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                @endif
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
@section('script')
    @if(!empty($update))
        <script>
            $(window).on('load',function () {
                $('#largeModal2').modal('show');
            })
        </script>
    @endif
    @if(!empty($create))
        <script>
            $(window).on('load',function () {
                $('#largeModal1').modal('show');
            })
        </script>
    @endif
    @endsection
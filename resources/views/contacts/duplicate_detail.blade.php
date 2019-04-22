@extends('layouts.master')
@section('title')
    Duplicate Contacts  | {{ config('app.name') }}
@endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>Duplicate Contact List</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12">
                                </div>
                            </div>

                            <div class="body">
                                <table class="table table-bordered table-striped table-hover datatable responsive js-basic-example" id="datatable-responsive">
                                    <thead class="bg-teal">
                                    <tr>
                                        <th class="align-center">#</th>
                                        <th class="align-center">Folio</th>
                                        <th class="align-center">Folio Master</th>
                                        <th class="align-center">Full Name</th>
                                        <th class="align-center">Status</th>
                                        <th class="align-center">Room</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dups as $key=>$contact)
                                        <tr class="align-center">
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $contact->folio }}</td>
                                            <td>{{ $contact->folio_master }}</td>
                                            <td>
                                                @if(!empty($contact->lname))
                                                    {{ ucwords(strtolower($contact->fname)).' '.ucwords(strtolower($contact->lname)) }}
                                                @else
                                                    {{ ucwords(strtolower($contact->fname)) }}
                                                @endif
                                            </td>


                                            <td>
                                                {{ $contact->status }}
                                            </td>


                                            <td>
                                                {{ $contact->room }}
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
@endsection

@extends('layouts.master')
@section('title')
	Contact List | {{ config('app.name') }}
	@endsection
@section('content')
	<div class="right_col" role="main">
		<section class="content">
			<div class="container-fluid">
				<div class="row clearfix">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="card">
							<div class="header">
								<h2>Contact List</h2>
							</div>
							<div class="row clearfix">
								<div class="col-lg-12">
									{{--<div class="col-lg-6 col-md-6  ">--}}
										{{--<a href="{{ url('contacts/add') }}" class="btn btn-sm btn-primary waves-effect">--}}
											{{--<i class="fa fa-plus"></i>--}}
											{{--<span>Add Contact</span>--}}
										{{--</a>--}}
									{{--</div>--}}
								</div>
							</div>

							<div class="body">
								<table class="table table-bordered table-striped table-hover datatable responsive js-basic-example" id="datatable-responsive">
									<thead class="bg-teal">
									<tr>
										<th class="align-center">#</th>
										<th class="align-center">Full Name</th>
										<th class="align-center">Birthday</th>
										<th class="align-center">Country</th>
										<th class="align-center">Status</th>
										<th class="align-center">Campaign</th>
										<th class="align-center">Total Stays</th>
										<th class="align-center">Last Stay</th>
										<th class="align-center">Total Spending (Rp.)</th>
										{{--<th class="align-center">Action</th>--}}
									</tr>
									</thead>
									<tbody>
									@foreach($data as $key=>$contact)
										<tr class="align-center">
											<td>{{ $key+1 }}</td>
											<td>
												@if(!empty($contact->lname))
													<a href="{{ url('contacts/detail/').'/'.$contact->contactid }}" >{{ $contact->fname.' '.$contact->lname }}</a>
												@else
													<a href="{{ url('contacts/detail/').'/'.$contact->contactid }}" >{{ $contact->fname }}</a>
												@endif
												@if( $contact->birthday=='' ? '': \Carbon\Carbon::parse($contact->birthday)->format('m-d')==\Carbon\Carbon::now()->format('m-d'))
													<i class="fa fa-birthday-cake " style="color: #009688" ></i>
												@endif

											</td>
											<td>{{ $contact->birthday=='' ? "": \Carbon\Carbon::parse($contact->birthday)->format('M d') }}</td>
											<td>{{ \App\Country::where('iso2',$contact->country_id)->first()['country'] }}
												<img src="{{ asset('flags/blank.gif') }}" class="flag flag-{{strtolower($contact->country['iso2'])}} pull-right" alt="{{$contact->country['country']}}" />
											</td>

											<td>
												@if(count($contact->transaction)<>0)
													@if($contact->transaction[0]->status=='I')
														Inhouse
														@elseif($contact->transaction[0]->status=='C')
														Confirm
														@elseif($contact->transaction[0]->status=='X')
														Cancel
														@else
														Check Out
														@endif
												@endif
											</td>


											<td> @if(count($contact->campaign) <> 0)
													 	<i class="fa fa-envelope success"></i>
														{{ $contact->campaign->where('status','=','Sent')->count()}} Campaign
												@endif
											</td>

											<td>

												{{ ($contact->transaction->first()['status']=='X' || $contact->transaction->first()['status']=='C') ? 0: count($contact->transaction) }}
											</td>
											<td>
												{{ $contact->transaction->max('checkin')==NULL ? "": \Carbon\Carbon::parse($contact->transaction->max('checkin'))->format('d M Y') }}
											</td>
											<td>
												{{ number_format($contact->transaction->sum('revenue'),0,'.',',')}}
											</td>
											{{--<td>--}}
												{{--<a href="{{url('contacts/stay/add/').'/'.$contact->contactid}}" title="Add Stay"><i class="fa fa-hotel fa-2x "></i></a>--}}
											{{--</td>--}}
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
	{{--<script>--}}
		{{--$('.dataTable').DataTable();--}}
	{{--</script>--}}
@endsection
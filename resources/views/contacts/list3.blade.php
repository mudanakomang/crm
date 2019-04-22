@extends('layouts.master')
@section('title')
	Contact List | {{ \App\Configuration::first()->hotel_name.' '.\App\Configuration::first()->app_title }}
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
								</div>
							</div>

							<div class="body">
								<table style="font-size: 13px" class="table table-bordered table-striped table-hover responsive js-basic-example  " id="loadcontacts">
									<thead class="bg-teal">
									<tr>
										<th class="align-center">No</th>
										<th class="align-center">Full Name</th>
										<th class="align-center">Last Name</th>
										<th class="align-center">Birthday</th>
										<th class="align-center">Wedding Birthday</th>
										<th class="align-center">Country</th>
										<th class="align-center">Area/Origin</th>
										<th class="align-center">Status</th>
										<th class="align-center">Campaign</th>
										<th class="align-center">Total Stays</th>
										<th class="align-center">Last Stay</th>
										<th class="align-center">Total Spending (Rp.)</th>
									</tr>
									</thead>

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

    $(document).ready(function(){
        var item=[];
        var path="{{ asset('countries.json') }}"
        var json = $.getJSON(path,function (d) {
            $.each(d,function (i) {
                item.push(d[i])
            })
        })
       var t= $('#loadcontacts').DataTable({
           "autoWidth": true,
            "processing": true,
            "serverSide": true,
		   	"pageLength":20,
            "ajax":{
                "url": "{{ route('contactslist') }}",
                "dataType": "json",
                "type": "POST",
                "data":{
                    _token: "{{csrf_token()}}",
					route:'list'
                }
            },
            "columns": [
                { "data": "contactid" },
                { "data": "fname" },
                { "data": "lname" },
                { "data": "birthday" },
				{ "data": "wedding_bday"},
				{ "data": "country_id"},
				{ "data": "area"},
				{ "data": "status","name":"transaction.status"},
				{ "data": "campaign"},
				{ "data": "stay"},
				{ "data": "checkin"},
				{ "data": "revenue","name":"transaction.revenue"},

            ],
		   	"columnDefs":[
                {
                    "targets":1,
                    "render":function (data,type,row) {
                        var id=row.contactid
                        return '<a href="{{ url('contacts/detail/') }}'+'/'+id+'" >'+ data +' ' +row.lname+'</a>'
                    }
                },{
                	"targets":2,
					"visible":false,
				},{
                    "targets":[3,4],
                    "render":function (data,type,row) {
                        if(moment(data).isValid()){
                            return moment(data).format("MMM DD")
                        }else {
                            return ''
                        }
                    }
                },
                {
                    "targets":5,
                    "render":function (data,type,row) {
                        for(var i in item){
                            if(data===item[i]['iso3']){
                                var d=''
                                d=(item[i]["iso2"])
                                d=d.toLowerCase()
                                var  country=item[i]["country"];
                                return country +'<img src="../flags/blank.gif"  class="flag flag-'+d+' pull-right"  alt="'+data+'" />';
                            }
                        }
                    }
                },{
                    "targets":7,
					"sortable":false,
                    "render":function (data,type,row) {
                        switch (data){
                            case 'C':
                                return "Confirm";
                                break
                            case 'O':
                                return "Checkout"
                                break
                            case "I":
                                return "Inhouse"
                                break
                            case "X":
                                return "Cancel"
                                break
                            case "G":
                                return "Guaranteed"
                                break
                            default:""
                        }
                    }
                },{
                	"targets":10,
					"sortable":false,
					"render":function (data,type,row) {
                        if(moment(data).isValid()){
                            return moment(data).format("MMM DD YYYY")
                        }else {
                            return ''
                        }
                    }
				}
			],
       });

    });
</script>
@endsection

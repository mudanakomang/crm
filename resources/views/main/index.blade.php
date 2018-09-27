@extends('layouts.master')
@section('content')


    <div class="right_col" role="main">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel tile " >
                    <div class="x_title">

                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>

                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height:120px;">
                        <div class="row tile_count">
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Contacts</span>
                                <div class="count green"> {{ \App\Contact::all()->count()  }} </div>

                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Males</span>
                                <div class="count blue">{{ \App\Contact::where('gender','M')->count() }}</div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Females</span>
                                <div class="count orange">{{ \App\Contact::where('gender','F')->count() }}</div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Inhouse </span>
                                <div class="count green"> {{ \App\Contact::whereHas('transaction',function ($q){
                                    return $q->where('status','=','I');
                                    })->count()  }} </div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Confirm </span>
                                <div class="count red"> {{ \App\Contact::whereHas('transaction',function ($q){
                                    return $q->where('status','=','C');
                                    })->count()  }} </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- top tiles -->
        <!-- /top tiles -->
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel tile " style="height: 420px">
                    <div class="x_title">
                        <h2>Contacts Added</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" >
                        <div class="dashboard-widget-content">
                            <div id="added" class="dashboard-donut-chart "></div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel tile  " style="height: 420px; ">
                    <div class="x_title">
                        <h2>Guest based on Country</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:350px;">

                        <table class="" style="width:100%">

                            <tr>
                                <td>
                                    <div id="donut_chart" class="dashboard-donut-chart "></div>
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel tile " style="height: 420px">
                    <div class="x_title">
                        <h2>Top 10 Spending</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>

                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="dashboard-widget-content">
                            <div id="top10rev" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Incoming Birthdays </h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="overflow-y: scroll;height: 350px" >
                        <div class="dashboard-widget-content">
                            <ul class="list-unstyled timeline widget">
                                @foreach($data as $key=>$value)
                                    <li>
                                        <div class="block">
                                            <div class="block_content">
                                                <h2 class="title">
                                                    <a href="{{ url('/contacts/detail/').'/'.$value->contactid }}" >  {{ $value->fname .' '.$value->lname }} </a>
                                                </h2>
                                                <div class="byline">
                                                    <span> <div class="number font-30">{{ \Carbon\Carbon::parse($value->birthday)->format('M d') }}</div></span>
                                                </div>

                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            {{ Form::open(['url'=>'contacts/birthday/search']) }}
                            {{ Form::hidden('days','7') }}
                                <button class="btn btn-sm" type="submit">More..</button>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Longest Stay</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height:350px">
                        <div class="dashboard-widget-content">
                            <div id="longest" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Contacts By Room Type</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height: 350px">
                        <div  class="dashboard-widget-content">
                            <div id="roomtype" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Contacts by Ages</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>
                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content" style="height: 350px;">
                        <div  class="dashboard-widget-content">
                            <div id="ages" class="dashboard-donut-chart "></div>
                        </div>
                    </div>
                    <!-- end of weather widget -->
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var total={!! $total !!};
        var data={!! $country !!};
        Morris.Donut({
            element: 'donut_chart',
            data:  data,
            resize:true,
            // colors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],

            formatter: function (y) {
                var res=y/total * 100;
                res=Math.round(res);
                return res  + '%'
            }
        }).on('click',function (i,row) {
            window.location.href='contacts/f/country/'+row.label;
        });

        Morris.Bar({
            element: 'top10rev',
            resize: true,
            data:{!! $spending !!},
            xkey: 'x',
            ykeys: ['y'],
            labels: ['Spending Rp'],
            barColors: function (row, series, type) {
                var blue = Math.ceil((255 * row.y / this.ymax)+255/10 );
                return 'rgb(0,100,'+blue+')';
            }
     //           ['rgb(255,0,80)']
        }).on('click',function (i,row) {
            window.location.href='contacts/f/spending/'+row.x;
        });
        Morris.Bar({
            element:'added',
            resize:true,
            data:{!! $monthcount !!},
            xkey:'x',
            ykeys:['y'],
            labels:['Contact Added'],
            barColors:function (row, series, type) {
                var red = Math.ceil((255 * row.y / this.ymax)+255/5 );
                return 'rgb('+red+',0,0)';
            }
        }).on('click',function(i,row){
            window.location.href='contacts/f/created/'+row.x;
        });
        Morris.Bar({
            element:'longest',
            resize:true,
            data:{!! $longstay !!},
            xkey:'x',
            ykeys:['y'],
            labels:['Nights'],
            barColors:function (row, series, type) {
                var red = Math.ceil((255 * row.y / this.ymax)+255/5 );
                return 'rgb('+red+',0,0)';
            }
        }).on('click',function(i,row){
            window.location.href='contacts/f/longest/'+row.x;
        });
        var troom='{!! $troom !!}';
        Morris.Donut({
            element: 'roomtype',
            data: {!!  $room_type !!},
            resize:true,
            // colors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],

            formatter: function (y) {
                var res=y/troom * 100;
                res=Math.round(res);
                return res  + '%'
            }
        }).on('click',function (i,row) {
            window.location.href='contacts/f/roomtype/'+row.label;
        });
        var tages='{!! $tages !!}'
        Morris.Donut({
            element: 'ages',
            data: {!!  $data_age !!},
            resize:true,
            // colors: ['rgb(233, 30, 99)', 'rgb(0, 188, 212)', 'rgb(255, 152, 0)', 'rgb(0, 150, 136)', 'rgb(96, 125, 139)'],

            formatter: function (y) {
                var res=y/tages * 100;
                res=Math.round(res);
                return res  + '%'
            }
        });
    </script>
    @endsection
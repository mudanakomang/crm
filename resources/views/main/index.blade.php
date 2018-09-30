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
                                <div class="count green"> <a href="{{ url('contacts/list') }}" class="green" >{{ \App\Contact::all()->count()  }}</a> </div>

                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Males</span>
                                <div class="count blue"><a href="{{ url('contacts/f/male') }}" class="green" >{{ \App\Contact::where('gender','M')->count() }}</a></div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Total Females</span>
                                <div class="count orange"><a href="{{ url('contacts/f/female') }}" class="green" >{{ \App\Contact::where('gender','F')->count() }}</a></div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Inhouse </span>
                                <div class="count green"> <a href="{{ url('contacts/f/status/Inhouse') }}" class="green" >{{ \App\Contact::whereHas('transaction',function ($q){
                                    return $q->where('status','=','I')->whereRaw('date_format(now(),\'%Y-%m-%d\') between checkin and checkout');
                                    })->count()  }}</a> </div>

                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                                <span class="count_top"><i class="fa fa-user"></i> Confirm </span>
                                <div class="count red"> <a href="{{ url('contacts/f/status/Confirm') }}" class="green" > {{ \App\Contact::whereHas('transaction',function ($q){
                                    return $q->where('status','=','C')->whereRaw('checkin > date_format(now(),\'%y-%m-%d\')');
                                    })->count()  }} </a></div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- top tiles -->
        <!-- /top tiles -->
        <div class="row">

            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="col-md-4 col-sm-4 col-xs-12">
                    <div class="x_panel tile " style="height: 420px">
                        <div class="x_title">
                            <h2>Tripadvisor Rating </h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" >
                            <div class="dashboard-widget-content">
                                <div class="star" style="color:#1ABB9C;padding: 20px">
                                    <h4>Rating  {{$reviews->aggregateRating->ratingValue}} of {{$reviews->aggregateRating->reviewCount}} Travelers</h4>
                                    @for($i=0;$i<5;$i++ )
                                            @if($i<$reviews->aggregateRating->ratingValue)
                                                <span><i class="fa fa-star fa-2x"></i> </span>
                                                @else
                                            <span><i class="fa fa-star-o fa-2x"></i> </span>
                                            @endif

                                    @endfor
                                </div>
                                @foreach($reviews->rating as $rating)
                                <div class="widget_summary">
                                    <div class="w_left w_25">
                                        <span>{{ $rating->label }}</span>
                                    </div>
                                    <div class="w_center w_55">
                                        <div class="progress">
                                            <div class="progress-bar bg-green" role="progressbar" aria-valuenow="{{ $rating->value }}" aria-valuemax=" {{ $reviews->aggregateRating->reviewCount  }}" aria-valuemin="0" style="width: {{$rating->value/$reviews->aggregateRating->reviewCount*100 }}%" >
                                                <span class="sr-only">{{ $rating->value }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w_right w_25">
                                        <span>{{ $rating->value }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-sm-8 col-xs-12">
                    <div class="x_panel tile " style="height: 420px">
                        <div class="x_title">
                            <h2>Tripadvisor Review</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content"  style="overflow-y:auto; overflow-x:hidden; height:350px;">
                            <div class="dashboard-widget-content">

                                  <ul  class="list-unstyled ">
                                  @foreach($reviews->reviews as $review)
                                        <li >
                                         <div class="block" style="border-bottom: 1px solid">
                                             <a class="pull-left border-green profile_thumb">
                                                 <img src="{{ url($review->avatar) }} "  width="75px" alt="" style="padding-top: 20px" >
                                             </a>
                                             <div class="media" style="padding: 10px">
                                             <h4 class="title">
                                                 <a href="{{ url($review->link) }}">{{ $review->quotes }}</a>
                                             </h4>
                                             <div class="byline">{{ $review->member }}
                                                 <div class="star" style="color:#1ABB9C">
                                                 @for($i=0;$i<5;$i++ )
                                                     @if($i<$review->rate/10)
                                                         <span><i class="fa fa-star "></i> </span>
                                                     @else
                                                         <span><i class="fa fa-star-o"></i> </span>
                                                     @endif

                                                 @endfor
                                                 </div>
                                             </div>
                                             <p class="excerpt">
                                                 {{ $review->review }}
                                             </p>

                                             </div>

                                         </div>
                                        </li>



                                  @endforeach
                                  </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

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
                var red = Math.ceil((255 * row.y / this.ymax)+255/6);
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
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">
        <h3>Menu</h3>
        <ul class="nav side-menu">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i>Dashboard</a></li>
            <li><a><i class="fa fa-user"></i> Contact Management <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                    <li><a href="{{ url('contacts/list') }}">View Contacts</a></li>
                    {{--<li><a href="{{ url('contacts/add') }}">New Contact</a></li>--}}
                    <li><a href="{{ url('contacts/filter') }}">Filter Contact</a></li>
                    {{--<li><a href="{{ url('contacts/import') }}">Import Guest Profile</a></li>--}}
                    {{--<li><a href="{{ url('contacts/importstay') }}">Import Guest Stay</a></li>--}}
                </ul>
            </li>
            <li><a><i class="fa fa-envelope"></i> Email Marketing <span class="fa fa-chevron-down"></span></a>
                <ul class="nav child_menu">
                        <li><a href="{{url('campaign') }}">Campaign Management</a></li>
                        <li><a href="{{url('email/config/poststay')}}">Post-Stay Configuration</a></li>
                        <li><a href="{{url('email/config/birthday')}}">Birthday Configuration</a></li>
                        <li><a href="{{url('email/config/confirm')}}">Check in Confirmation</a></li>
                        <li><a href="{{url('email/config/miss')}}">We Miss You Letter</a></li>
                        <li><a href="{{url('email/template')}}">Email Template</a></li>
                </ul>
            </li>
            <li><a href="{{ url('reviews') }}"><i class="fa fa-comments-o"></i>Reviews</a>
            </li>
        </ul>

    </div>


</div>
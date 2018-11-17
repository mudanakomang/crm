@extends('layouts.master')
@section('title')
    Preferences | {{ config('app.name') }}
    @endsection
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>Dashboard Setting</h2>
                            </div>
                            <div class="row clearfix">
                                <div class="col-lg-12 ">
                                    <h4>Show/Hide Panel</h4>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeAdded"> Contact Added
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeCountry"> Contacts By Country
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeSpending"> Top 10 Spending
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeIncoming"> Incoming Birthday
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeLong"> Contacts By Longest Stay
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeRoomType"> Contacts By Room Type
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeAge"> Contacts By Age
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeBooking"> Contacts By Booking Source
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="closeStay"> Contacts By Stay
                                        </label><br>
                                        <label>
                                            <input type="checkbox" class="flat dashboard"  id="collapseSidebar"> Collapse Sidebar
                                        </label><br>
                                    </div>
                                </div>
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

            $(document).ready(function () {

                $('.dashboard').each(function () {
                    var id = $(this).attr('id')
                    $(this).iCheck('check')
                    $(this).closest("input").attr('checked', true);
                    if (readCookie(id) == 0) {
                        $(this).closest('input').removeAttr('checked')
                    }
                    var state = readCookie(id)
                    $(this).on('ifChanged', function () {
                        if (state == 0) {
                            eraseCookie(id)
                        } else {
                            createCookie(id, 0, 1)
                            console.log(id)
                        }
                    })

                })
            })
            function createCookie(name, value, days) {
                var expires;
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toGMTString();
                } else {
                    expires = "";
                }
                document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
            }

            function readCookie(name) {
                var nameEQ = encodeURIComponent(name) + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ')
                        c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0)
                        return decodeURIComponent(c.substring(nameEQ.length, c.length));
                }
                return null;
            }

            function eraseCookie(name) {
                createCookie(name, "", -1);
            }

        </script>
    @endsection
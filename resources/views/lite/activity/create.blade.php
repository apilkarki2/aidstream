@extends('lite.base.sidebar')

@section('title', 'Activities')

@section('content')
    {{Session::get('message')}}
    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel__heading">
                <h1>@lang('lite/global.add_an_activity')</h1>
                <p>@lang('lite/global.add_an_activity_in_simple_steps')</p>
            </div>
            <div class="panel__body">
                <div class="panel__form">
                    <div class="create-form create-project-form edit-form">
                        {!! form_start($form) !!}
                        <div class="form__block" id="basics">
                            <div class="col-md-9">
                                <h2>@lang('lite/global.basics')</h2>
                                <div class="row">
                                    {!! form_until($form,'target_groups') !!}
                                </div>
                            </div>
                            <div class="panel__nav">
                                <div id="nav-anchor"></div>
                                <nav>
                                    <ul>
                                        <li class="nav--completed"><a href="#basics">@lang('lite/global.basics')</a></li>
                                        <li><a href="#location">@lang('lite/global.location')</a></li>
                                        <li><a href="#involved-organisations">@lang('lite/global.involved_organisations')</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="form__block" id="location">
                            <div class="col-md-9">
                                <h2>@lang('lite/global.location')</h2>
                                <div class="row">
                                    {!! form_row($form->country) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form__block" id="involved-organisations">
                            <div class="col-md-9">
                                <h2>@lang('lite/global.involved_organisations')</h2>
                                <div class="row">
                                    {!! form_until($form,"add_more_implementing") !!}
                                </div>
                            </div>
                        </div>
                        <div class="form__block">
                            <div class="col-md-9">
                                {!! form_rest($form) !!}
                                <a href="#" class="pull-right btn-go-back">Cancel and go back</a>
                            </div>
                        </div>
                        {!! form_end($form) !!}
                    </div>
                    <div class="funding_organisations-container hidden"
                         data-prototype="{{ form_row($form->funding_organisations->prototype()) }}">
                    </div>
                    <div class="implementing_organisations-container hidden"
                         data-prototype="{{ form_row($form->implementing_organisations->prototype()) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script type="text/javascript" src="{{url('/js/jquery.scrollto.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.add-to-collection').on('click', function (e) {
                e.preventDefault();
                var source = $(this).attr('data-collection');
                var collection = $('.' + source + '-container');
                var parentContainer = $('.' + source);
                var count = $('.' + source + '> div.form-group').length;
                var proto = collection.data('prototype').replace(/__NAME__/g, count);
                $(parentContainer).append(proto);
            });

            /**
             * This part does the "fixed navigation after scroll" functionality
             * We use the jQuery function scroll() to recalculate our variables as the
             * page is scrolled/
             */
            $(window).scroll(function () {
                var window_top = $(window).scrollTop() + 20; // the "12" should equal the margin-top value for nav.stick
                var div_top = $('#nav-anchor').offset().top;
                if (window_top > div_top) {
                    $('.panel__nav nav').addClass('stick');
                } else {
                    $('.panel__nav nav').removeClass('stick');
                }
            });

            /**
             * This part causes smooth scrolling using scrollto.js
             * We target all a tags inside the nav, and apply the scrollto.js to it.
             */
            $(".panel__nav nav a").click(function (evn) {
                evn.preventDefault();
                $('html,body').scrollTo(this.hash, this.hash);
            });

            /**
             * This part handles the highlighting functionality.
             * We use the scroll functionality again, some array creation and
             * manipulation, class adding and class removing, and conditional testing
             */
            var aChildren = $(".panel__nav nav li").children(); // find the a children of the list items
            var aArray = []; // create the empty aArray
            for (var i = 0; i < aChildren.length; i++) {
                var aChild = aChildren[i];
                var ahref = $(aChild).attr('href');
                aArray.push(ahref);
            } // this for loop fills the aArray with attribute href values

            $(window).scroll(function () {
                var windowPos = $(window).scrollTop(); // get the offset of the window from the top of page
                var windowHeight = $(window).height(); // get the height of the window
                var docHeight = $(document).height();

                for (var i = 0; i < aArray.length; i++) {
                    var theID = aArray[i];
                    var divPos = $(theID).offset().top - 54; // get the offset of the div from the top of page
                    var divHeight = $(theID).height(); // get the height of the div in question
                    if (windowPos >= divPos && windowPos < (divPos + divHeight)) {
                        $("a[href='" + theID + "']").addClass("active");
                    } else {
                        $("a[href='" + theID + "']").removeClass("active");
                    }
                }

                if (windowPos + windowHeight == docHeight) {
                    if (!$(".panel__nav nav li:last-child a").hasClass("active")) {
                        var navActiveCurrent = $(".active").attr("href");
                        $("a[href='" + navActiveCurrent + "']").removeClass("active");
                        $(".panel__nav nav li:last-child a").addClass("active");
                    }
                }
            });
        });
    </script>
@stop

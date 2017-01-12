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
            <div class="panel-body">
                <div class="create-form create-project-form edit-form">
                    {!! form_start($form) !!}
                    <div class="form__block">
                        <h2>@lang('lite/global.basics')</h2>
                        <div class="row">
                            {!! form_until($form,'target_groups') !!}
                        </div>
                    </div>
                    <div class="form__block">
                        <h2>@lang('lite/global.location')</h2>
                        <div class="row">
                            {!! form_row($form->country) !!}
                        </div>
                    </div>
                    <div class="form__block">
                        <h2>@lang('lite/global.involved_organisations')</h2>
                        <div class="row">
                            {!! form_until($form,"add_more_implementing") !!}
                        </div>
                    </div>
                    <div class="form__block">
                        {!! form_rest($form) !!}
                        <a href="#" class="pull-right btn-go-back">Cancel and go back</a>
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
@stop

@section('script')
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
        });
    </script>
@stop

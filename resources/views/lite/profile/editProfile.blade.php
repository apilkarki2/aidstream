@extends('lite.base.sidebar')

@section('title', trans('lite/title.edit_profile'))

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel__heading">
                <h1>@lang('lite/title.edit_profile')</h1>
            </div>
            <div class="panel-body">
                {!! form_start($form) !!}
                <div>
                    Personal Information
                </div>
                {!! form_until($form, 'picture') !!}
                <div>
                    Organisation Information
                </div>
                {!! form_rest($form) !!}
                {!! form_end($form) !!}

        </div>
            <div class="panel__body">
                <div class="create-form user-form">
                    <div class="row">
                        {!! form_start($form) !!}
                        <div>
                            Personal Information
                        </div>
                        {!! form_until($form, 'timeZone') !!}
                        <div class="form-group col-sm-6 upload-logo-block edit-profile-block edit-profile-form-block">
                            {{--<label class="control-label">Profile Picture</label>--}}
                            <div class="upload-logo">
                                {!! form_row($form->picture) !!}
                                <label for="file-logo">
                                    <div class="uploaded-logo has-image">
                                        <img src="" height="150" width="150" alt="Uploaded Image" id="selected_picture">
                                        <div class="change-logo-wrap">
                                            <span class="change-logo">Change Picture</span>
                                        </div>
                                    </div>
                                </label>
                                <span class="upload-label">Upload your Profile Picture</span>
                            </div>
                        </div>
                        <div class="description">
                            <span>@lang('global.image_criteria')</span>
                        </div>
                        <div>
                            Organisation Information
                        </div>
                        {!! form_rest($form) !!}
                        {!! form_end($form) !!}
                    </div>
                </div>
            </div>
    </div>
@stop

@section('script')

@stop

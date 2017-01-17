@extends('lite.base.sidebar')

@section('title', trans('lite/title.settings'))

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>
                    @lang('lite/title.settings')
                    @if ($loggedInUser->isAdmin() && session('version') == 'V202')
                        <button class="btn btn-sm btn-xs pull-right" data-toggle="modal" data-target="#system-upgrade-modal">@lang('lite/settings.version_upgrade')</button>
                    @endif
                </div>
            </div>
            <div class="panel-body">
                {!! form($form) !!}
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="system-upgrade-modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">@lang('lite/settings.confirm_upgrade')</h4>
                </div>
                <form action="{{ route('lite.settings.upgrade-version') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>
                            @lang('lite/settings.upgrade_changes')
                        </p>
                    </div>
                    <div class="modal-footer">
                        <label>
                            <input type="checkbox" id="agree-upgrade">@lang('lite/settings.agree_upgrade')
                        </label>
                        <button type="submit" disabled id="submit-upgrade" class="btn btn-primary">@lang('lite/settings.version_upgrade')</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lite/global.cancel')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        var agencies = {!! $agencies !!};
        var selectedRegistrationAgency = "{!! $registrationAgency !!}";
        var country = "{!! $country !!}";
    </script>
    <script src="{{ asset('lite/js/settings.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#agree-upgrade').change(function () {
                if (this.checked) {
                    $('#submit-upgrade').attr('disabled', false);
                } else {
                    $('#submit-upgrade').attr('disabled', true);
                }
            });
        });
    </script>
@stop

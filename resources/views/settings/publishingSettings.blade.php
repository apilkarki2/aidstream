@extends('settings.settings')
@section('panel-body')
    <div class="panel-body">
        <div class="create-form settings-form">
            {!! form_start($form) !!}
            <div id="publishing_info1">
                <div class="publishing-wrap">
                    <h2>@lang('setting.registry_information')</h2>
                    <div class="form-group">
                        {!! form_label($form->publisher_id, ['label' => trans('setting.publisher_id')]) !!}
                        {!! form_widget($form->publisher_id) !!}
                        <div id="publisher_id_status_display"
                             class="{{ (($status = getVal($form->getModel(), ['publisher_id_status'])) == 'Correct') ? 'text-success' : 'text-danger' }}">{{ $status }}</div>
                    </div>
                    {!! form_until($form,'publisher_id_status') !!}
                    <div class="form-group">
                        {!! form_label($form->api_id, ['label' => trans('setting.api_key')]) !!}
                        {!! form_widget($form->api_id) !!}
                        <div id="api_id_status_display"
                             class="{{ (($status = getVal($form->getModel(), ['api_id_status'])) == 'Correct') ? 'text-success' : 'text-danger' }}">{{ $status }}</div>
                    </div>
                    {!! form_until($form,'verify') !!}
                </div>
            </div>
            <div id="publishing_info2">
                <div class="publishing-wrap">
                    <div class="col-md-12">
                        {!!  form_until($form,'publishing') !!}
                    </div>
                </div>
            </div>
            <div id="publishing_info3">
                <div class="publishing-wrap">
                    <div class="col-md-12">
                        {!! form_until($form,'publish_files') !!}
                    </div>
                </div>
            </div>
            {!!  form_end($form) !!}
        </div>
    </div>
@endsection
@section('foot')
    <script src="{{url('js/chunk.js')}}"></script>
    <script>
        $(window).load(function () {
            Chunk.verifyPublisherAndApi();
                    @if(session('first_login') && (auth()->user()->isAdmin()))
            var stepNumber = location.hash.replace('#', '');
            if (stepNumber == 1 || stepNumber == 2 || stepNumber == 3) {
                var completedSteps = [{!! json_encode((array)$completedSteps) !!}];
                UserOnBoarding.getLocalisedSettingsText(completedSteps);
                UserOnBoarding.validatePublishingInfo();
            }
            @endif
        });
    </script>
@endsection

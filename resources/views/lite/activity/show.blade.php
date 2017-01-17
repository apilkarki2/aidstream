@extends('lite.base.sidebar')

@section('title', @trans('lite/title.activities'))

@inject('getCode', 'App\Helpers\GetCodeName')

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
        @include('includes.response')
        <div class="element-panel-heading">
            <div class="col-md-9">

                <div class="element-panel-heading-info">
                    <div>
                        {{ $activity->title ? $activity->title[0]['narrative'] : trans('lite/global.no_title') }}
                    </div>
                    <span>{{ $activity->identifier['activity_identifier'] }}</span>
                    <span class="last-updated-date">@lang('lite/global.last_updated_on'): {{ changeTimeZone($activity['updated_at'], 'M d, Y H:i') }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="clearfix">
                    <a href="{{ route('lite.activity.edit', $activity->id) }}" class="edit-btn">@lang('lite/global.edit')</a>
                </div>
                {{--<a href="{{ route('change-project-defaults', $activity->id) }}" class="override-section">--}}
                {{--<span class="glyphicon glyphicon-triangle-left"></span> Override Default Values--}}
                {{--</a>--}}
            </div>

        </div>
        <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper fullwidth-wrapper">
            <div class="activity-status activity-status-{{ $statusLabel[$activityWorkflow] }}">
                <ol>
                    @foreach($statusLabel as $key => $value)
                        @if($key == $activityWorkflow)
                            <li class="active"><span>{{ trans(sprintf('lite/global.%s',strtolower($value)))}}</span></li>
                        @else
                            <li><span>{{ trans(sprintf('lite/global.%s',strtolower($value)))}}</span></li>
                        @endif
                    @endforeach
                </ol>
                @include('lite.activity.partials.workflow')
            </div>
        </div>

        <div class="panel panel-default panel-element-detail element-show">
            <div class="activity-element-wrapper">
                <div class="activity-element-list">
                    <div class="activity-element-label">
                        @lang('lite/elementForm.activity_identifier')
                    </div>
                    <div class="activity-element-info">
                        {{getVal($activity->identifier,['iati_identifier_text'])}}
                    </div>
                </div>
            </div>

            <div class="activity-element-wrapper">
                <div class="activity-element-list">
                    <div class="activity-element-label">
                        @lang('lite/elementForm.activity_title')
                    </div>
                    <div class="activity-element-info">
                        {{$activity->title[0]['narrative']}}
                    </div>
                </div>
            </div>

            <div class="activity-element-wrapper">
                @foreach ($activity->description as $description)
                    @if(getVal($description, ['type']) == 1)
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                @lang('lite/elementForm.general_description')
                            </div>
                            <div class="activity-element-info">
                                {{$description['narrative'][0]['narrative']}}
                            </div>
                        </div>
                    @endif

                    @if(getVal($description, ['type']) == 2)
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                @lang('lite/elementForm.objectives')
                            </div>
                            <div class="activity-element-info">
                                {{$description['narrative'][0]['narrative']}}
                            </div>
                        </div>
                    @endif

                    @if(getVal($description, ['type']) == 3)
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                @lang('lite/elementForm.target_groups')
                            </div>
                            <div class="activity-element-info">
                                {{$description['narrative'][0]['narrative']}}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="activity-element-wrapper">
                <div class="activity-element-list">
                    <div class="activity-element-label">
                        @lang('lite/elementForm.activity_status')
                    </div>
                    <div class="activity-element-info">
                        {{ $getCode->getCodeNameOnly('ActivityStatus', $activity->activity_status) }}
                    </div>
                </div>
            </div>

            <div class="activity-element-wrapper">
                <div class="activity-element-list">
                    <div class="activity-element-label">
                        @lang('lite/elementForm.sector')
                    </div>
                    <div class="activity-element-info">
                        {{ $getCode->getCodeNameOnly('Sector', getVal((array)$activity->sector, [0, 'sector_code']),-7)}}
                    </div>
                </div>
            </div>

            @foreach ((array)$activity->activity_date as $date)
                @if(getVal($date, ['type']) == 2)
                    <div class="activity-element-wrapper">
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                @lang('lite/elementForm.start_date')
                            </div>
                            <div class="activity-element-info">
                                {{ formatDate($date['date']) }}
                            </div>
                        </div>
                    </div>
                @endif

                @if(getVal($date, ['type']) == 4)
                    <div class="activity-element-wrapper">
                        <div class="activity-element-list">
                            <div class="activity-element-label">
                                @lang('lite/elementForm.end_date')
                            </div>
                            <div class="activity-element-info">
                                {{ formatDate($date['date']) }}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="activity-element-wrapper">
                <div class="activity-element-list">
                    <div class="activity-element-label">
                        @lang('lite/elementForm.recipient_country')
                    </div>
                    <div class="activity-element-info">
                        @foreach((array)$activity->recipient_country as $index=> $country)
                            {{$getCode->getCodeNameOnly('Country', $country['country_code'],-4,'Organization')}}
                        @endforeach
                    </div>
                </div>
            </div>

            {{--<div class="activity-element-wrapper">--}}
            {{--<div class="activity-element-list">--}}
            {{--<div class="activity-element-label">--}}
            {{--Location--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--@foreach ($activity->location as $location)--}}
            {{--@if (getVal($location, ['administrative', 0, 'code']))--}}
            {{--<li>--}}
            {{--{{ getVal($location, ['administrative', 0, 'code']) }}, {{ getVal($location, ['administrative', 1, 'code']) }}--}}
            {{--</li>--}}
            {{--@endif--}}
            {{--@endforeach--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}

            @if ($activity->participating_organization)
                <div class="activity-element-wrapper">
                    @foreach ($activity->participating_organization as $participatingOrganization)
                        @if(getVal($participatingOrganization, ['narrative', 0, 'narrative']) && getVal($participatingOrganization, ['organization_role']) == "1")
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    @lang('lite/elementForm.funding_organisation')
                                </div>
                                <div class="activity-element-info">
                                    <li>
                                        {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                        , {{$getCode->getCodeNameOnly('OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                                    </li>
                                </div>
                            </div>
                        @endif

                        @if(getVal($participatingOrganization, ['narrative', 0, 'narrative']) && getVal($participatingOrganization, ['organization_role']) == 4)
                            <div class="activity-element-list">
                                <div class="activity-element-label">
                                    @lang('lite/elementForm.implementing_organisation')
                                </div>
                                <div class="activity-element-info">
                                    <li>
                                        {{ getVal($participatingOrganization, ['narrative', 0, 'narrative']) }}
                                        , {{$getCode->getCodeNameOnly('OrganisationType', getVal($participatingOrganization, ['organization_type']))}}
                                    </li>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
            @if(array_key_exists('outcomes_document',$documentLinks))
                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            @lang('lite/elementForm.results_outcomes_documents')
                        </div>
                        <div class="activity-element-info">
                            @foreach((array)getVal($documentLinks,['outcomes_document'],[]) as $index => $value)
                                <li>
                                    @if(($url = getVal($value,['document_url'])) != "")
                                        <a href="{{$url}}">{{getVal($value,['document_title'])}}</a>
                                    @else
                                        {{getVal($value,['document_title'])}}
                                    @endif
                                </li>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if(array_key_exists('annual_report',$documentLinks))
                <div class="activity-element-wrapper">
                    <div class="activity-element-list">
                        <div class="activity-element-label">
                            @lang('lite/elementForm.annual_reports')
                        </div>
                        <div class="activity-element-info">
                            @foreach((array)getVal($documentLinks,['annual_report'],[]) as $index => $value)
                                <li>
                                    @if(($url = getVal($value,['document_url'])) != "")
                                        <a href="{{$url}}">{{getVal($value,['document_title'])}}</a>
                                    @else
                                        {{getVal($value,['document_title'])}}
                                    @endif
                                </li>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            {{--TODO REMOVE THIS--}}
            {{--@if ($activity->resultDocuments())--}}
            {{--<div class="activity-element-wrapper">--}}
            {{--<div class="title">--}}

            {{--</div>--}}
            {{--<div class="activity-element-list">--}}
            {{--<div class="activity-element-label">--}}
            {{--Results/Outcomes Documents--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--<a href="{{ getVal($activity->resultDocuments(), ['document_link', 'url']) }}">{{ getVal($activity->resultDocuments(), ['document_link', 'title', 0, 'narrative', 0, 'narrative']) }}</a>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}

            {{--@if ($activity->annualReports())--}}
            {{--<div class="activity-element-wrapper">--}}
            {{--<div class="activity-element-list">--}}
            {{--<div class="activity-element-label">--}}
            {{--Annual Reports--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--<a href="{{ getVal($activity->annualReports(), ['document_link', 'url']) }}">{{ getVal($activity->annualReports(), ['document_link', 'title', 0, 'narrative', 0, 'narrative']) }}</a>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endif--}}

            @include('lite.activity.partials.budget')
            {{--TODO REMOVE THIS--}}
            {{-- -------------  start of transactions --------------- --}}
            {{--<div class="transactions-wrap">--}}
            {{--<div class="title">Transactions</div>--}}

            {{--<div class="activity-element-wrapper">--}}
            {{--@if(count($disbursement) > 0)--}}
            {{--<div class="activity-element-label">--}}
            {{--<span>Disbursement--}}
            {{--<a href="{{url(sprintf('project/%s/transaction/%s/edit', $activity->id, 3))}}"--}}
            {{--class="edit">--}}
            {{--<span>Edit Disbursement</span>--}}
            {{--</a>--}}
            {{--</span>--}}
            {{--</div>--}}

            {{--@foreach($disbursement as $data)--}}
            {{--<div class="activity-element-info">--}}
            {{--<li>--}}
            {{--<span>--}}
            {{--{{ number_format($data['value'][0]['amount']) }} {{ $data['value'][0]['currency'] }}, {{ formatDate(getVal($data, ['transaction_date', 0, 'date'])) }}--}}
            {{--<span class="has-delete-wrap">--}}
            {{--<a href="javascript:void(0)" class="delete-transaction delete" data-route="{{ route('single.transaction.destroy', [$data['id']]) }}">Delete</a>--}}
            {{--{!! Form::open(['method' => 'POST', 'route' => ['single.transaction.destroy', $data['id']],'class' => 'hidden', 'role' => 'form', 'id' => 'transaction-delete-form']) !!}--}}
            {{--{!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}--}}
            {{--{!! Form::close() !!}--}}
            {{--</span>--}}
            {{--</span>--}}
            {{--</li>--}}

            {{--<div class="toggle-btn">--}}
            {{--<span class="show-more-info">Show more info</span>--}}
            {{--<span class="hide-more-info hidden">Hide more info</span>--}}
            {{--</div>--}}
            {{--<div class="more-info hidden">--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Internal Ref:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['reference']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Transaction Value:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{ number_format($data['value'][0]['amount']) }} {{ getVal($data, ['value', 0, 'currency']) }}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Transaction Date:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['transaction_date'][0]['date']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Description--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['description'][0]['narrative'][0]['narrative']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Receiver Organization--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['provider_organization'][0]['narrative'][0]['narrative']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endforeach--}}
            {{--<div class="activity-element-list">--}}
            {{--<a href="{{ url(sprintf('project/%s/transaction/%s/create', $activity->id,3)) }}" class="add-more"><span>Add Another Disbursement</span></a>--}}
            {{--</div>--}}
            {{--@else--}}
            {{--<div class="activity-element-list">--}}
            {{--<div class="title">Disbursements</div>--}}
            {{--<a href="{{ url(sprintf('project/%s/transaction/%s/create', $activity->id,3)) }}" class="add-more"><span>Add Disbursement</span></a>--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--</div>--}}
            {{--<div class="activity-element-wrapper">--}}
            {{--@if(count($expenditure) > 0)--}}
            {{--<div class="activity-element-label">--}}
            {{--<span> Expenditure--}}
            {{--<a href="{{url(sprintf('project/%s/transaction/%s/edit', $activity->id, 4))}}" class="edit"> Edit Expenditure</a>--}}
            {{--</span>--}}
            {{--</div>--}}

            {{--@foreach($expenditure as $data)--}}
            {{--<div class="activity-element-info">--}}
            {{--<li>--}}
            {{--<span>{{ number_format($data['value'][0]['amount']) }} {{ $data['value'][0]['currency'] }}, {{ formatDate(getVal($data, ['transaction_date', 0, 'date'])) }}--}}
            {{--<span class="has-delete-wrap">--}}
            {{--<a href="javascript:void(0)" class="delete-transaction delete" data-route="{{ route('single.transaction.destroy', [$data['id']]) }}">Delete</a>--}}
            {{--{!! Form::open(['method' => 'POST', 'route' => ['single.transaction.destroy', $data['id']],'class' => 'hidden', 'role' => 'form', 'id' => 'transaction-delete-form']) !!}--}}
            {{--{!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}--}}
            {{--{!! Form::close() !!}--}}
            {{--</span>--}}
            {{--</span>--}}

            {{--</li>--}}
            {{--<div class="toggle-btn">--}}
            {{--<span class="show-more-info">Show more info</span>--}}
            {{--<span class="hide-more-info hidden">Hide more info</span>--}}
            {{--</div>--}}
            {{--<div class="more-info hidden">--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Internal Ref:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['reference']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Transaction Value:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{ number_format($data['value'][0]['amount']) }} {{ getVal($data, ['value', 0, 'currency']) }}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Transaction Date:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['transaction_date'][0]['date']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Description--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['description'][0]['narrative'][0]['narrative']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Receiver Organization--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['provider_organization'][0]['narrative'][0]['narrative']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endforeach--}}
            {{--<div class="activity-element-list">--}}
            {{--<a href="{{ url(sprintf('project/%s/transaction/%s/create', $activity->id,4)) }}" class="add-more"><span>Add Another Expenditure</span></a>--}}
            {{--</div>--}}
            {{--@else--}}
            {{--<div class="activity-element-list">--}}
            {{--<div class="title">Expenditure</div>--}}
            {{--<a href="{{ url(sprintf('project/%s/transaction/%s/create', $activity->id,4)) }}" class="add-more"><span>Add Expenditure</span></a>--}}
            {{--</div>--}}
            {{--@endif--}}
            {{--</div>--}}
            {{--<div class="activity-element-wrapper">--}}
            {{--@if(count($incomingFund) > 0)--}}
            {{--<div class="activity-element-label">--}}
            {{--<span>Incoming Funds--}}
            {{--<a href="{{url(sprintf('project/%s/transaction/%s/edit', $activity->id, 1))}}"--}}
            {{--class="edit"><span>Edit Incoming Funds</span></a>--}}
            {{--</span>--}}
            {{--</div>--}}

            {{--@foreach($incomingFund as $data)--}}
            {{--<div class="activity-element-info">--}}
            {{--<li>--}}
            {{--<span>{{ number_format($data['value'][0]['amount']) }} {{ $data['value'][0]['currency'] }}, {{ formatDate(getVal($data, ['transaction_date', 0, 'date'])) }}--}}
            {{--<span class="has-delete-wrap">--}}
            {{--<a href="javascript:void(0)" class="delete-transaction delete" data-route="{{ route('single.transaction.destroy', [$data['id']]) }}">Delete</a>--}}
            {{--{!! Form::open(['method' => 'POST', 'route' => ['single.transaction.destroy', $data['id']],'class' => 'hidden', 'role' => 'form', 'id' => 'transaction-delete-form']) !!}--}}
            {{--{!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}--}}
            {{--{!! Form::close() !!}--}}
            {{--</span>--}}

            {{--</span>--}}

            {{--</li>--}}
            {{--<div class="toggle-btn">--}}
            {{--<span class="show-more-info">Show more info</span>--}}
            {{--<span class="hide-more-info hidden">Hide more info</span>--}}
            {{--</div>--}}
            {{--<div class="more-info hidden">--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Internal Ref:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['reference']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Transaction Value:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{ number_format($data['value'][0]['amount']) }} {{ getVal($data, ['value', 0, 'currency']) }}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Transaction Date:--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['transaction_date'][0]['date']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Description--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['description'][0]['narrative'][0]['narrative']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="element-info">--}}
            {{--<div class="activity-element-label">--}}
            {{--Provider Organization--}}
            {{--</div>--}}
            {{--<div class="activity-element-info">--}}
            {{--{{$data['provider_organization'][0]['narrative'][0]['narrative']}}--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--@endforeach--}}
            {{--<div class="activity-element-list">--}}
            {{--<a href="{{ url(sprintf('project/%s/transaction/%s/create', $activity->id,1)) }}" class="add-more"><span>Add Another Incoming Funds</span></a>--}}
            {{--</div>--}}
            {{--@else--}}
            {{--<div class="activity-element-list">--}}
            {{--<div class="title">Incoming Funds</div>--}}
            {{--<a href="{{ url(sprintf('project/%s/transaction/%s/create', $activity->id,1)) }}" class="add-more"><span>Add Incoming Funds</span></a>--}}
            {{--</div>--}}
            {{--@endif--}}

            {{--</div>--}}
            {{--</div>--}}
            {{-- -------------  end of transactions --------------- --}}

            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="hidden">--}}
            {{--<div class="modal" id="transactionDeleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index: 9999">--}}
            {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
            {{--<div class="modal-header">--}}
            {{--<h4 class="modal-title" id="myModalLabel">--}}
            {{--Confirm Delete?--}}
            {{--</h4>--}}
            {{--</div>--}}
            {{--<div class="modal-body">--}}
            {{--Are you sure you want to delete this transaction?--}}
            {{--</div>--}}
            {{--<div class="modal-footer">--}}
            {{--<button class="btn btn_del" type="button" id="yes-delete">Yes</button>--}}
            {{--<button class="btn btn-default" type="button" data-dismiss="modal">No</button>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>
@endsection
@section('script')
    {{--<script>--}}
    {{--var currentTransactionCount;--}}

    {{--@if(old('transaction'))--}}
    {{--currentTransactionCount = "{{ count(old('transaction')) - 1 }}";--}}
    {{--@elseif (isset($transactions))--}}
    {{--currentTransactionCount = "{{ count($transactions) - 1 }}";--}}
    {{--@else--}}
    {{--currentTransactionCount = 0;--}}
    {{--@endif--}}
    {{--</script>--}}
    {{--<script src="{{ asset('/js/tz/transaction.js') }}"></script>--}}
    {{--<script src="{{ asset('/js/tz/transactionDelete.js') }}"></script>--}}
@endsection

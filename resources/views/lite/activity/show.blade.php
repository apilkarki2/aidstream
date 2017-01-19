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

            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="delete-modal">
                <div class="modal-dialog modal-lg" role="document">     
                    <div class="modal-content">         
                        <div class="modal-header">             
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">@lang('lite/settings.confirm_upgrade')</h4>
                        </div>
                        <form action="" method="POST" id="delete-form">
                            {{ csrf_field() }}
                            <input id="index" type="hidden" value="" name="index">
                            <div class="modal-body">
                                <p>@lang('lite/global.confirm_delete')</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" id="submit-delete-transaction" class="btn btn-primary">@lang('lite/settings.yes')</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lite/settings.no')</button>
                                    
                            </div>
                        </form>
                           
                    </div>
                </div>
            </div>
            @include('lite.activity.partials.budget')
            @include('lite.activity.partials.transaction')
        </div>
    </div>
@endsection

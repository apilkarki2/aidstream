@extends('lite.base.sidebar')

@section('title', trans('lite/title.profile'))

@inject('getCode', 'App\Helpers\GetCodeName')
@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>@lang('lite/title.profile')</div>
            </div>
            <div class="panel-body">
                <img src="{{ $loggedInUser->profile_url ? $loggedInUser->profile_url : url('images/avatar-full.png')}}" width="200" height="200" alt="{{$loggedInUser->name}}">
                <div class="profile-info">
                    <span class="profile-username">{{$loggedInUser->username}}</span>
                    <span class="profile-user-email"><a href="mailto:{{$loggedInUser->email}}">{{$loggedInUser->email}}</a></span>
                    <div><a href="{{route('lite.user.profile.edit')}}">
                            @lang('lite/profile.edit_profile')
                        </a> |
                        <a href="{{route('lite.user.password.edit')}}">
                            @lang('lite/profile.change_password')
                        </a>
                    </div>
                </div>
                <div class="organization-logo"><img
                            src="{{$organisation->logo ? url($organisation->logo_url) : url('images/no-logo.png')}}">
                </div>
                <div class="organization-detail">
                    <div class="organization-name">{{$organisation->name}}</div>
                    <ul>
                        @if($organisation->telephone)
                            <li class="telephone col-xs-6 col-md-4 col-lg-4">
                                <label>@lang('lite/profile.telephone')</label><span>{{$organisation->telephone}}</span></li>
                        @endif
                        @if($organisation->twitter)
                            <li class="twitter col-xs-6 col-md-4 col-lg-4"><label>@lang('lite/profile.twitter')</label><a
                                        href="http://www.twitter.com/{{ $organisation->twitter }}">{{$organisation->twitter}}</a></li>
                        @endif
                        @if($organisation->organization_url)
                            <li class="website col-xs-6 col-md-4 col-lg-4"><label>@lang('lite/profile.website')</label><a
                                        href="{{$organisation->organization_url}}" target="_blank">{{$organisation->organization_url}}</a></li>
                        @endif
                        @if($organisation->address)
                            <li class="address col-xs-6 col-md-4 col-lg-4">
                                <label>@lang('lite/profile.address')</label><span>{{$organisation->address}}</span></li>
                        @endif
                        @if($organisation->country)
                            <li class="country col-xs-6 col-md-4 col-lg-4">
                                <label>@lang('lite/profile.country')</label><span>{{$getCode->getOrganizationCodeName('Country', $organisation->country)}}</span>
                            </li>
                        @endif
                        @if($organisation->reporting_org)
                            <li class="country col-xs-6 col-md-4 col-lg-4">
                                <label>@lang('lite/settings.organisation_type')</label><span>{{$getCode->getOrganizationCodeName('OrganizationType', getVal($organisation->reporting_org, [0, 'reporting_organization_type'], null), false)}}</span>
                            </li>
                        @endif
                        @if($organisation->registration_agency)
                            <li class="country col-xs-6 col-md-4 col-lg-4">
                                <label>@lang('lite/settings.organisation_registration_agency')</label><span>{{$organisation->registration_agency}}</span>
                            </li>
                        @endif
                        @if($organisation->registration_number)
                            <li class="country col-xs-6 col-md-4 col-lg-4">
                                <label>@lang('lite/settings.organisation_registration_number')</label><span>{{$organisation->registration_number}}</span>
                            </li>
                        @endif
                    </ul>
                    <div class="disqus-wrapper"><span>Disqus Comments : </span>{{($organisation->disqus_comments == 1) ? 'Enabled' : 'Disabled'}}</div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')

@stop

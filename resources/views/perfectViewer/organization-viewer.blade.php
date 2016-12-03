<!DOCTYPE html>
<html>

@inject('codeListHelper', 'App\Helpers\GetCodeName')

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Organization Viewer</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <link rel="shortcut icon" type="image/png" sizes="16*16" href="{{asset('/images/favicon.png')}}"/>
    <link rel="stylesheet" href="{{asset('/css/bootstrap.min.css')}}">
    <link href="{{ asset('/css/jquery.jscrollpane.css') }}" rel="stylesheet">
</head>

<style type="text/css">
    .bar {
        fill: #B2BEC4;
        font-size: 14px;
        cursor: pointer;
    }

    rect.bar:hover {
        fill: #00A8FF !important;
    }

    .axis {
        font: 14px sans-serif;
        fill: #979797;
    }

    .axis path,
    .axis line {
        fill: none;
        shape-rendering: crispEdges;
    }
</style>

<body>
@include('includes.header')
<div class="wrapper">
    <section class="col-md-12 org-map-wrapper">
        <div class="width-940">
            <div class="organisation-info"> 
                <a href="#" class="organisation-logo">
                    <!--dynamic organisation name-->
                    @if($organizations['logo'])
                        <div class="logo">
                            <img src="{{ $organizations['logo_url'] }}" alt="AbleChildAfrica" width="238" height="68">
                        </div>
                    @endif
                </a>
                <span class="organisation-name">
        <a href="#" title="AbleChildAfrica">
            <!--dynamic organisation name-->
            {{$organizations['name']}}
        </a>
    </span>
                <address><i class="pull-left material-icons">room</i>Unit 3 Graphite Square, London, SE11 5EE</address>
            </div>
        </div>
    </section>

    <section class="col-md-12 org-main-wrapper">
        <div class="width-940">
            <div class="col-xs-12 col-md-8 org-activity-wrapper">
                <h2>Activities <span class="activity-count">{{count($activities)}}</span></h2>
                <ul class="activities-listing">
                @foreach($activities as $index => $activity)
                    <!--dynamic activity list-->
                        <li>
                            <a href="{{url('/who-is-using/'.$organizations['org_slug'].'/'.$activity['activity_id'])}}">
                                <div class="col-md-9 pull-left activity-info-wrapper">
                                    <h3 class="activity-name">
                                        <!--dynamic activity name-->
                                        {{getVal($activity, ['published_data', 'title', 0, 'narrative'])}}
                                    </h3>
                                    <div class="activity-publish-state">
                                        @if($activity['activity_in_registry'])
                                            <span class="pull-left published-in-iati">
                                    <!--dynamic state-->
                                        Registered in IATI
                                    </span>
                                        @else
                                            <span class="pull-left unpublished-in-iati">
                                    <!--dynamic state-->
                                        Not Published in IATI
                                    </span>
                                        @endif
                                        <img src="{{asset('images/iati-logo.png')}}" alt="IATI" width="20" height="19">
                                    </div>
                                    <div class="iati-identifier-wrapper">IATI Identifier:
                                        <span class="iati-identifier">
                                        <!--dynamic iati identifier-->
                                            {{getVal($activity, ['published_data', 'identifier', 'activity_identifier'], '')}}
                                    </span>
                                    </div>
                                    <dl>
                                        <dt class="pull-left"><i class="material-icons">date_range</i></dt>
                                        <dd class="pull-left">
                                            @if($activity['published_data']['activity_date'][0]['type'] == 2)
                                                {{getVal($activity, ['published_data', 'activity_date', 0, 'date'], '')}}
                                            @elseif($activity['published_data']['activity_date'][0]['type'] == 1)
                                                {{getVal($activity, ['published_data', 'activity_date', 0, 'date'], '')}}
                                            @else
                                            @endif
                                            -
                                            @if($activity['published_data']['activity_date'][0]['type'] == 4)
                                                {{getVal($activity, ['published_data', 'activity_date', 0, 'date'], '')}}
                                            @elseif($activity['published_data']['activity_date'][0]['type'] == 3)
                                                {{getVal($activity, ['published_data', 'activity_date', 0, 'date'], '')}}
                                            @else
                                            @endif
                                        </dd>
                                        <dt class="pull-left"><i class="material-icons">autorenew</i></dt>
                                        <dd class="pull-left">
                                            {{ $codeListHelper->getCodeNameOnly('ActivityStatus', getVal($activity, ['published_data', 'activity_status'], '')) }} <i>(Status)</i>
                                        </dd>
                                    </dl>
                                </div>
                                <div class="col-md-3 pull-right total-budget-wrapper">
                                    <span>Total Budget</span>
                                    <span class="total-budget-amount">{{getVal($activity, ['published_data', 'totalBudget', 'value'], 0)}}</span>
                                    <span class="currency">{{getVal($activity, ['published_data', 'totalBudget', 'currency'], '')}}</span>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-xs-12 col-md-4">
                <div class="org-transaction-wrapper">
                    <ul>
                        <li>
                            <h4>Total Commitments</h4>
                            <span>
                                <!--dynamic budget-->
                                {{getVal($organizations, ['transaction_totals', 'total_commitments'], 0)}}
                            </span>
                        </li>
                        <li>
                            <h4>Total Disbursements</h4>
                            <span>
                                <!--dynamic budget-->
                                {{getVal($organizations, ['transaction_totals', 'total_disbursements'], 0)}}
                            </span>
                        </li>
                        <li>
                            <h4>Total Expenditures</h4>
                            <span>
                                <!--dynamic budget-->
                                {{getVal($organizations, ['transaction_totals', 'total_expenditures'], 0)}}
                            </span>
                        </li>
                        <li>
                            <h4>Total Incoming Funds</h4>
                            <span>
                                <!--dynamic budget-->
                                {{getVal($organizations, ['transaction_totals', 'total_incoming_funds'], 0)}}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div id="map"></div>
    <footer>
        <div class="width-900">
            <div class="social-wrapper bottom-line">
                <div class="col-md-12 text-center">
                    <ul>
                        <li><a href="https://github.com/younginnovations/aidstream-new" class="github"
                               title="Fork us on Github">Fork us on Github</a></li>
                        <li><a href="https://twitter.com/aidstream" class="twitter" title="Follow us on Twitter">Follow us
                                on Twitter</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-nav bottom-line">
                <div class="col-md-12">
                    <ul>
                        <li><a href="{{ url('/about') }}">About</a></li>
                        <li><a href="{{ url('/who-is-using') }}">Who's using</a></li>
                        <!--<li><a href="#">Snapshot</a></li>-->
                    </ul>
                    <ul>
                        @if(auth()->check())
                            <li>
                                <a href="{{ url((auth()->user()->role_id == 1 || auth()->user()->role_id == 2) ? config('app.admin_dashboard') : config('app.super_admin_dashboard'))}}">Go
                                    to Dashboard</a>
                            </li>
                        @else
                            <li><a href="{{ url('/auth/login') }}">Login</a></li>
                            <li><a href="{{ url('/auth/register') }}">Register</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="footer-logo">
                <div class="col-md-12 text-center">
                    <a href="{{ url('/') }}"><img src="/images/logo-text.png" alt=""></a>
                </div>
            </div>
        </div>
        <div class="width-900 text-center">
            <div class="col-md-12 support-desc">
                For queries, suggestions, shoot us an email at <a href="mailto:support@aidstream.org">support@aidstream
                    .org</a>
            </div>
        </div>
    </footer>
</div>
</body>
<script>
    var recipientCountries = {!!json_encode(array_flip($recipientCountries))!!};
</script>
<script src="/js/d3.min.js"></script>
<script type="text/javascript" src="/js/worldMap.js"></script>

</html>
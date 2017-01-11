<div class="row">
    <div class="col-sm-4">
        <div>@lang('lite/activityDashboard.total_activities')</div>
        <div>
            {{count($activities)}}
            <div>
                @lang('lite/activityDashboard.no_of_activities_published_to_iati'): {{$noOfPublishedActivities}}
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div>@lang('lite/activityDashboard.activities_by_status')</div>
        <div class="stats"></div>
    </div>
    <div class="col-sm-4">
        <div>@lang('lite/activityDashboard.total_budget')</div>
        <div>
            <h2 id="budgetTotal">$0</h2>
            <div>@lang('lite/activityDashboard.highest_budget_in_activity'): <span id="maxBudget">$0</span></div>
        </div>
    </div>
</div>
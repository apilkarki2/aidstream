<div class="activity-element-wrapper">
    @if ($activity->budget)
        <a href="{{ route('lite.activity.budget.edit', $activity->id) }}"
           class="edit-element">
            <span>@lang('lite/elementForm.edit_budget')</span>
        </a>
        <div>
        </div>
        <div class="activity-element-list">
            <div class="activity-element-label">
                @lang('lite/title.budget')
            </div>
            <div class="activity-element-info">
                @foreach ($activity->budget as $index => $budget)
                    <li>
                        {{ getVal($budget, ['value', 0, 'amount']) }}
                        @if(getVal($budget, ['value', 0, 'currency']))
                            {{ getVal($budget, ['value', 0, 'currency']) }}
                        @else
                            {{ $defaultCurrency }}
                        @endif
                        [{{ getVal($budget, ['period_start', 0, 'date']) }}
                        - {{ getVal($budget, ['period_end', 0, 'date']) }}]

                        <a data-href="{{ route('lite.activity.budget.delete', $activity->id)}}" data-index="{{ $index }}"
                           class="delete-lite-resource" data-toggle="modal" data-target="#delete-modal" data-message="@lang('lite/global.confirm_delete')"> @lang('lite/global.delete') </a>
                    </li>
                @endforeach
            </div>
            <a href="{{ route('lite.activity.budget.create', $activity->id) }}"
               class="add-more"><span>@lang('lite/elementForm.add_budget')</span></a>
        </div>
    @else
        <div class="activity-element-list">
            <div class="title">
                @lang('lite/title.budget')
            </div>
            <a href="{{ route('lite.activity.budget.create', $activity->id) }}"
               class="add-more"><span>@lang('lite/elementForm.add_budget')</span></a>
        </div>
    @endif
</div>



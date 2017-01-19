<div class="activity-element-wrapper">
    @if ($expenditure)
        <a href="{{ route('lite.activity.transaction.edit', [$activity->id, 'Expenditure']) }}"
           class="edit-element">
            <span>Edit Expenditure</span>
        </a>
        <div>
        </div>
        <div class="activity-element-list">
            <div class="activity-element-label">
                Expenditure
            </div>
            <div class="activity-element-info">
                @foreach ($expenditure as $index => $transaction)
                    <li>
                        {{ getVal($transaction, ['transaction', 'value', 0, 'amount']) }}
                        @if(getVal($transaction, ['transaction', 'value', 0, 'currency']))
                            {{ getVal($transaction, ['transaction', 'value', 0, 'currency']) }}
                        @else
                            {{ $defaultCurrency }}
                        @endif
                        @if(getVal($transaction, ['transaction', 'value', 0, 'date']))
                            [{{ getVal($transaction, ['transaction', 'value', 0, 'date']) }}]
                        @endif
                        <a data-href="{{ route('lite.activity.transaction.delete', $activity->id)}}" data-index="{{ getVal($transaction, ['transaction', 'id']) }}"
                           class="delete-lite-transaction" data-toggle="modal" data-target="#delete-modal"> @lang('lite/global.delete') </a>
                    </li>
                @endforeach
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 'Expenditure']) }}"
               class="add-more"><span>Add Expenditure</span></a>
        </div>
    @else
        <div class="activity-element-list">
            <div class="title">
                Expenditure
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 'Expenditure']) }}"
               class="add-more"><span>Add Expenditure</span></a>
        </div>
    @endif
</div>

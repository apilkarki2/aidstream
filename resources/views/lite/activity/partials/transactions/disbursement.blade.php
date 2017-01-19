<div class="activity-element-wrapper">
    @if ($disbursement)
        <a href="{{ route('lite.activity.transaction.edit', [$activity->id, 'Disbursement']) }}"
           class="edit-element">
            <span>Edit Disbursement</span>
        </a>
        <div>
        </div>
        <div class="activity-element-list">
            <div class="activity-element-label">
                Disbursement
            </div>
            <div class="activity-element-info">
                @foreach ($disbursement as $index => $transaction)
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
                        <a data-href="{{ route('lite.activity.transaction.delete', $activity->id)}}" data-index="{{ getVal($transaction, ['id'], '') }}"
                           class="delete-lite-transaction" data-toggle="modal" data-target="#delete-modal"> @lang('lite/global.delete') </a>
                    </li>
                @endforeach
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 'Disbursement']) }}"
               class="add-more"><span>Add Disbursement</span></a>
        </div>
    @else
        <div class="activity-element-list">
            <div class="title">
                Disbursement
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 'Disbursement']) }}"
               class="add-more"><span>Add Disbursement</span></a>
        </div>
    @endif
</div>

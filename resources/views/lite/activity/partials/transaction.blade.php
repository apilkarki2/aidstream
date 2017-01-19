@include('lite.activity.partials.transactions.disbursement')
@include('lite.activity.partials.transactions.expenditure')
@include('lite.activity.partials.transactions.incoming')

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="delete-transaction-modal">
    <div class="modal-dialog modal-lg" role="document">     
        <div class="modal-content">         
            <div class="modal-header">             
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang('lite/settings.confirm_upgrade')</h4>
            </div>
            <form action="" method="POST" id="delete-transaction-form">
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
@section('script')
    <script>
        $('.delete-lite-transaction').on('click', function(){
            var form = $('#delete-transaction-form');

            form.attr('action', $(this).attr('data-href'));
            form.children('input#index').attr('value', $(this).attr('data-index'));
        });
    </script>
@stop

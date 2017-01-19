@include('lite.activity.partials.transactions.disbursement')
@include('lite.activity.partials.transactions.expenditure')
@include('lite.activity.partials.transactions.incoming')
@section('script')
    <script type="text/javascript">
        $('.delete-lite-transaction').on('click', function () {

            var form = $('#delete-form');

            form.attr('action', $(this).attr('data-href'));
            form.children('input#index').attr('value', $(this).attr('data-index'));
        });
    </script>
@stop

@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('message'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('app.Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table class="table table-striped table-bordered" id="declaratii">
                        <thead>
                        <tr>
                            <th class="text-center">{{ __('app.Code') }}</th>
                            <th class="text-center">{{ __('app.Name') }}</th>
                            <th class="text-center">{{ __('app.Auto') }}</th>
                            <th class="text-center">{{ __('app.Border') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                    </table>
                    <script type="text/javascript">
                        function format ( d ) {
                            return '<table class="table table-sm table-details-control">'+
                                '<tr>'+
                                "<td>{{ __('app.Phone') }}: <strong>"+ d.phone+'</strong></td>'+
                                '</tr><tr>'+
                                "<td>{{ __('app.Travelling from date') }}: <strong>"+ d
                                    .travelling_from_date+'</strong> '+
                                "{{ __('app.Travelling from city') }}: <strong>"+ d
                                    .travelling_from_city+'</strong></td>'+
                                '</tr><tr>'+
                                "<td>{{ __('app.Itinerary country list') }}: <strong>"+ d
                                    .itinerary_country_list+'</strong></td>'+
                                '</tr>'+
                                '</table>';
                        }
                        $(document).ready( function () {
                            let table = $('#declaratii').DataTable({
                                processing: true,
                                serverSide: true,
                                ajax: "{{ url('declaratii') }}",
                                columns: [
                                    {
                                        data:       'code',
                                        name:       'code',
                                        className:  'code-control',
                                    },
                                    { data: 'name', name: 'name' },
                                    { data: 'auto', name: 'auto' },
                                    { data: 'checkpoint', name: 'checkpoint' },
                                    {
                                        data:           null,
                                        className:      'details-control',
                                        orderable:      false,
                                        defaultContent: ''
                                    }
                                ],
                                columnDefs: [
                                    {
                                        render: function (data, type, row) {
                                            let signed = (row['signed'] == 1) ? '<img src="/icons/check.svg" alt="" ' +
                                                'width="14px" height="14px">' : '<img src="/icons/attention.svg" ' +
                                                'alt="" width="14px" height="14px">';
                                            return row['code'] + '  ' + signed + '<span class="d-none">'+row['url']+'</span>';
                                        },
                                        'targets': 0,
                                    },
                                ],
                                order: [[0, 'asc']],
                                pageLength: 10,
                                language: {
                                    url: "{{ asset('js/traducere_ro.json' )}}"
                                },
                                responsive: true
                            });
                            $('#declaratii tbody').on('click', 'td.details-control', function () {
                                let tr = $(this).closest('tr');
                                let row = table.row( tr );

                                if ( row.child.isShown() ) {
                                    row.child.hide();
                                    tr.removeClass('shown');
                                } else {
                                    row.child( format(row.data()) ).show();
                                    tr.addClass('shown');
                                }
                            } );
                            $('#declaratii tbody').on('click', 'td.code-control', function () {
                                let url = $(this).find('span.d-none').text();
                                window.location=url;
                            } );
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

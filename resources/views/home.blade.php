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
                <div class="card-header">
                    {{ __('app.Dashboard') }}
                    @if (Auth::user()->username !== 'admin_dsp')
                        search form
                    @endif
                    <div class="float-right">
                        <a href="javascript:void(0);" id="refresh-list" class="btn btn-secondary btn-sm" role="button"
                           aria-pressed="true">
                            {{ __('app.Refresh declarations list') }}
                        </a>
                    </div>
                </div>

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
                            <th class="text-center">{{ __('app.Status declaration') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                    </table>
                    <script type="text/javascript">
                        function format ( d ) {
                            let createdAt = (d.app_status == 1) ?
                                "<td>{{ __('app.Created at') }}: <strong>"+
                                d.created_at+'</strong></td>' :
                                "<td>{{ __('app.Created at') }}: <strong>"+
                                "{{ __('app.Not validated yet') }}</strong></td>";

                            let borderPassAt = (d.border_status == 1) ?
                                "<td>{{ __('app.Border status') }}: "+
                                "{{ __('app.Border pass at') }} <strong>"+
                                d.border_validated_at+'</strong> ' +
                                "{{ __('app.Border pass on') }} <strong>"+
                                d.checkpoint+'</strong>' + '</td>' :
                                "<td>{{ __('app.Border status') }}: <strong>"+
                                "{{ __('app.Border not pass yet') }}</strong></td>";

                            let dspAt = (d.dsp_status == 1) ?
                                "<td>{{ __('app.Dsp status') }}: "+
                                "{{ __('app.Dsp printed at') }} <strong>"+
                                d.dsp_validated_at+'</strong> ' +
                                "{{ __('app.Dsp user') }} <strong>"+
                                d.dsp_user_name+'</strong>' + '</td>' :
                                "<td>{{ __('app.Dsp status') }}: <strong>"+
                                "{{ __('app.Dsp not printed yet') }}</strong></td>";

                            let formatHtml = '<table class="table table-sm table-details-control">'+
                                '<tr>'+
                                "<td>{{ __('app.Phone') }}: <strong>"+ d.phone+'</strong></td>'+
                                '</tr><tr>'+
                                "<td>{{ __('app.Travelling from date') }}: <strong>"+
                                    d.travelling_from_date+'</strong> '+
                                "{{ __('app.Travelling from city') }}: <strong>"+
                                    d.travelling_from_city+'</strong></td>'+
                                '</tr><tr>'+
                                "<td>{{ __('app.Itinerary country list') }}: <strong>"+
                                    d.itinerary_country_list+'</strong></td>'+
                                '</tr><tr>'+
                                createdAt+
                                '</tr><tr>'+
                                borderPassAt+
                                '</tr><tr>'+
                                dspAt+
                                '</tr></table>';

                                return formatHtml;
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
                                        className:      'text-center status-declaration',
                                        orderable:      false,
                                        defaultContent: ''
                                    },
                                    {
                                        data:           null,
                                        className:      'text-center details-control',
                                        orderable:      false,
                                        defaultContent: ''
                                    }
                                ],
                                columnDefs: [
                                    {
                                        render: function (data, type, row) {
                                            let appStat = (row['app_status'] == 1) ?
                                                '<img src="/icons/app_ok.svg" ' + 'alt="' +
                                                row['created_at'] + '" ' +
                                                'width="20px" height="20px">' :
                                                '<img src="/icons/app_no.svg" ' +
                                                'alt="" width="20px" height="20px">';
                                            let borderStat = (row['border_status'] == 1) ?
                                                '<img src="/icons/border_ok.svg" ' + 'alt="' +
                                                row['border_validated_at'] + '" ' +
                                                'width="20px" height="20px">' :
                                                '<img src="/icons/border_no.svg" ' +
                                                'alt="" width="20px" height="20px">';
                                            let dspStat = (row['dsp_status'] == 1) ?
                                                '<img src="/icons/printer_ok.svg" ' + 'alt="' +
                                                row['dsp_validated_at'] + '" ' +
                                                'width="20px" height="20px">' :
                                                '<img src="/icons/printer_no.svg" ' +
                                                'alt="" width="20px" height="20px">';
                                            return appStat + borderStat + dspStat;
                                        },
                                        'width': 90,
                                        'targets': 4,
                                    },
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
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        $('#refresh-list').click(function(e){
                            e.preventDefault();
                            $.ajax({
                                type:'POST',
                                url:"{{ route('refresh-list') }}",
                                data:{refresh:true},
                                success:function(data){
                                    location.reload();
                                }
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header h3">
                        {{ $title }}
                    </div>
                    @if($reportButtons)
                        <div class="card-header">
                            <div class="float-right font-light" style="font-size: 1rem;">
                                @foreach($reportButtons AS $reportButton)
                                    {!! $reportButton !!}
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="card-header">
                        <div id="filterContainer">
                            <div>
                                {!! \BluefynInternational\ReportEngine\ReportBase::rendersFilters($filterColumns) !!}
                            </div>
                            <div>
                                <button id="download-csv">Download CSV</button>
                                <button id="download-xlsx">Download XLSX</button>
                                <button id="download-pdf">Download PDF</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="report-table"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    <link href="https://unpkg.com/tabulator-tables@4.9.3/dist/css/tabulator.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.9.3/dist/js/tabulator.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone.min.js"></script>
    <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.0.5/jspdf.plugin.autotable.js"></script>

    @include('report-engine::partials.modal')
    <script type="application/javascript">
        Tabulator.prototype.extendModule("format", "formatters", {
            stageFormatter:function(cell, formatterParams){
                return '<div class="sales-order-stage text-truncate background-' + lowerCaseSlugify(cell.getValue()) + '">'
                    + cell.getValue()
                    + '</div>';
            }
        });

        Tabulator.prototype.extendModule("accessor", "accessors", {
            htmlToText:function(value, data, accessorParams){
                let parsedElement = $.parseHTML(value)[0]
                return parsedElement ? parsedElement.innerText : ''
            }
        });

        function popupConfirm(row, title, body, routeTemplate, routeReplacements, routeHttpAction)
        {
            $('#confirmationModal .modal-title').text(title)
            $('#confirmationModal .modal-body').text(body)
            let m = $('#confirmationModal').modal('show')

            m.find('a.confirm').unbind('click').click(function () {
                m.find('a.confirm').unbind('click')
                let data = row.getData()
                let route = routeTemplate
                for(let index in routeReplacements) {
                    let propName = routeReplacements[index]
                    if (data.hasOwnProperty(propName)) {
                        route = route.replace('%' + propName + '%', data[propName])
                    }
                }

                $.ajax({
                    url: route,
                    type: routeHttpAction,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        row.delete()
                        $('#confirmationModal').modal('hide')
                    },
                    error: function(result) {
                        alert(result.responseJSON.message);
                    }
                });

            })
            m.find('a.cancel').unbind('click').click(function () {
                $('#confirmationModal').modal('hide');
            })
        }

        (() => {
            $('#filterContainer .custom-select').change()

            let table = new Tabulator('#report-table', {
                columns:{!! json_encode($columns) !!},
                height:"500px",
                layout:"fitColumns",
                movableColumns: true,
                tooltips:true,
                placeholder:"No Data, Set Filters And Try Again"
                @if($rowContextActions ?? false)
                ,rowContextMenu:[
                    @foreach($rowContextActions as $rowContextAction)
                    {
                        label: '{{ $rowContextAction->getLabel() }}',
                        action: function(e, row){
                            popupConfirm(
                                row,
                                '{{ $rowContextAction->getLabel() }}',
                                '{{ $rowContextAction->getMessage() }}',
                                '{{ $rowContextAction->getLinkTemplate() }}',
                                {!! json_encode($rowContextAction->getLinkTemplateReplacements()) !!},
                                '{{ $rowContextAction->getHttpAction() }}',
                            )
                        }
                    },
                    @endforeach
                ]
                @endif

            });

            document.getElementById('download-csv').addEventListener('click', function(){
                table.download('csv', 'data.csv');
            });

            //trigger download of data.xlsx file
            document.getElementById('download-xlsx').addEventListener('click', function(){
                table.download('xlsx', 'data.xlsx', {sheetName:'My Data'});
            });

            //trigger download of data.pdf file
            document.getElementById('download-pdf').addEventListener('click', function(){
                table.download('pdf', 'data.pdf', {
                    orientation:'portrait', //set page orientation to portrait
                    title:'Example Report', //add title to report
                });
            });

            // Build and submit data ajax request
            document.getElementById('report-filter-submit').addEventListener('click', function(){
                let possibleFilters = {!! $filterColumns->mapWithKeys(function ($column) {return [$column->name() => $column->name()];})->values()->toJson() !!};
                let endpoint = '{!! $route !!}.json';
                let filterParams = new URLSearchParams();

                $('.report-filter-input').each(function (i, el) {
                    let element = $(el)
                    let value = element.val()

                    if (value) {
                        let filterName = element.attr('id')

                        filterName = filterName.substring(0, filterName.indexOf('_filter'))

                        if (possibleFilters.includes(filterName)) {
                            let action = $('#' + element.attr('id') + '_action').val()

                            filterParams.append('filters['+filterName+']['+action+']', value)
                        }
                    }
                })

                history.replaceState(null, '', endpoint.replace('.json', '') + '?' + filterParams.toString());
                table.setData(endpoint + '?' + filterParams.toString());
            });

            @if($autoloadInitialData) {
                $('#report-filter-submit').click();
            }
            @endif

            $('.report-filter-input').on('keypress',function(e) {
                if(e.which === 13) {
                    $('#report-filter-submit').click();
                }
            });

        })();
    </script>
@endsection

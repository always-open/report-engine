<div class="form-row">
    @foreach($columns ?? [] as $column)
        {!! $column->renderFilter() !!}
    @endforeach
    @includeWhen($includeSubmit ?? true, 'report-engine::partials.filters-submit', ['offset' => $offset])
</div>

<div class="col-md-3 mb-3">
    <input type="hidden" id="{{ $field }}_filter_action"/>
    <label class="my-1 mr-2" for="{{ $field }}_filter">{{ $label }}</label>
    <div class="input-group mb-1">
        <select
            id="{{ $field }}_filter"
            class="custom-select report-filter-input"
            onchange="document.getElementById('{{ $field }}_filter_action').value = this.selectedOptions[0].getAttribute('data-operator') || '';"
        >
            <option></option>
            <option value="1" data-operator="{{ \BluefynInternational\ReportEngine\BaseFeatures\Filters\IsEmptyFilter::key() }}">Yes</option>
            <option value="0" data-operator="{{ \BluefynInternational\ReportEngine\BaseFeatures\Filters\IsNotEmptyFilter::key() }}">No</option>
        </select>
    </div>
</div>

<div class="col-md-3 mb-3">
    <input type="hidden" id="{{ $field }}_filter_action" value="{{ \BluefynInternational\ReportEngine\BaseFeatures\Filters\EqualsFilter::key() }}"/>
    <label class="my-1 mr-2" for="{{ $field }}_filter">{{ $label }}</label>
    <div class="input-group mb-1">
        <select id="{{ $field }}_filter" class="custom-select report-filter-input">
            <option></option>
            @foreach($options as $optionKey => $optionValue)
                <option
                @if (false === ($useKey ?? false))
                    {{ $optionValue == $value->first() ? 'selected' : '' }}
                @else
                    {{ $optionKey == $value->first() ? 'selected' : '' }}
                    value="{{$optionKey}}"
                @endif
                >{!! $optionValue !!}</option>
            @endforeach
        </select>
    </div>
</div>

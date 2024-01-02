@foreach($fields as $field)
    <div class="d-flex justify-content-between js-field-wrapper">
        <label>
            <input type="hidden" name="fields[]" value="{{$field}}">
            {{$field}}
        </label>
        <div>
            <button class="btn js-sort-up" type="button">
                <svg width="16" height="16" fill="currentColor" class="bi bi-list">
                    <use xlink:href="#sort-up"></use>
                </svg>
            </button>
            <button class="btn js-sort-down" type="button">
                <svg width="16" height="16" fill="currentColor" class="bi bi-list">
                    <use xlink:href="#sort-down"></use>
                </svg>
            </button>
            <button class="btn js-remove" type="button">
                <svg width="16" height="16" fill="currentColor" class="bi bi-list">
                    <use xlink:href="#remove"></use>
                </svg>
            </button>
        </div>
    </div>
@endforeach
<button class="btn js-reload" type="button">
    <svg width="16" height="16" fill="currentColor" class="bi bi-list">
        <use xlink:href="#reload"></use>
    </svg>
</button>

<div>
    <label for="{{$id}}" class="form-label">{{$label}}</label>
    <input @class($classes) type="file" id="{{$id}}" name="{{$name}}"
           @disabled($readonly) @if($multiple) multiple @endif>
    @isset($error)
        <div class="invalid-feedback">
            {{$error}}
        </div>
    @endisset
</div>

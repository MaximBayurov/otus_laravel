<div class="form-floating">
    <input type="{{$type}}" @class($classes) id="{{$id}}" placeholder="{{$placeholder}}"
           @isset($value)value="{{$value}}"@endisset name="{{$name}}" @readonly($readonly)>
    <label for="{{$id}}" class="form-label">{{$label}}</label>
    @isset($error)
        <div class="invalid-feedback">
            {{$error}}
        </div>
    @endisset
</div>

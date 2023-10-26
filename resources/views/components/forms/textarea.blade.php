<div class="form-floating">
    <textarea @class($classes) id="{{$id}}" placeholder="{{$placeholder}}" name="{{$name}}" @readonly($readonly) style="height: {{$height}}px">{{$value}}</textarea>
    <label for="{{$id}}" class="form-label">{{$label}}</label>
    @isset($error)
        <div class="invalid-feedback">
            {{$error}}
        </div>
    @endisset
</div>

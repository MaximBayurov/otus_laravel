<div class="form-check text-start my-3">
    <input class="form-check-input" type="checkbox" name="{{$name}}" value="{{$value}}" id="{{$id}}" @checked(old($name, request()->has($name)))>
    <label class="form-check-label" for="{{$id}}">
        {{$label}}
    </label>
</div>

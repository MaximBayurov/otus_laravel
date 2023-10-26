<div class="form-floating">
    <select @class($classes) id="{{$id}}" name="{{$name}}" @disabled($disabled)>
        <option value>{{$unselectedOption}}</option>
        @foreach($options as $option)
            <option @if($value == $option['value']) selected @endif value="{{$option['value']}}">
                [{{$option['value']}}] {{$option['title']}}
            </option>
        @endforeach
    </select>
    <label for="{{$id}}">{{$label}}</label>
    @isset($error)
        <div class="invalid-feedback">
            {{$error}}
        </div>
    @endisset
</div>

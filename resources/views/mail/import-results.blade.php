<div>
    Импорт модели "{{$model}}" успешно завершён
</div>
<ul>
@foreach($stats as $stat)
    <li>
        {{$stat['title']}}: {{$stat['count']}}
    </li>
@endforeach
</ul>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>详情</title>
</head>

<body>
<div>
    <ol>
        @foreach($data as $value)
            <li>

                <a href="{{url('detail?stock_number='.$value->id)}}">{{$value->name}}</a>

            </li>

        @endforeach
    </ol>
</div>

</body>
</html>

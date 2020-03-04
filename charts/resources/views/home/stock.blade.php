<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>详情</title>
</head>

<body>
<div><a href="{{url('stock')}}"> 返回股票列表 </a></div>
<div>
    <form method="post" action="{{url('newstock')}}">
        {{csrf_field()}}

        <label>股票名称：</label>
        <input type="text" name="name">
        <input type="hidden" name="category_id" value="{{$category_id}}">
        <button type="submit">添加新股票:{{$category_id}}</button>

    </form>
</div>
<div>
    <ol>
        @foreach($data as $value)
            <li>

                <a href="{{url('detail?stock_number='.$value->id)}}">{{$value->name}}</a>
                <td><a href="{{url('deleteStock?id='.$value->id.'&category_id='.$category_id)}}">删除</a></td>

            </li>

        @endforeach
    </ol>
</div>

</body>
</html>

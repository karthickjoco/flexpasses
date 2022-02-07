<!DOCTYPE html>
<html>
<head>
    <title>JOCO Flex Pass Trip Summary</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="
        margin: auto;
        padding: 0px;
        font-family: Poppins;
        color:#000000;">
<div style="margin-top:10px;">
    <div style="text-align: center;margin-top:50px;width: 100%"><a href="{{url('')}}"><img src="https://ridejoco.com/delivery/img/emaillogo.png" style="max-width: 100%;height: auto"></a></div>
    <p style="margin: 5px;font-size: 16px;">&nbsp;Here are the trips taken on {{\Carbon\Carbon::now()->subtract('1 day')->format('m/d/Y')}} </p>
    <div style="overflow-x:auto;">
    <table style="margin: 10px;width: 95%;" border="1"  cellpadding="0" cellspacing="0">
        <thead>
            <th>S.no</th>
            <th>Bike ID</th>
            <th>Start Time</th>
            <th>Account ID</th>
        </thead>
        @foreach($details as $detail)
            <tr>
                <td  style="padding: 3px"> {{ $loop->index+1 }} </td>
                <td  style="padding: 3px;"> {{ $detail->name }} </td>
                <td  style="padding: 3px;"> {{ $detail->startdate }} </td>
                <td  style="padding: 3px;"> {{ $detail->userName }} </td>
            </tr>
        @endforeach
    </table>
    </div>
    <div style=" margin:10px;
        font-size:14px;">
        <p style="margin: 2px;font-weight: bold;">Total Trip Qty: {{count($details)}}</p>
        <p style="margin: 2px;font-weight: bold;">Sub Total: ${{number_format(20*count($details),2)}}</p>
        <p style="margin: 2px;font-weight: bold;">Tax at 8.875%: {{ number_format(((20*count($details))*8.875)/100,2) }} </p>
        <p style="margin: 2px;font-weight: bold;"> Total: ${{ number_format(20*count($details)+(((20*count($details))*8.875)/100),2) }}</p>
    </div>

    <p style="  text-align: center;
        font-size: 15px;">Follow Our Journey</p>
    <div style="  text-align: center;
        font-size: 15px;">
        <a href="https://www.facebook.com/RIDEJOCO/" target="_blank"><img src="https://ridejoco.com/delivery/img/fb.png"></a>
        <a href="https://www.instagram.com/ridejoco/" target="_blank"> <img src="https://ridejoco.com/delivery/img/ig.png"></a>
        <a href="https://twitter.com/RideJOCO" target="_blank"> <img src="https://ridejoco.com/delivery/img/tw.png"></a>
    </div>
    <p>&nbsp;</p>
</div>
</body>
</html>
{{--changed  file--}}

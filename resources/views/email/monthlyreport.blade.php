<!DOCTYPE html>
<html>
<head>
    <title>Summary of trips taken</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="
        margin: auto;
        padding: 0px;
        font-family: Poppins;
        color:#000000;">
<div style="margin-top:10px;">
    <div style="text-align: center;margin-top:50px;width: 100%"><a href="{{url('')}}"><img src="https://ridejoco.com/delivery/img/emaillogo.png" style="max-width: 100%;height: auto"></a></div>
    <p style="margin: 10px; font-size:16px;">&nbsp;A summary of trips taken From {{\Carbon\Carbon::now()->subtract('1 month')->format('d')}}th {{\Carbon\Carbon::now()->subtract('1 month')->format('M Y')}}  to {{\Carbon\Carbon::now()->subtract('1 day')->format('d')}}th  {{\Carbon\Carbon::now()->subtract('1 day')->format('M Y')}} </p>
    <div style=" margin:10px;
        font-size:16px;">
        <p style="margin: 2px;">Totals Trip Qty: {{count($details)}}</p>
        <p style="margin: 2px;">Sub Total: ${{number_format(20*count($details),2)}}</p>
        <p style="margin: 2px;"> Total: ${{ number_format(20*count($details)+(((20*count($details))*8.875)/100),2) }}</p>
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

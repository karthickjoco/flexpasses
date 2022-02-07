<?php

use App\Models\completed;
use App\Models\ongoing;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/profiles', [App\Http\Controllers\HomeController::class, 'profiles'])->name('profiles');
Auth::routes([
//    'login'=>false,

    'register' => false, // Register Routes...

    //'reset' => false, // Reset Password Routes...

    //'verify' => false,
]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('sendemail', function () {
    echo Carbon::now()->format('Y-m-d H:i:s');exit;
    $datelastmonth= Carbon::now()->subtract('1 month')->format('Y-m-d');
    $datecurrentmonth= Carbon::now()->subtract('1 day')->format('Y-m-d');


    $select=completed::join('profiles','completeds.userId','=','profiles.userId')->join('vechiles','vechiles.vechicleid','=','completeds.vehicleId')->whereDate('completeds.date','>=',$datelastmonth)->whereDate('completeds.date','<=',$datecurrentmonth)->get(['vechiles.name','completeds.date as startdate','profiles.userName']);
    //print_r(json_encode($select));
    //DB::enableQueryLog();
    $select2=ongoing::join('profiles','ongoings.userId','=','profiles.userId')->join('vechiles','vechiles.vechicleid','=','ongoings.vechicleid')->whereDate('ongoings.booking_date','>=',$datelastmonth)->whereDate('ongoings.booking_date','<=',$datecurrentmonth)->whereNotIn('ongoings.orderId',function($query) use ($datelastmonth,$datecurrentmonth){
        $query->select('tripId')->from('completeds')->whereDate('date','>=',$datelastmonth)->whereDate('date','<=',$datecurrentmonth);
    })->get(['vechiles.name','ongoings.booking_date as startdate','profiles.userName']);
    //dd(DB::getQueryLog());
    //print_r(json_encode($select2));
    $details=$select2->concat($select);
    if(count($details)){
        return view('email.monthlyreport',compact('details'));
    }else{
        return redirect('404');
    }

    exit;
});

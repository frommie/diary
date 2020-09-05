<?php

use App\User;
use App\Calendar;
use App\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response([
            'message' => ['These credentials do not match our records.']
        ], 404);
    }

    $token = $user->createToken('my-app-token')->plainTextToken;

    $response = [
        'user' => $user,
        'token' => $token
    ];

    return response($response, 201);
});

Route::post('/register', function (Request $request) {
    $data = $request->validate([
      'name' => 'required',
      'email' => 'required|email',
      'password' => 'required'
    ]);

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->save();

    $calendar = new Calendar();
    $calendar->user_id = $user->id;
    $calendar->save();

    $token = $user->createToken('my-app-token')->plainTextToken;

    $response = [
        'user' => $user,
        'token' => $token
    ];

    return response($response, 201);
});

Route::post('/newentry', function (Request $request) {
    $data = $request->validate([
      'date' => 'required',
      'content' => 'required'
    ]);

    $user_id = $request->user()->id;
    $entry_date = explode('T', $request->date)[0];
    $entry = Entry::firstOrNew(['date' => $entry_date]);
    $entry->calendar_id = User::find($user_id)->calendar->id;
    $entry->date = explode('T', $request->date)[0];
    $entry->content = $request->content;
    $entry->mood = $request->mood;
    $entry->save();

    $response = $request;

    return response($response, 200);
})->middleware('auth:sanctum');

Route::post('/entry', function (Request $request) {
    $entry = Entry::where('date', $request->date)->first();
    $response = $entry;

    return response($response, 200);
})->middleware('auth:sanctum');

Route::get('/get_events/{month}', function ($month, Request $request) {
  $user_id = $request->user()->id;
  $calendar_id = Calendar::where('user_id', $user_id)->first()->id;
  $entries = Entry::where('calendar_id', $calendar_id)->whereMonth('date', '=', $month)->get();
  $response = $entries;

  return response($response, 200);
})->middleware('auth:sanctum');

Route::get('/calendars', function (Request $request) {
  $calendars = Calendar::all();
  $response = $calendars;
  return response($response, 200);
});

Route::get('/entries', function (Request $request) {
  $entries = Entry::all();
  $response = $entries;
  return response($response, 200);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

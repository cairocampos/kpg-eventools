<?php
Route::get('/', function () {
    return redirect("/login");
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::middleware("auth")->group(function() {
    Route::prefix("/events")->group(function() {
        Route::get("/", "EventController@index");
        Route::post("/", "EventController@store");
        Route::get("/{id}", "EventController@show")->name("events.show");
        Route::put("/{id}", "EventController@update");
        Route::get("/{id}/participants", "EventController@getParticipants");
        Route::post("/{id}/cancel", "EventController@cancel");
    });    
    Route::get("/users", "UserController@index");
    Route::prefix("/invitations")->group(function() {
        Route::post("/", "InvitationController@create");
        Route::post("/confirm", "InvitationController@confirm");
        Route::post("/cancel", "InvitationController@cancelParticipation");
    });

    Route::prefix("/notifications")->group(function() {
        Route::get("/", "NotificationController@index");
        Route::post("/{id}/read", "NotificationController@read");
    });
});


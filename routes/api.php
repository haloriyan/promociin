<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "page"], function () {
    Route::post('home', "PageController@home");
    Route::post('explore', "PageController@explore");
});

Route::group(['prefix' => "user"], function () {
    Route::post('login', "UserController@login");
    Route::post('logout', "UserController@logout");
    Route::post('register', "UserController@register");
    Route::post('auth', "UserController@auth");
    Route::post('update-basic', "UserController@updateBasic");
    Route::post('update-bio', "UserController@updateBio");
    Route::post('update-photo', "UserController@updatePhoto");

    Route::post('forget-password', "UserController@forgetPassword");
    Route::post('reset-password', "UserController@resetPassword");

    Route::group(['prefix' => "{username}"], function () {
        Route::post('profile', "UserController@profile");
        Route::post('follow', "UserController@follow");
    });

    Route::post('otp-auth', "OtpController@auth");

    Route::group(['prefix' => "experience"], function () {
        Route::post('store', "ExperienceController@store");
        Route::post('delete', "ExperienceController@delete");
        Route::post('update', "ExperienceController@update");
        Route::post('/', "ExperienceController@mine");
    });
});

Route::group(['prefix' => "experience"], function () {
    Route::post('store', "ExperienceController@store");
    Route::post('update', "ExperienceController@update");
    Route::post('delete', "ExperienceController@delete");
});
Route::group(['prefix' => "education"], function () {
    Route::post('store', "EducationController@store");
    Route::post('update', "EducationController@update");
    Route::post('delete', "EducationController@delete");
    Route::post('/', "EducationController@list");
});
Route::group(['prefix' => "skill"], function () {
    Route::post('store', "SkillController@store");
    Route::post('update', "SkillController@update");
    Route::post('delete', "SkillController@delete");
    Route::post('/', "SkillController@list");
});

Route::group(['prefix' => "content"], function () {
    Route::post('store', "ContentController@store");
    Route::post('delete', "ContentController@delete");

    Route::group(['prefix' => "{contentID}"], function () {
        Route::post('like', "ContentController@like");
        Route::post('comment', "ContentController@comment");
        Route::get('stream', "ContentController@stream");
    });

    Route::post('/', "ContentController@myContent");
});

Route::group(['prefix' => "comment/{contentID}"], function () {
    Route::post('store', "CommentController@store");
    Route::post('delete', "CommentController@delete");
    Route::post('like', "CommentController@like");
    Route::post('/', "CommentController@get");
});

Route::group(['prefix' => "appointment"], function () {
    Route::post('{username}/store', "AppointmentController@store");
    Route::post('accept', "AppointmentController@acceptInvitation");
    Route::post('/', "AppointmentController@list");
});
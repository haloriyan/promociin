<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => "page"], function () {
    Route::post('home', "PageController@home");
    Route::post('explore', "PageController@explore");
});

Route::group(['prefix' => "ad"], function () {
    Route::get('fetch', "AdController@fetch");
    Route::post('click', "AdController@click");
});

Route::group(['prefix' => "announcement"], function () {
    Route::get('/', "AnnouncementController@fetch");
    Route::post('store', "AnnouncementController@store");
});

Route::group(['prefix' => "admin"], function () {
    Route::post('login', "AdminController@login");
    Route::post('logout', "AdminController@logout");

    Route::group(['prefix' => "tag"], function () {
        Route::post('create', "TagController@create");
        Route::post('delete', "TagController@delete");
        Route::post('update', "TagController@update");
        Route::post('/', "TagController@list");
    });

    Route::group(['prefix' => "ad"], function () {
        Route::post('create', "AdController@create");
        Route::post('update', "AdController@update");
        Route::group(['prefix' => "{id}"], function () {
            Route::post('views', "AdController@views");
            Route::post('clicks', "AdController@clicks");
            Route::post('delete', "AdController@delete");
            Route::post('/', "AdController@detail");
        });
        
        Route::post('/', "AdController@list");
    });

    Route::group(['prefix' => "user"], function () {
        Route::post('delete', "UserController@delete");
        Route::post('/', "AdminController@user");
    });

    Route::group(['prefix' => "content"], function () {
        Route::post('reported', "ContentController@reportedContent");
    });
});

Route::group(['prefix' => "user"], function () {
    Route::post('login', "UserController@login");
    Route::post('logout', "UserController@logout");
    Route::post('register', "UserController@register");
    Route::post('auth', "UserController@auth");
    Route::post('update-basic', "UserController@updateBasic");
    Route::post('update-bio', "UserController@updateBio");
    Route::post('update-photo', "UserController@updatePhoto");
    Route::post('delete-account', "UserController@deleteAccount");
    Route::post('save-industry', "UserController@saveIndustry");
    Route::post('save-country', "UserController@saveCountry");
    Route::post('request-deletion', "UserController@requestDeletion");
    Route::get('search', "UserController@search");

    Route::post('forget-password', "UserController@forgetPassword");
    Route::post('reset-password', "UserController@resetPassword");

    Route::group(['prefix' => "block"], function () {
        Route::post('list', "UserController@blockList");
        Route::post('unblock', "UserController@unblock");
        Route::post('/', "UserController@block");
    });

    Route::group(['prefix' => "{username}"], function () {
        Route::post('profile', "UserController@profile");
        Route::post('follow', "UserController@follow");
    });

    Route::post('otp-auth', "OtpController@auth");
    Route::post('otp-resend', "OtpController@resend");

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
Route::group(['prefix' => "certificate"], function () {
    Route::post('store', "CertificateController@store");
    Route::post('update', "CertificateController@update");
    Route::post('delete', "CertificateController@delete");
    Route::post('/', "CertificateController@list");
});

Route::group(['prefix' => "content"], function () {
    Route::post('store', "ContentController@store");
    Route::post('delete', "ContentController@delete");
    Route::post('report', "ContentController@report");

    Route::group(['prefix' => "{contentID}"], function () {
        Route::post('like', "ContentController@like");
        Route::post('dislike', "ContentController@dislike");
        Route::post('comment', "ContentController@comment");
        Route::post('view', "ContentController@hitView");
        Route::get('stream', "ContentController@stream");
        Route::get('/', "ContentController@getData");
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
    Route::post('answer', "AppointmentController@answerInvitation");
    Route::post('send-link', "AppointmentController@sendLink");
    Route::post('/', "AppointmentController@list");
});

Route::group(['prefix' => "chat"], function () {
    Route::post('load', "ChatController@load");
    Route::post('send', "ChatController@send");
    Route::post('room', "ChatController@room");
    Route::post('delete-room', "ChatController@deleteRoom");
});

Route::group(['prefix' => "stream"], function () {
    Route::post('post', "LivestreamController@post");

    Route::post('start', "LivestreamController@start");
    Route::get('list', "LivestreamController@list");
    
    Route::group(['prefix' => "code"], function () {
        Route::post('generate', "LivestreamController@generateCode");
        Route::post('auth', "LivestreamController@authCode");
        Route::post('delete', "LivestreamController@deleteCode");
        Route::post('/', "LivestreamController@liveCode");
    });
    
    Route::group(['prefix' => "{key}"], function () {
        Route::get('data', "LivestreamController@getDatas");
        Route::post('chat/post', "LivestreamController@postChat");
    });
});

Route::group(['prefix' => "training-center"], function () {
    Route::post('store', "TcController@store");
    Route::get('list', "TcController@list");

    Route::group(['prefix' => "{id}/course"], function () {
        Route::post('store', "TcController@courseStore");
    });
});

Route::group(['prefix' => "match"], function () {
    Route::post('/', "PageController@jobMatch");
});
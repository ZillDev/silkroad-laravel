<?php

Auth::routes();


Route::get('/', 'IndexController@index')->name('index');

Route::get('/news/{slug}', 'NewsController@index')->name('news-slug');
Route::get('/news-archive', 'NewsController@archive')->name('news-archive');

// Needed to be logged in after that
Auth::routes(['verify' => true]);

// User Dashboard
Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix' => 'backend', 'middleware' => ['role:backend']], function () {
    Route::get('/', 'Backend\BackendController@index')->name('index-backend');

    // Ticket
    Route::group(['prefix' => 'ticket'], function () {
        Route::get('/{conversation?}', 'TicketController@list')->name('ticket-index-list')->where(['conversation' => '[0-9]+']);
        Route::get('/fetch', 'TicketController@fetch')->name('ticket-fetch-backend');
        Route::post('/send', 'TicketController@send')->name('ticket-send-backend');
        Route::get('/conversations', 'TicketController@fetchConversations')->name('ticket-conversations-backend');
        Route::get('/settings', 'TicketController@settings')->name('ticket-settings-backend');
        Route::post('/close', 'TicketController@close')->name('ticket-close-backend');
        Route::group(['prefix' => 'category'], function () {
            Route::match(['get', 'post'], '/create', 'TicketController@categoryCreate')->name('ticket-category-create');
            Route::match(['get', 'post'], '/{id}', 'TicketController@categoryUpdate')->name('ticket-category-update');
            Route::post('/delete/{id}', 'TicketController@categoryDelete')->name('ticket-category-delete');
        });
        Route::group(['prefix' => 'priority'], function () {
            Route::match(['get', 'post'], '/create', 'TicketController@priorityCreate')->name('ticket-priority-create');
            Route::match(['get', 'post'], '/{id}', 'TicketController@priorityUpdate')->name('ticket-priority-update');
            Route::post('/delete/{id}', 'TicketController@priorityDelete')->name('ticket-priority-delete');
        });
    });

    // Silkroad
    Route::group(['prefix' => 'silkroad'], function () {
        Route::group(['prefix' => 'notice'], function () {
            Route::get('/', 'Backend\SilkroadNoticeController@noticeIndex')->name('sro-notice-index-backend');
            Route::get('/create', 'Backend\SilkroadNoticeController@noticeCreate')->name('sro-notice-create-backend');
            Route::post('/save', 'Backend\SilkroadNoticeController@noticeSave')->name('sro-notice-save-backend');
            Route::get('/{id}/edit', 'Backend\SilkroadNoticeController@noticeEdit')->name('sro-notice-edit-backend');
            Route::post('/{id}/update', 'Backend\SilkroadNoticeController@noticeEditPatch')->name('sro-notice-patch-backend');
            Route::delete('/{id}/destroy', 'Backend\SilkroadNoticeController@noticeDestroy')->name('sro-notice-edit-destroy');
        });

        Route::get('/user', 'Backend\SilkroadController@indexSroUser')->name('sro-user-index-user-backend');
        Route::get('/user-datatables', 'Backend\SilkroadController@sroUserDatatables')->name('sro-user-datatables-backend');
        Route::get('/user/{user}/edit', 'Backend\SilkroadController@sroUserEdit')->name('sro-user-edit-backend');

        Route::get('/players', 'Backend\SilkroadController@indexSroPlayer')->name('sro-players-index-backend');
        Route::get('/players-datatables', 'Backend\SilkroadController@SroPlayerDatatables')->name('sro-players-datatables-backend');
        Route::get('/players/{char}/edit', 'Backend\SilkroadController@sroPlayerEdit')->name('sro-players-edit-backend');

        Route::get('/guilds', 'Backend\SilkroadGuildController@indexSroGuild')->name('sro-guild-index-backend');
        Route::get('/guilds-datatables', 'Backend\SilkroadGuildController@sroGuildDatatables')->name('sro-guild-datatables-backend');
        Route::get('/guilds/{guild}/edit', 'Backend\SilkroadGuildController@sroGuildEdit')->name('sro-guild-edit-backend');
        Route::get('/guilds/{guild}/edit-datatables', 'Backend\SilkroadGuildController@sroGuildEditDatatables')->name('sro-guild-edit-datatables-backend');

        // Patching TB_User
        Route::post('/user/{user}/silk/add', 'Backend\SilkroadController@sroUserSilkAdd')->name('sro-user-silk-add-backend');
        Route::post('/user/{user}/block/add', 'Backend\SilkroadController@sroUserBlockAdd')->name('sro-user-block-add-backend');
        Route::post('/user/{user}/block/destory', 'Backend\SilkroadController@sroUserBlockDestory')->name('sro-user-block-destroy-backend');

        // Patching _Char
        Route::post('/players/{char}/unstuck', 'Backend\SilkroadController@sroUnstuckChar')->name('sro-players-unstuck');
    });

    // Web
    Route::group(['prefix' => 'web'], function () {
        Route::group(['prefix' => 'downloads'], function () {
            Route::get('/', 'Backend\DownloadsController@index')->name('downloads-index-backend');
            Route::get('/add', 'Backend\DownloadsController@create')->name('downloads-create-backend');
            Route::post('/create', 'Backend\DownloadsController@create')->name('downloads-create-backend');
            Route::get('/{download}/edit', 'Backend\DownloadsController@edit')->name('downloads-edit-backend');
            Route::patch('/{download}/update', 'Backend\DownloadsController@update')->name('downloads-update-backend');
            Route::post('/{download}/destroy', 'Backend\DownloadsController@destroy')->name('downloads-destroy-backend');
        });
        Route::group(['prefix' => 'images'], function () {
            Route::get('/', 'Backend\ImagesController@index')->name('images-index-backend');
            Route::get('/add', 'Backend\ImagesController@show')->name('images-show-backend');
            Route::post('/create', 'Backend\ImagesController@create')->name('images-create-backend');
            Route::post('/{image}/destroy', 'Backend\ImagesController@destroy')->name('images-destroy-backend');
        });

        Route::resource('/news', 'Backend\NewsController', [
            'as' => 'backend-news'
        ]);

        Route::group(['prefix' => 'voucher'], function () {
            Route::get('/', 'Backend\VoucherController@index')->name('voucher-index-backend');
            Route::get('/datatables', 'Backend\VoucherController@indexDatatables')->name('voucher-index-datatables-backend');
            Route::get('/add', 'Backend\VoucherController@addForm')->name('voucher-add-backend');
            Route::post('/create', 'Backend\VoucherController@create')->name('voucher-create-backend');
            Route::post('/{id}/destroy', 'Backend\VoucherController@destroy')->name('voucher-destroy-backend');
        });
    });

    // Logging
    Route::get('/smc-log', 'Backend\BackendController@smclogIndex')->name('smclog-index-backend');
    Route::get('/smc-log-datatables', 'Backend\BackendController@smclogDatatables')->name('smclog-datatables-backend');

    Route::get('/users-created-counts', 'Backend\UsersCreatedCounts@index')->name('users-created-counts-backend');

    Route::get('/users-blocked', 'Backend\BackendController@blockedAccountsIndex')->name('users-blocked-backend');
    Route::get('/users-blocked-datatables', 'Backend\BackendController@blockedAccountsDatatables')->name('users-blocked-datatables-backend');

    Route::get('/worldmap', 'Backend\BackendController@worldmapIndex')->name('worldmap-index-backend');
});

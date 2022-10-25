<?php

use App\Http\Controllers\GDCS\Web\AuthController;
use App\Http\Controllers\GDCS\Web\Tools\AccountLinkController;
use App\Http\Controllers\GDCS\Web\Tools\CustomSongController;
use App\Http\Controllers\GDCS\Web\Tools\LevelTransferController;
use App\Http\Presenters\GDCS\DashboardPresenter;
use App\Http\Presenters\GDCS\HomePresenter as GDCS_HomePresenter;
use App\Http\Presenters\GDCS\Tools\AccountLinkPresenter;
use App\Http\Presenters\GDCS\Tools\LevelTransferPresenter;
use App\Http\Presenters\GDCS\Tools\SongCustomPresenter;
use App\Http\Presenters\GDProxy\HomePresenter as GDProxy_HomePresenter;
use App\Http\Presenters\NGProxy\HomePresenter as NGProxy_HomePresenter;
use Illuminate\Support\Facades\Route;

Route::group([
    'domain' => 'gf.geometrydashchinese.com',
    'as' => 'gdcs.',
], static function () {
    Route::get('/', [GDCS_HomePresenter::class, 'render'])->name('home');

    Route::group([
        'as' => 'auth.',
        'prefix' => 'auth'
    ], static function () {
        Route::group([
            'middleware' => ['guest:gdcs']
        ], static function () {
            Route::inertia('/login', 'GDCS/Auth/Login')->name('login');
            Route::post('/login', [AuthController::class, 'login'])->name('login.api');
        });
        Route::group([
            'middleware' => ['auth:gdcs']
        ], static function () {
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout.api');
        });
    });

    Route::group([
        'prefix' => 'dashboard',
        'as' => 'dashboard.',
        'middleware' => ['auth:gdcs']
    ], static function () {
        Route::inertia('/', 'GDCS/Dashboard/Home')->name('home');
        Route::get('/account/{account}', [DashboardPresenter::class, 'renderAccountInfo'])->name('info.account');
    });

    Route::group([
        'prefix' => 'tools',
        'as' => 'tools.',
        'middleware' => ['auth:gdcs']
    ], static function () {
        Route::inertia('/', 'GDCS/Tools/Home')->name('home');

        Route::group([
            'prefix' => 'account',
            'as' => 'account.'
        ], static function () {
            Route::group([
                'prefix' => 'link',
                'as' => 'link.'
            ], static function () {
                Route::get('/', [AccountLinkPresenter::class, 'renderHome'])->name('home');
                Route::put('/', [AccountLinkController::class, 'create'])
                    ->middleware('throttle:gdcs_remote')
                    ->name('create.api');

                Route::delete('/{link}', [AccountLinkController::class, 'delete'])->name('delete.api');
            });
        });

        Route::group([
            'prefix' => 'song',
            'as' => 'song.'
        ], static function () {
            Route::group([
                'prefix' => 'custom',
                'as' => 'custom.'
            ], static function () {
                Route::get('/', [SongCustomPresenter::class, 'renderHome'])->name('home');
                Route::put('/link', [CustomSongController::class, 'createFromLink'])->name('create.link.api');
                Route::put('/netease', [CustomSongController::class, 'createFromNetease'])->name('create.netease.api');
            });
        });

        Route::group([
            'prefix' => 'level',
            'as' => 'level.'
        ], static function () {
            Route::group([
                'prefix' => 'transfer',
                'as' => 'transfer.'
            ], static function () {
                Route::get('/', [LevelTransferPresenter::class, 'renderHome'])->name('home');

                Route::group([
                    'prefix' => 'in',
                    'as' => 'in.'
                ], static function () {
                    Route::get('/{link}', [LevelTransferController::class, 'loadRemoteLevelsForTransferIn'])
                        ->middleware('throttle:gdcs_remote')
                        ->name('levels.load');

                    Route::post('/', [LevelTransferController::class, 'transferInFromRemote'])
                        ->middleware('throttle:gdcs_remote')
                        ->name('api');
                });

                Route::group([
                    'prefix' => 'out',
                    'as' => 'out.'
                ], static function () {
                    Route::get('/{level}', [LevelTransferController::class, 'loadLinksForTransferOut'])
                        ->name('links.load');

                    Route::post('/', [LevelTransferController::class, 'transferOutToRemote'])
                        ->middleware('throttle:gdcs_remote')
                        ->name('api');
                });
            });
        });
    });
});

Route::group([
    'domain' => 'dl.geometrydashchinese.com',
    'as' => 'gdproxy.'
], static function () {
    Route::get('/', [GDProxy_HomePresenter::class, 'render'])->name('home');
});

Route::group([
    'domain' => 'ng.geometrydashchinese.com',
    'as' => 'ngproxy.'
], static function () {
    Route::get('/{id?}', [NGProxy_HomePresenter::class, 'render'])
        ->where('id', '\d+')
        ->name('home');
});

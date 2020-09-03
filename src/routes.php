<?php

    Route::post(
        config('tenantmagic.route.prefix') . config('tenantmagic.route.name', 'tenantmagic'),
        'Cidekar\Tenantmagic\Http\Controllers\TenantmagicController@store')->name('tenantmagic')->middleware('throttle');

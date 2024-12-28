<?php

namespace App\Http\Controllers;

use App\Http\Requests\CacheControllerRequests\CacheClearRequest;
use App\Http\Requests\CacheControllerRequests\CacheIndexRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    protected $cache_keys = ['EARTH_REGIONS','CURRENCIES','APPLICATION_SETTING'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('team:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(CacheIndexRequest $request)
    {
        // List of cache keys
        $cache_keys = $this->cache_keys;

        // Get cache value if it exists by keys
        $cache_memory = Cache::has($cache_keys) ? Cache::get($cache_keys) : null;

        // Return success
        if ($cache_memory) {
            return $this->success($cache_memory);
        }

        // Return failure
        return $this->noContent('No app defined data in cache');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear(CacheClearRequest $request)
    {
        // List of cache keys
        $cache_keys = $this->cache_keys;

        // Map through user submitted keys making sure it is only keys contained in cache_keys list that are cleared
        $cleared_list = collect($request->input('clear_list'))->filter(function ($item) use ($cache_keys) {
            if (in_array($item, $cache_keys)) {
                if (Cache::has($item)) {
                    return Cache::forget($item);
                }
            }
        });

        if ($cleared_list->count() === collect($request->input('clear_list'))->count()) {
            // Return success
            return $this->actionSuccess();
        }

        if ($cleared_list->count() > 0 && $cleared_list->count() < collect($request->input('clear_list'))->count()) {
            // Return success
            return $this->actionSuccess('Not all given keys were cleared');
        }

        // Return failure
        return $this->requestConflict('Make sure the targeted keys are app defined');
    }

    /**
     * Validate existence of resource pool.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        $test = DB::table('cache')->first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}

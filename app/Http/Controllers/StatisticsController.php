<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Broadcast;
use App\Models\Contact;
use App\Models\EmbeddedForm;
use App\Models\Group;
use App\Models\User;
use App\Http\Requests\StatisticsControllerRequests\StatisticsGeneralRequest;
use App\Http\Requests\StatisticsControllerRequests\StatisticsUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('team:api')->except('user');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function general(StatisticsGeneralRequest $request)
    {
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));

        // Fetch required data
        $broadcast = Broadcast::when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        $contact = Contact::when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        $embedded_form = EmbeddedForm::when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        $group = Group::when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        $user = User::when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        $account_payment_plan = DB::table('account_payment_plan')->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        // Perform statistics calculation
        $stats = new \stdClass();

        $stats->broadcasts = $broadcast->count();
        $stats->contacts = $contact->count();
        $stats->embedded_forms = $embedded_form->count();
        $stats->groups = $group->count();
        $stats->users = $user->count();
        $stats->account_payment_plans = $account_payment_plan->count();

        // Return success
        return $this->success($stats);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(StatisticsUserRequest $request)
    {
        $user_id =is_null($request->input('user_id'))? auth()->user()->id : $request->input('user_id');
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));

        // Build search query
        $user = User::with([
            'broadcasts' => function ($query) use ($start_date, $end_date) {
 
            $query->when($start_date, function ($query, $start_date) {
                return $query->whereDate('created_at', '>=', $start_date);

            })->when($end_date, function ($query, $end_date) {
                return $query->whereDate('created_at', '<=', $end_date);

            });

        },
            'contacts' => function ($query) use ($start_date, $end_date) {
 
            $query->when($start_date, function ($query, $start_date) {
                return $query->whereDate('created_at', '>=', $start_date);

            })->when($end_date, function ($query, $end_date) {
                return $query->whereDate('created_at', '<=', $end_date);

            });

        },
        
            'groups' => function ($query) use ($start_date, $end_date) {
    
            $query->when($start_date, function ($query, $start_date) {
                return $query->whereDate('created_at', '>=', $start_date);

            })->when($end_date, function ($query, $end_date) {
                return $query->whereDate('created_at', '<=', $end_date);

            });

        },

            'embeddedForms' => function ($query) use ($start_date, $end_date) {
 
            $query->when($start_date, function ($query, $start_date) {
                return $query->whereDate('created_at', '>=', $start_date);

            })->when($end_date, function ($query, $end_date) {
                return $query->whereDate('created_at', '<=', $end_date);

            });

        }])->where('id', $user_id)->orderBy('created_at', 'asc')->first();


        // Map through data and perform statistics calculation
        $stats = collect($user->getRelations())->map(function ($relation, $name) {

            $statistics = (object)[];

            $statistics->total = $relation->count();

            return $statistics;
        });

        // Merge calculated results with rest of data
        $user = array_merge($user->withoutRelations()->toArray(), ['stats' => $stats]);

        // Return success
        return $this->success($user);
    }
}

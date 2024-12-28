<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Contact;
use App\Models\User;
use App\Http\Requests\ContactControllerRequests\ContactDestroyRequest;
use App\Http\Requests\ContactControllerRequests\ContactFilterRequest;
use App\Http\Requests\ContactControllerRequests\ContactIndexRequest;
use App\Http\Requests\ContactControllerRequests\ContactMeRequest;
use App\Http\Requests\ContactControllerRequests\ContactSearchRequest;
use App\Http\Requests\ContactControllerRequests\ContactShowRequest;
use App\Http\Requests\ContactControllerRequests\ContactStoreRequest;
use App\Http\Requests\ContactControllerRequests\ContactUpdateRequest;
use App\Http\Requests\ContactControllerRequests\SocialiteGoogleCallBackRequest;
use App\Jobs\ProcessContactImportForGoogle;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\PeopleService;
use Socialite;

class ContactController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('redirectToGoogleProvider','handleGoogleProviderCallback');
        // $this->middleware('team:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ContactIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all contacts with all their relations
            $contacts = Contact::with([
                'user',
            ])->when($request->input('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->input('user_id'));

            })->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted contacts with all their relations
            $contacts = Contact::onlyTrashed()->with([
                'user',
            ])->when($request->input('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->input('user_id'));

            })->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all contacts with out their relations
            $contacts = Contact::when($request->input('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->input('user_id'));

            })->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($contacts) {
            
            if (count($contacts) > 0) {
                return $this->success($contacts);
            } else {
               return $this->noContent('No contact was found');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterIndex(ContactFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $title = is_null($request->input('title'))? false : Helper::escapeForLikeColumnQuery($request->input('title'));
        $first_name = is_null($request->input('first_name'))? false : Helper::escapeForLikeColumnQuery($request->input('first_name'));
        $last_name = is_null($request->input('last_name'))? false : Helper::escapeForLikeColumnQuery($request->input('last_name'));
        $email = is_null($request->input('email'))? false : Helper::escapeForLikeColumnQuery($request->input('email'));
        $phone = is_null($request->input('phone'))? false : Helper::escapeForLikeColumnQuery($request->input('phone'));
        $address = is_null($request->input('address'))? false : Helper::escapeForLikeColumnQuery($request->input('address'));
        $city = is_null($request->input('city'))? false : Helper::escapeForLikeColumnQuery($request->input('city'));
        $state = is_null($request->input('state'))? false : Helper::escapeForLikeColumnQuery($request->input('state'));
        $country = is_null($request->input('country'))? false : Helper::escapeForLikeColumnQuery($request->input('country'));
        $zip = is_null($request->input('zip'))? false : Helper::escapeForLikeColumnQuery($request->input('zip'));
        $latitude = is_null($request->input('latitude'))? false : Helper::escapeForLikeColumnQuery($request->input('latitude'));
        $longitude = is_null($request->input('longitude'))? false : Helper::escapeForLikeColumnQuery($request->input('longitude'));
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $contacts = Contact::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', $user_id);
        })->when($title, function ($query, $title) {
            return $query->where('title', 'like', '%'.$title.'%');
        })->when($first_name, function ($query, $first_name) {
            return $query->where('first_name', 'like', '%'.$first_name.'%');
        })->when($last_name, function ($query, $last_name) {
            return $query->where('last_name', 'like', '%'.$last_name.'%');
        })->when($email, function ($query, $email) {
            return $query->where('email', 'like', '%'.$email.'%');
        })->when($phone, function ($query, $phone) {
            return $query->where('phone', 'like', '%'.$phone.'%');
        })->when($address, function ($query, $address) {
            return $query->where('address', 'like', '%'.$address.'%');
        })->when($city, function ($query, $city) {
            return $query->where('city', 'like', '%'.$city.'%');
        })->when($state, function ($query, $state) {
            return $query->where('state', 'like', '%'.$state.'%');
        })->when($country, function ($query, $country) {
            return $query->where('country', 'like', '%'.$country.'%');
        })->when($zip, function ($query, $zip) {
            return $query->where('zip', 'like', '%'.$zip.'%');
        })->when($latitude, function ($query, $latitude) {
            return $query->where('latitude', 'like', '%'.$latitude.'%');
        })->when($longitude, function ($query, $longitude) {
            return $query->where('longitude', 'like', '%'.$longitude.'%');
        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        });

        // Check if the builder has any where clause
        if (count($contacts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $contacts = $contacts->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $contacts = $contacts->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $contacts = $contacts->take(1000)->get();
        }

        // Return success
        if ($contacts) {
            if (count($contacts) > 0) {
                return $this->success($contacts);
            } else {
               return $this->noContent('No contact was found for this range');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchIndex(ContactSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));

        // Build search query
        $contacts = Contact::when($search_string, function ($query) use ($request, $search_string) {

            return $query->when($request->input('user_id'), function($query) use ($request) {

                return $query->where('user_id', $request->input('user_id'));

            })->where(function ($query) use ($search_string) {

                return $query->where('user_id', 'like', '%'.$search_string.'%')
                ->orWhere('title', 'like', '%'.$search_string.'%')
                ->orWhere('first_name', 'like', '%'.$search_string.'%')
                ->orWhere('last_name', 'like', '%'.$search_string.'%')
                ->orWhere('email', 'like', '%'.$search_string.'%')
                ->orWhere('phone', 'like', '%'.$search_string.'%')
                ->orWhere('address', 'like', '%'.$search_string.'%')
                ->orWhere('city', 'like', '%'.$search_string.'%')
                ->orWhere('state', 'like', '%'.$search_string.'%')
                ->orWhere('country', 'like', '%'.$search_string.'%')
                ->orWhere('zip', 'like', '%'.$search_string.'%')
                ->orWhere('latitude', 'like', '%'.$search_string.'%')
                ->orWhere('longitude', 'like', '%'.$search_string.'%');
            });
        });

        // Check if the builder has any where clause
        if (count($contacts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $contacts = $contacts->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($contacts) {
            if (count($contacts) > 0) {
                return $this->success($contacts);
            } else {
               return $this->noContent('No contact was found for this range');
            }
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ContactStoreRequest $request)
    {
        // Check if user has an exceeded contact limit contact and validate user
        $contacts = Contact::where('user_id', auth()->user()->id)->count();
        if ($contacts > 999) {
            return $this->requestConflict('Contact limit exceeded, please delete or edit existing contacts');
        }

        // Fill the user contact model
        $contact = new Contact;
        $contact = $contact->fill($request->toArray());

        // Additional params
        $contact->user_id = auth()->user()->id;

        // Return success
        if ($contact->save()) {
            return $this->entityCreated($contact,'User contact was saved');
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ContactShowRequest $request)
    {
        // Use contact model passed in from request authorization
        $contact = $request->contact;

        // Return success
        if ($contact) {

            if ($request->input('properties')) {
                $contact = $contact->load('user');
            }

            return $this->success($contact);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Display the authenticated user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(ContactMeRequest $request)
    {
        // Get all user contact
        $contacts = Contact::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($contacts) {

            if ($request->input('properties')) {
                $contacts = $contacts->load('user');
            }

            return $this->success($contacts);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ContactUpdateRequest $request)
    {
        // Use contact model passed in from request authorization
        $contact = $request->contact;

        if ($contact) {

            // Fill requestor input
            $contact->fill($request->toArray());

            // Update user contact
            if ($contact->update()) {
                return $this->actionSuccess('User contact was updated');
            } else {
                return $this->unavailableService();
            }
        } else {
            // Return failure
            return $this->notFound();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ContactDestroyRequest $request)
    {
        // Use contact model passed in from request authorization
        $contact = $request->contact;

        if ($contact) {

            // Delete user contact
            if ($contact->delete()) {
                return $this->actionSuccess('User contact was deleted');
            } else {
                return $this->unavailableService();
            }
        } else {
            // Return failure
            return $this->notFound();
        }
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogleProvider()
    {
        return Socialite::driver(config('constants.socialite.google'))->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleProviderCallback(SocialiteGoogleCallBackRequest $request)
    {
        try {

            if ($request->input('code')) {
                $user = Socialite::driver(config('constants.socialite.google'))->stateless()->user();
            }

            if ($request->input('token')) {
                $user = Socialite::driver(config('constants.socialite.google'))->userFromToken($request->input('token'));
            }

            // Find user
            $registered_user = auth()->user()? User::where('email',auth()->user()->id)->first() : User::where('email',$user->getEmail())->first();

            // Use google client to access contacts
            $client = new Client();
            $client->setAccessToken($user->token);
            $peopleService = new PeopleService($client);
            $connections = $peopleService->people_connections->listPeopleConnections('people/me', ['personFields' => 'names,emailAddresses,phoneNumbers']);
            $contacts = $connections->getConnections();

            // dispatch
            ProcessContactImportForGoogle::dispatchIf($registered_user, $registered_user, $contacts);

            // Return success
            return $this->actionSuccess('User contact was retrieved');

        } catch (\Throwable $th) {
            return $this->unavailableService($th->getMessage());
        }
    }

    /**
     * Validate existence of resource pool.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        $test = Contact::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}

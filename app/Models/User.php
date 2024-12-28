<?php

namespace App\Models;

use App\Casts\BaseUrlCast;
use App\Casts\GenderCast;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use Notifiable;
    use SoftDeletes;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
      return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'phone', 'gender', 'description', 'birth_date', 'birth_year'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'deleted_at', 'deleted_by'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date:m-d',
        'birth_year' => 'string',
        'blocked' => 'boolean',
        'providers_allowed' => 'array',
        'providers_disallowed' => 'array',
        'providers_details' => 'array',
        'groups' => 'array',
        'user_details_verified' => 'boolean',
        'picture' => BaseUrlCast::class,
        'gender' => GenderCast::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->created_by = auth()->user()? auth()->user()->id : null;

            /**
             * This is because some mySql version do not allow default on json columns,
             * Hence these json columns were set to null on migration.
             * Then on creation are set to a default of '[]' if no value was given.
             */
            $model->providers_allowed = $model->providers_allowed ?? [];
            $model->providers_disallowed = $model->providers_disallowed ?? [];
            $model->providers_details = $model->providers_details ?? [];
            $model->groups = $model->groups ?? [];
        });

        self::updating(function ($model) {
            $model->updated_by = auth()->user()? auth()->user()->id : null;
        });

        self::deleting(function ($model) {
            $model->deleted_by = auth()->user()? auth()->user()->id : null;
            $model->save();
        });
    }

    /**
     * Assigns a role to a user using existing relationship with roles
     * 
     * @param array:App\Models\Role|array:string $role 
     * @param App\Models\Team|string|null $team
     * @return bool
     */
    public function assignRole($roles, $team = null)
    {
        return $this->attachRoles($roles, $team);
    }

    /**
     * Retracts a role from a user using existing relationship with roles
     * 
     * @param array:App\Models\Role|array:string $role 
     * @param App\Models\Team|string|null $team
     * @return bool
     */
    public function retractRole($roles, $team = null)
    {
        return $this->detachRoles($roles, $team);
    }

    /**
     * Establishes if user is associated to given team
     * 
     * @param string $team_name
     * @return bool
     */
    public function belongsToTeam($team_name)
    {
        return $this->rolesTeams()->where('name',$team_name)->first();
    }

    /**
     * Establishes a has many through relationship with roles table through role_user table
     * @param void|bool $grouped
     * @return Illuminate\Support\Collection
     */
    public function teamsRoles($grouped = true)
    {
        $roles = Role::all();
        return $this->rolesTeams()->get()->map(function ($item) use ($roles) {
            return collect($item)->merge(['role'=>$roles->firstWhere('id',$item->pivot->role_id)]);
        })
        ->when($grouped, function ($query) {
            return $query->groupBy('name')->map(function ($item) {
                return [
                    'id' => $item->first()->get('id'),
                    'name' => $item->first()->get('name'),
                    'display_name' => $item->first()->get('display_name'),
                    'roles' => $item->pluck('role.name')
                ];
            });
        });
    }

    /**
     * Retrieve user team through group
     * 
     * @param string $team_id
     * @param string|null $accessor
     * @return array|string|null
     */
    public function group($team_id, $accessor=null)
    {
        if (isset($this->groups[$team_id])) {

            if ($accessor) {
                return $this->groups[$team_id][$accessor] ?? null;
            }

            return $this->groups[$team_id];
        }

        return null;
    }

    /**
     * Establishes a one to many relationship with accounts table
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Establishes a one to many relationship with browsers table
     */
    public function browsers()
    {
        return $this->hasMany(Browser::class);
    }

    /**
     * Establishes a one to many relationship with broadcasts table
     */
    public function broadcasts()
    {
        return $this->hasMany(Broadcast::class);
    }

    /**
     * Establishes a one to many relationship with contacts table
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Establishes a one to many relationship with groups table
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Establishes a one to many relationship with embedded forms table
     */
    public function embeddedForms()
    {
        return $this->hasMany(EmbeddedForm::class);
    }
}

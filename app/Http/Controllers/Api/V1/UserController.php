<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Policies\V1\UserPolicy;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\V1\UserResource;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends ApiController
{
    protected $policyClass = UserPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // plolicy
        if ($this->isAble('store', User::class)) {


            return new UserResource(User::create($request->input('data.attributes')));
        }
        return $this->error('You are not authorized to create a user', 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            // policy
            if ($this->isAble('update', $user)) {


                $user->update($request->mappedAttributes());

                return new UserResource($user);
            }
            return $this->error('You are not authorized to update this user', 403);
        } catch (ModelNotFoundException $ex) {
            return $this->error('User not found', 404);
        }
    }

    public function replace(ReplaceUserRequest $request, $user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            // policy
            if ($this->isAble('update', $user)) {

                $user->update($request->mappedAttributes());

                return new UserResource($user);
            }
            return $this->error('You are not authorized to update this user', 403);
        } catch (ModelNotFoundException $ex) {
            return $this->error('User not found', 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);
            // policy
            if ($this->isAble('delete', $user)) {
                $user->delete();

                return $this->success('User deleted successfully');
            }
            return $this->error('You are not authorized to delete this user', 403);
        } catch (ModelNotFoundException $ex) {
            return $this->error('User not found', 404);
        }
    }
}

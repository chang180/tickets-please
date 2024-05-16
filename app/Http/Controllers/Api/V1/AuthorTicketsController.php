<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Support\Facades\Auth;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\V1\TicketResource;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorTicketsController extends ApiController
{

    protected $policyClass = TicketPolicy::class;

    public function index($author_id, TicketFilter $filter)
    {
        return TicketResource::collection(
            Ticket::where('user_id', $author_id)->filter($filter)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request, $author_id)
    {
        //plolicy
        if ($this->isAble('store', Ticket::class)) {

            return new TicketResource(
                Ticket::create($request->mappedAttributes(['author' => 'user_id']))
            );
        }
        return $this->error('You are not authorized to create a ticket.', 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            // policy
            if ($this->isAble('delete', $ticket)) {
                $ticket->delete();
                return $this->ok('Ticket deleted');
            }
            return $this->error('You are not authorized to delete a ticket.', 401);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket not found.', 404);
        }
    }

    public function replace(ReplaceTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            // policy
            if ($this->isAble('replace', $ticket)) {
                $ticket->update($request->mappedAttributes());
                return new TicketResource($ticket);
            }
            return $this->error('You are not authorized to update a ticket.', 401);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket not found.', 404);
        }
    }

    public function update(UpdateTicketRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            // policy
            if ($this->isAble('update', $ticket)) {
                $ticket->update($request->mappedAttributes());
                return new TicketResource($ticket);
            }
            return $this->error('You are not authorized to update a ticket.', 401);
        } catch (ModelNotFoundException) {
            return $this->error('Ticket not found.', 404);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\EventResource;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use App\Services\UserInvitationService;
use App\Event;
use App\User;
use App\Invitation;
use App\Notifications\CanceledEvent;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{

    protected $repository;

    public function __construct(Event $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   

        $filter = $request->query("filter");

        if($filter == "organizando") {
            return $this->repository->where(function ($query) use ($filter) {
                if($filter == "organizando") {
                    return $query->where("user_id", auth()->id())->where("active", 1);
                }
            })->orderBy("id", "desc")
            ->paginate();
        }

        if($filter == "vou_participar") {
            return DB::table("events")
                ->join("invitations", "events.id", "=", "invitations.event_id")
                ->where("events.active", 1)
                ->join("users", "invitations.user_id", "=", "users.id")
                ->where("invitations.user_id", "=", auth()->id())
                ->where("invitations.status", "=", 1)
                ->select("events.*")
                ->orderBy("id", "desc")
                ->paginate();
        }

        if($filter == "convites") {
            return DB::table("events")
                ->join("invitations", "events.id", "=", "invitations.event_id")
                ->where("events.active", 1)
                ->where("invitations.status", "=", 0)
                ->join("users", "invitations.user_id", "=", "users.id")->where("invitations.user_id", "=", auth()->id())
                ->select("events.*")
                ->orderBy("id", "desc")
                ->paginate();
        }

        return $events;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {    
        $validated = $request->validated();
        $data = $request->only(["title", "description", "started", "description", "localization"]);

        if($request->hasFile("cover")) {
            $path = $request->file("cover")->store("images/events");
        }

        $event = new Event();
        $event->user_id = auth()->id();
        $event->title = $data["title"];
        $event->description = $data["description"];
        $event->started = \Carbon\Carbon::parse($data["started"])->format("Y-m-d H:i:s");
        $event->localization = $data["localization"];
        if(isset($path)) {
            $event->cover = $path;
        }
        $event->save();

        return $event;
                
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {   
        if($event = UserInvitationService::CheckIfUserCanViewEvent($id)) {
            $event->totalConfirmations = Invitation::where("event_id", $id)->where("status", 1)->count();
            $author = $event->user;
            $myInvite = auth()->user()->invitations->where("event_id", $event->id)->first();

            //dd($myInvite);
            if($request->ajax()) {
                return $event;
            }

            $data = [
                "event" => $event,
                "author" => $author,
                "invitation" => $myInvite
            ];

            return view("events.show", $data);
        }

        if($request->ajax()) {
            abort(404);
            exit;
        }

        return view("events.expired");


    }
    /**
     * Get participants of an event
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getParticipants(Request $request, $id) 
    {
        $event = $this->repository->find($id);
        if($event) {
            $participants = DB::table("events")
                    ->join("invitations", function($join) {
                        $join->on("events.id", "invitations.event_id")
                            ->where("status", 1);
                    })->where("events.id", $id)
                    ->join("users", function ($join) {
                        $join->on("users.id", "invitations.user_id");
                    })
                    ->select("users.name")
                    ->get();
            return $participants;
        }

        abort(404);
    }   

    /**
     * Cancel an event
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        $event = $this->repository->find($id);
        if(!$event) {
            abort(404);
        }
        $participantsArray = [];
        $invitations = $event->invitations->where("status", 1);
        foreach($invitations as $invite) {
            array_push($participantsArray, $invite->user);
        }

        $author = $event->user;
        if(Gate::allows("update-event", $event)) {
            $event->active = 0;
            $event->save();

            if(count($participantsArray) > 0) {
                $participants = collect($participantsArray)->all();
                Notification::send($participants, new CanceledEvent($event));
            }


            return $event;
        }

        return response()->json(["message" => "Not authorized!"], 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, $id)
    {   
        $validated = $request->validated();     
        $event = $this->repository->find($id);

        if($event) {            
            if(Gate::allows("update-event", $event)) {
                if($request->hasFile("cover")) {
                    $coverOld = $event->cover;
                    if($coverOld) {
                        Storage::delete($coverOld);
                    }

                    $path = $request->file("cover")->store("images/events");
                }

                $event->title = $request->title;
                $event->description = $request->description;
                $event->localization = $request->localization;
                $event->started = \Carbon\Carbon::parse($request->started)->format("Y-m-d H:i:s");
                if(isset($path)) {
                    $event->cover = $path;
                }
                $event->save();
                
                return $event;
            }
        }


        return response()->json(["message" => "Not authorized"]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

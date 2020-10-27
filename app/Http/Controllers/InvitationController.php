<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\InvitationRequest;
use App\Services\UserInvitationService;
use App\Invitation;
use App\User;
use App\Events\CreatedInvitation;
use App\Notifications\SendInvitation;
use App\Notifications\InvitationAccepted;

class InvitationController extends Controller
{  
    public function createOld(InvitationRequest $request)
    {
        $validated = $request->validated();
        $data = [
            "user_id" => $request->user_id,
            "event_id" => $request->event_id,
        ];

        $user = User::find($data["user_id"]);
        if(!$user) abort(404);

        // Se o usuário já estiver sido convidado, não cria.
        if(UserInvitationService::CheckIfUserAlreadyInvited($data)) {
            return ["message" => "Já foi convidado"];
        }

        // Se o usuário já cancelou o evento, o convite terá o status como pendente novamente
        if($invite = UserInvitationService::CheckIfUserCancelParticipation($data)) {
            $invite->status = 0;
            $invite->save();
            return;
        }

        $invite = Invitation::create($data);
        return $invite;
    }

    public function create(InvitationRequest $request) 
    {
        $validated = $request->validated();
        $data = [
            "user_id" => $request->user_id,
            "event_id" => $request->event_id,
        ];

        $user = User::find($data["user_id"]);
        if(!$user) {
            abort(404);
        }

        // Se o usuário já estiver sido convidado, não cria.
        if(UserInvitationService::CheckIfUserAlreadyInvited($data)) {
            return ["message" => "Já foi convidado"];
        }

        $invite = Invitation::create($data);
        $user->notify(new SendInvitation($invite->event));
        return $invite;
    }

    private function validUserInvitation(Request $request) {
        $validate = $request->validate([
            "event_id" => ["required"]
        ]);

        $data = [
            "user_id" => auth()->id(),
            "event_id" => $request->event_id,
        ];

        if(UserInvitationService::CheckIfUserAlreadyInvited($data)) {
            $invite = Invitation::where("user_id", "=", $data["user_id"])->where("event_id", "=", $data["event_id"])->first();
            if($invite) {
                return $invite;
            }

            return false;
        }

        return false;
    }

    /**
     * Confirms participation in an event
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        if($invite = $this->validUserInvitation($request)) {
            $invite->status = 1;
            $invite->save();
            $author = $invite->event->user;
            $author->notify(new InvitationAccepted($invite->event, auth()->user()));
            return ["message" => "Convite aceito!"];
        }

        return response()->json(['message' => 'Not authorized.'],403);
    }

    public function cancelParticipation(Request $request) {
        if($invite = $this->validUserInvitation($request)) {
            $invite->status = 2;
            $invite->save();

            return ["message" => "Participação cancelada!"];
        }

        return response()->json(['message' => 'Not authorized.'],403);
    }
}
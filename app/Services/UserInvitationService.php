<?php 

namespace App\Services;
use App\Invitation;

class UserInvitationService
{   
    // Verifica se o usuário já foi convidado
    public static function checkIfUserAlreadyInvited($data)
    {   
        $count = Invitation::where("event_id", "=", $data["event_id"])
                ->where("user_id", "=", $data["user_id"])
                ->where("status", "!=", "2")->count();

        return $count;
    }

    // Verifica se o usuário possui convite sobre determinado evento
    public static function CheckIfUserHasInvitation($data) 
    {
        $count = Invitation::where("event_id", "=", $data["event_id"])
                ->where("user_id", "=", $data["user_id"])
                ->where("status", "=", 0)->count();

        return $count;
    }

    // Verifica se o usuário pode visualizar determinado evento
    public static function checkIfUserCanViewEvent($event_id)
    {
        $event = \App\Event::find($event_id);
        $invite = \App\Invitation::where("user_id", auth()->id())
                ->where("event_id", $event_id)
                ->where("status", "!=", "2")
                ->first();

        if($event) {
            return $event->user_id == auth()->id() || $invite ? $event : false;
        } 

        return false;
    }

    // Verifica se o usuário confirmou a participação em determinado evento
    public static function checkIfUserConfirmParticipation($data)
    {   
        $count = Invitation::where("event_id", "=", $data["event_id"])
                ->where("user_id", "=", $data["user_id"])
                ->where("status", "=", 1)->count();

        return $count;
    }

    // Verifica se o usuário cancelou a participação em um evento
    public static function CheckIfUserCancelParticipation($data)
    {   
        $invite = Invitation::where("event_id", "=", $data["event_id"])
                ->where("user_id", "=", $data["user_id"])
                ->where("status", "=", 2)->first();

        if($invite) {
            return $invite; 
        }
        
        return false;
    }
}
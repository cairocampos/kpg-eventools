@extends("layouts.app")

@section("content")
    <section class="event">
        <div class="container">
            <div class="row mb-2">
                <div class="col-4">
                    <div class="event__cover">
                        <img src="{{asset($event->cover ? 'storage/'.$event->cover : 'images/default.jpg')}}" width="100%">
                    </div>
                </div>
                <div class="col-8 d-flex flex-column">
                    <div>
                        <span class="badge badge-secondary">{{ $event->totalConfirmations }} Pessoa(s) confirmada(s)</span>
                    </div>
                    <h1 class="event__title text-color__default">
                        {{$event->title}}
                    </h1>
                    <div class="w-100">
                        @if(auth()->id() == $event->user_id)
                            @if($event->active)
                                <button class="btn btn-outline-primary" v-on:click="cancelEvent({{$event->id}})"><i class="fas fa-times"></i> Cancelar Evento</button>
                            @endif
                        @endif 

                        @if(!$event->active)
                            <button class="btn text-danger"><i class="fas fa-times"></i> Evento Cancelado</button>
                        @else
                            <button class="btn btn-info" v-on:click="showModalInvitesWithEvent({{$event->id}})"><i class="fa fa-envelope"></i> Convidar</button>
                            <button class="btn btn-outline-secondary" v-on:click="showParticipants = !showParticipants">
                                <i class="fas fa-eye"></i> Pessoas que confirmaram participação
                            </button>
                            @if(!empty($invitation))
                                @if($invitation->status == 0)
                                    <button class="btn btn-primary" v-on:click="confirmEvent({{$event->id}})">Participar</button>
                                @else 
                                    <button class="btn btn-outline-primary" v-on:click="cancelParticipation({{$event->id}})"><i class="fas fa-times"></i> Cancelar Participação</button>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="card events__details p-3 rounded">
                        <h5 class="text-color__default font-weight-bold">Organizador do Evento</h5>
                        <ul class="text-color__default m-0 p-0" style="list-style: none;">
                            <li>{{$author->name}}</li>
                            <li><strong>{{$author->email}}</strong></li>
                        </ul>
                        <hr>
                        <h5 class="text-color__default font-weight-bold">Detalhes</h5>
                        <ul class="event__details-list">
                            <li><i class="fas fa-users"></i> {{$event->totalConfirmations}} Pessoa(s) confirmada(s)</li>
                            <li><i class="fas fa-clock"></i> {{date('d-m-Y \à\s H:i', strtotime($event->started))}}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-color__default font-weight-bold">Descrição do evento</h5>
                            <p class="text-color__default text-description__event">{{$event->description}}</p>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <template v-if="showParticipants">
        <participants-component />
    </template>

    <div v-if="showModalInvites">
        <send-invitation-component :event="event" :event.sync="event" v-on:close="showModalInvites = false" />
    </div>
@endsection
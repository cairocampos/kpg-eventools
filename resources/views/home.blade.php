@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card card__events">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-tab" :class="{active:currentTab == 'organizando'}" v-on:click="setTab('organizando')">Organizando</button>
                        <button class="btn btn-tab mx-3" :class="{active:currentTab == 'vou_participar'}" v-on:click="setTab('vou_participar')">Vou Participar</button>
                        <button class="btn btn-tab" :class="{active:currentTab == 'convites', new:newInvites}" v-on:click="setTab('convites')">Convites</button>
                    </div>
                    <div>
                        <b-button v-b-modal.modal-create-event class="rounded-circle"><i class="fas fa-plus"></i></b-button>
                    </div>
                </div>

                <div class="card-body card__events-container">
                    <events-component :filter="currentTab" v-on:invites="openModalInvites" />
                </div>

                <div class="card-footer">
                    <b-button v-b-modal.modal-create-event class="btn btn-lg btn-secondary btn-block">
                        <i class="fas fa-plus"></i> Criar novo evento
                    </b-button>
                </div>
            </div>
        </div>
    </div>   

    <div v-if="showModalInvites">
        <send-invitation-component :event="event" :event.sync="event" v-on:close="showModalInvites = false" />
    </div>    

    {{-- <create-component v-on:created="eventCreated" /> --}}
    <create-component />
</div>
@endsection

<template>
    <div>
        <ul class="list-unstyled">
            <div class="d-flex justify-content-center" v-if="preloader">
                <div class="spinner-border text-center" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <p v-if="!Object.values(events.data).length">Nada por aqui...</p>
            <template v-else>
                <li v-for="(event, index) in events.data" class="media p-3 rounded mb-4" :key="event.id">
                    <img v-if="event.cover" :src="`storage/${event.cover}`" class="mr-3" alt="..." width="150">
                    <img v-else src="images/default.jpg" class="mr-3" alt="..." width="150">
                    <div class="media-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mt-0 mb-1">{{event.title}}</h5>
                            <div class="btn-group">
                                <button v-if="filter == 'organizando'" class="btn btn-sm btn-secondary" @click="showEvent(event)"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-secondary" @click="redirectToEvent(event.id)"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <hr>
                        <p class="event__description">{{event.description}}</p>
                        <ul class="mb-3">
                            <li>
                                <strong><i class="fas fa-calendar-alt"></i></strong>
                                {{event.started | transformDate}}
                            </li>
                            <li>
                                <strong><i class="fas fa-map-marker-alt"></i></strong> {{event.localization}}
                            </li>
                        </ul>
                        <div class="d-flex" role="group" aria-label="Basic example">
                            <button v-if="filter == 'convites'" type="button" class="btn btn-primary mr-3" @click="confirmEvent(event.id)"><i class="fas fa-check"></i> Participar</button>
                            <div v-if="event.active">
                                <button v-if="filter == 'organizando' " type="button" class="btn btn-outline-primary" @click="cancelEvent(event.id, index)"><i class="fas fa-times"></i> Cancelar Evento</button>
                                <button v-if="filter == 'vou_participar'" class="btn btn-outline-primary" @click="cancelParticipation(event.id)"><i class="fas fa-times"></i> Cancelar Participação</button>
                                <button type="button" class="btn btn-info" @click="triggerModalInvites(event)"><i class="fas fa-envelope"></i> Convidar</button>
                            </div>
                            <button class="btn text-danger" disabled v-else><i class="fas fa-times"></i> Evento Cancelado</button>
                        </div>
                    </div>
                </li>
                <div v-if="!hasNextPage" v-observe-visibility="visibilityChanged">...</div>
            </template>
        </ul>

        <b-modal id="modal-1" title="Informações do evento" @hide="reset" size="lg">
            <FormComponent>
                <template #imageInput>
                    <div class="form-group">
                        <label for="">Imagem de Capa</label>
                        <div class="image__view" @click="$refs.fileInput.click()">
                            <img v-if="$store.state.event.cover" ref="image" :src="`storage/${$store.state.event.cover}`" alt="Cover" width="200">
                            <img v-else ref="image" src="images/default.jpg" alt="Cover" width="200">
                        </div>
                        <input type="file" ref="fileInput" class="form-control d-none" @change="addImage" />
                    </div>
                </template>
            </FormComponent>
            <template #modal-footer>
                <button v-if="preloader" class="btn btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Atualizando...
                </button>
                <button v-else class="btn btn-primary" @click="updateEvent">Atualizar</button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";
import FormComponent from "./FormComponent";
import toastify from "../../mixins/toastify-mixin";
import validFormDataEvent from "../../mixins/valid-formdata-event-mixin";
import EventBus from "../event-bus";
import paginate from "../../mixins/paginate";

export default {
    mixins:[toastify, validFormDataEvent, paginate],
    props: ["filter"],
    components: {
        FormComponent
    },
    data() {
        return {
            preloader:false,
            events: {
                data:[],
            },
        }
    },
    watch: {
        filter(search) {
            this.events.data = [];
            this.fetchEvents(search);
        },
        page() {
            this.fetchEvents(this.search);
        }
    },
    methods: {
        triggerModalInvites(event) {
            this.$emit('invites', event)
        },
        fetchEvents(search = 'organizando') {
            this.preloader = true;
            axios.get(`/events?filter=${search}&page=${this.page}`)
            .then(response => {
                const {data} = response;
                if(data.data.length) {
                    this.events.data.push(...data.data);
                }
                
                let {current_page, last_page} = data;
                this.meta = {current_page, last_page};

            })
            .catch()
            .finally(() => this.preloader = false);
        },
        showEvent(event) {
            this.$store.commit("UPDATE_EVENT", event);
            this.$bvModal.show("modal-1");
        },
        redirectToEvent(event_id) {
            window.location.href = `/events/${event_id}`;
        },
        updateEvent(index) {
            this.preloader = true;
            axios.post(`/events/${this.$store.state.event.id}?_method=PUT`, this.formatData)
            .then(response => {
                if(response.data.id) {
                    this.success("Evento atualizado com sucesso!");
                    this.events.data.find((item, index) => {
                        if(item.id == response.data.id) {
                            this.$set(this.events.data, index, response.data);
                            return;
                        }
                    });
                }
            }).catch(error => {
                this.error(error.response.data.message);
            })
            .finally(() => this.preloader = false);
        },
        confirmEvent(event_id) {
            axios.post("/invitations/confirm", {event_id})
            .then(response => {
                this.events.data = this.events.data.filter(event => event.id != event_id);
                this.success(response.data.message);
            })
            .catch(error => {
                this.error(error.response.data.message);
            });
        },
        cancelParticipation(event_id) {
            axios.post("/invitations/cancel", {event_id})
            .then(response => {
                this.events.data = this.events.data.filter(event => event.id != event_id);
                this.success(response.data.message);
            })
            .catch(error => {
                this.error(error.response.data.message);
            });
        },
        cancelEvent(event_id, index) {
            axios.post(`/events/${event_id}/cancel`)
            .then(response => {
                if(response.data.id) {
                    this.$set(this.events.data, index, response.data);
                }
                this.success("Evento cancelado!");
            })
            .catch(error => {
                this.error(error.response);
            });
        },
        reset() {
            this.$store.dispatch("resetEvent");
        }
    },
    created() {
        this.fetchEvents();
        const vm = this;
        EventBus.$on("EVENT_CREATED", function(event) {
            console.log(event);
            event.active = 1;
            vm.events.data.unshift(event);
        });
    }
}
</script>

<style scoped>
.media {
    box-shadow: 0 1px 4px rgba(0,0,0,0.5);
}

.media-body h5 {
    font-weight: bold;
    color: #263b5e;
}

.media-body p {
    color: #263b5e;
}

.media-body ul {
    margin:0;
    padding:0;
    list-style: none;
}

.media-body ul li {
    color: #263b5e;
    cursor: pointer;
}

.event__description {
    max-height: 100px;
    line-height: 20px;
    word-break: break-all;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -moz-box-orient: vertical;
    -ms-box-orient: vertical;
    box-orient: vertical;
    -webkit-line-clamp: 5;
    -moz-line-clamp: 5;
    -ms-line-clamp: 5;
    line-clamp: 5;
    overflow: hidden;
    white-space: pre-line;
}
</style>
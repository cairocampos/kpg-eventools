
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//window.Vue = require('vue');

import Vue from "vue";
import 'bootstrap-vue/dist/bootstrap-vue.css';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import {BootstrapVue} from "bootstrap-vue";
import VueToastify from "vue-toastify";

//window.Vue.use(BootstrapVue);
//window.Vue.use();
Vue.use(BootstrapVue);
Vue.use(VueToastify);
Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('events-component', require("./components/events/EventsComponent.vue"));
Vue.component('create-component', require("./components/events/CreateComponent.vue"));
Vue.component('send-invitation-component', require("./components/events/SendInvitationComponent.vue"));
Vue.component("participants-component", require("./components/events/ParticipantsComponent.vue"));
Vue.component("notifications-component", require("./components/NotificationsComponent.vue"));
require("./plugins");

Vue.filter('transformDate', function(value) {
    let newDate = new Date(value);
    return newDate.toLocaleDateString("pt-BR", {day:"numeric", month:"long", year:"numeric", hour:"numeric", minute:"numeric"});
});

import store from "./store";
import axios from "axios";
import toastify from "./mixins/toastify-mixin";
import EventBus from "./components/event-bus";
const app = new Vue({
    el: '#app',
    mixins:[toastify],
    store,
    data() {
        return {
            tabs:["organizando", "vou_participar", "convites"],
            currentTab: "organizando",
            event: {},
            showModalInvites: false,
            showParticipants:false,
            showNotifications:false,
            newInvites:false,
            hasNotifications:false
        }
    },
    methods: {
        /*eventCreated(event) {
            this.event = event;
            this.showModalInvites = true;
        },*/
        setTab(tab_name) {
            if(this.tabs.includes(tab_name)) {
                this.currentTab = tab_name;
            }
        },
        openModalInvites(event) {
            this.event = event;
            this.showModalInvites = true;
        },
        showModalInvitesWithEvent(event_id) {
            axios.get(`/events/${event_id}`)
                .then(response => {
                    this.event = response.data;
                    this.showModalInvites = true;
                });
        },
        getNotifications()
        {
            axios.get("/notifications")
            .then(response => { 
                if(response.data.length) {
                    this.hasNotifications = true;
                }  
            })
            .catch(erro => {
                console.log(error);
            })
        },
        confirmEvent(event_id) {
            axios.post("/invitations/confirm", {event_id})
            .then((response) => {
                this.success(response.data.message);
                this.reload();
            })
            .catch(error => {
                this.error(error.response.data.message);
            });
        },
        cancelParticipation(event_id) {
            axios.post("/invitations/cancel", {event_id})
            .then((response) => {
                this.success(response.data.message);
                this.reload();
            })
            .catch(error => {
                this.error(error.response.data.message);
            });
        },
        cancelEvent(event_id) {
            axios.post(`/events/${event_id}/cancel`)
            .then(() => {
                this.success("Evento cancelado!");
                this.reload();
            })
            .catch(error => {
                this.error(error.response);
            });
        },
        reload() {
            setTimeout(() => {
                window.location.reload();
            })
        },
    },
    created() {
        this.getNotifications();
        const vm = this;
        EventBus.$on("EVENT_CREATED", function(event) {
            vm.event = event;
            vm.showModalInvites = true;
        });
    }
});

/*window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});*/
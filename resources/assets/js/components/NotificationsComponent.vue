<template>
    <div>
        <b-sidebar id="sidebar-notifications" title="" right shadow v-model="showSidebar">
           <div class="px-3 py-2">
                <div class="d-flex justify-content-center mb-3" v-if="preloader">
                    <b-spinner variant="secondary" label="Loading..."></b-spinner>
                </div>
                <template v-else-if="notifications.data.length">
                    <b-list-group>
                        <b-list-group-item v-for="notification in notifications.data" :key="notification.id" class="d-flex align-items-center item link" @click="markAsRead(notification)">
                            <span class="mr-auto" v-html="notification.message"></span>
                        </b-list-group-item>
                    </b-list-group>
                </template>
                <p v-else>Nada por aqui!</p>
            </div>
        </b-sidebar>
    </div>
</template>

<script>
import axios from "axios";
export default {
    data() {
        return {
            showSidebar:false,
            preloader: false,
            notifications: {
                data: [],
                meta: {
                    current_page:1,
                    last_page:1
                }
            }
        }
    },
    methods: {
        fetchNotifications() {
            this.preloader = true;
            axios.get(`/notifications`)
            .then(response => {
                const data = response.data;

                if(data.length) {
                    let vm = this;
                    data.forEach(item => {
                        item.type = item.type.replace(/\\/g, "/");
                        let id = item.id;
                        let link = `/events/${item.data.event.id}`;
                        if(item.type == 'App/Notifications/SendInvitation') {
                            let message = `<strong>${item.data.event.user.name}</strong> te convidou para participar do evento: <strong>${item.data.event.title}</strong>`;
                            this.notifications.data.push(...[{id, message,link}]);
                        }

                        if(item.type == "App/Notifications/CanceledEvent") {
                            let message = `<strong>${item.data.event.user.name}</strong> cancelou o evento: <strong>${item.data.event.title}</strong>`;
                            this.notifications.data.push(...[{id, message,link}]);
                        }

                        if(item.type == "App/Notifications/InvitationAccepted") {
                            let message = `<strong>${item.data.user.name}</strong> aceitou seu convite para o evento: <strong>${item.data.event.title}</strong>`;
                            this.notifications.data.push(...[{id, message,link}]);
                        }
                    });
                }
            })
            .catch(err => {
                console.log(err);
            })
            .finally(() => this.preloader = false)
        },
        markAsRead({id, link}) {
            axios.post(`/notifications/${id}/read`);
            window.location.href = link;
        }   

    },
    created() {
        this.showSidebar = true;
        this.fetchNotifications();
        
    }
}
</script>

<style scoped>
.link {
    cursor: pointer;
    margin-bottom: 10px;
}

.item span{
    max-height: 100px;
    text-overflow: ellipsis;
    overflow: hidden;
}

.link:hover {
    text-decoration: underline;
}

</style>
<template>
    <div>
        <b-sidebar id="sidebar-users" title="" right shadow v-model="showSidebar">
            <div class="px-3 py-2">
                <div class="d-flex justify-content-center mb-3" v-if="preloader">
                    <b-spinner variant="secondary" label="Loading..."></b-spinner>
                </div>
                <template v-else-if="users.data.length">
                <b-list-group v-for="user in users.data" :key="user.id">
                    <b-list-group-item class="d-flex align-items-center">
                        <b-avatar variant="info" class="mr-3"></b-avatar>
                        <span class="mr-auto">{{user.name}}</span>
                    </b-list-group-item>
                </b-list-group>
                </template>
                <p v-else>Nada por aqui...</p>
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
            users: {
                data: [],
                meta: {
                    current_page:1,
                    last_page:1
                }
            }
        }
    },
    methods: {
        fetchParticipants() {
            this.preloader = true;
            axios.get(`${location.pathname}/participants`)
            .then(response => {
                this.users.data = response.data
            })
            .catch(err => {
                console.log(err);
            })
            .finally(() => this.preloader = false)
        }   
    },
    created() {
        this.showSidebar = true;
        this.fetchParticipants();
        
    }
}
</script>

<style>

</style>
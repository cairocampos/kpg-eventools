<template>
    <div>
        <b-modal ref="teste" id="modal-1" title="Convidar pessoas para esse evento" v-model="show" @hide="reset">
            <div class="text-center" v-if="preloader && page == 1">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div class="list" v-else>
                <template v-if="users.data.length">
                    <b-list-group class="content">
                        <b-list-group-item class="d-flex align-items-center" v-for="user in users.data" :key="user.id">
                            <b-avatar variant="info" class="mr-3"></b-avatar>
                            <span class="mr-auto">{{user.name}}</span>
                            <button class="btn" disabled v-if="event.user_id == user.id">Organizador</button>
                            <div v-else>
                                <button class="btn btn-secondary" disabled v-if="user.message">{{user.message}}</button>
                                <button class="btn btn-secondary" @click="sendInvite(user.id)" v-else>Convidar</button>
                            </div>
                        </b-list-group-item>
                        <div v-if="!hasNextPage" v-observe-visibility="visibilityChanged">...</div>
                    </b-list-group>
                </template> 
                <p v-else>Nenhum usuário encontrado!</p>
            </div>
            <template #modal-footer="{ hide }">
                <button class="btn btn-primary" @click="hide()">Fechar</button>
            </template>
        </b-modal>
    </div>
</template>

<script>
import axios from "axios";
import toastify from "../../mixins/toastify-mixin";
import paginate from "../../mixins/paginate"
export default {
    props:["event"],
    mixins:[toastify, paginate],
    data() {
        return {
            users: {
                data:[],
            },
            show: false,
            preloader:false,
        }
    },
    watch: {
        page() {
            this.fetchUsers();
        }
    },
    methods: {
        fetchUsers() {
            this.preloader = true;
            axios.get(`/users?page=${this.page}`)
            .then(response => {
                this.users.data.push(...response.data.data);
                let {current_page, last_page} = response.data;
                this.meta = {current_page, last_page}
            }).catch(error => {
                console.log(error);
            }).finally(() => this.preloader = false);
                
        },
        openModal() {
            this.show = true;
            
            this.fetchUsers();

        },
        reset() {
            this.show = false;
            this.$store.dispatch("resetEvent");
            this.$emit("update:event", {});                                                                                                                                                                                           
            this.$emit("close", false);                                                                                                                                                                                                 
        },
        sendInvite(user_id) {
            const event_id = this.event.id;
            if(!event_id) this.error("Ocorreu um erro, tente novamente!");
            axios.post("/invitations", {user_id, event_id})
            .then(response => {
                const message = response.data.message ? response.data.message : "Convidado";
                this.users.data.find((user, index) => {
                    if(user.id == user_id) {
                        user.message = message;
                        this.$set(this.users.data, index, user);
                        return;
                    }
                });
            }).catch(error => {
                this.error(error.response.data.message);
            });

        }
    },
    created() {
        this.openModal();
    }
}
</script>

<style scoped>
.list {
    height:500px;
    overflow: hidden;
    overflow-y: auto;
}
</style>
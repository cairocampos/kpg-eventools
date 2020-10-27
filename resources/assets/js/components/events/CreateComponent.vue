<template>
    <div>
    <b-modal id="modal-create-event" ref="modal" title="Criar novo evento" size="lg" no-stacking >
        <FormComponent>
            <template #imageInput>
                <div class="form-group">
                    <label for="">Imagem de Capa</label>
                    <div class="image__view" @click="$refs.fileInput.click()">
                        <img ref="image" src="images/default.jpg" alt="Cover" width="200">
                    </div>
                    <input type="file" ref="fileInput" class="form-control d-none" @change="addImage" />
                </div>
            </template>
        </FormComponent>
        <template #modal-footer>
            <button v-if="preloader" class="btn btn-primary" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Salvando...
            </button>
            <button v-else class="btn btn-primary" @click="create">Salvar</button>
        </template>
    </b-modal>
    </div>
</template>

<script>
import {mapFields} from "../../services";
import FormComponent from "./FormComponent";
import toastify from "../../mixins/toastify-mixin";
import validFormDataEvent from "../../mixins/valid-formdata-event-mixin";
import EventBus from "../event-bus";
import {mapState} from "vuex";

export default {
    mixins:[toastify, validFormDataEvent],
    components: {
        FormComponent
    },
    data() {
        return {
            preloader:false,
        }
    },
    methods: {
        async create() {
            this.preloader = true;
            try {
                const data = await this.$store.dispatch("createEvent", this.formatData);
                this.$refs["modal"].hide();
                this.success("Criado com sucesso!");
                //this.$emit("created", data.data);
                EventBus.$emit("EVENT_CREATED", data.data)
            } catch (error) {
                const data = error.response.data;
                if("errors" in data) {
                    this.errors(data.errors);
                }
            } finally {
                this.preloader = false;
            }
        },
    },
    
}
</script>

<style>

</style>
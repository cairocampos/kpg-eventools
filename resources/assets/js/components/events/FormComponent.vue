<template>
    <form>
        <div class="row">
            <div class="col-4">
                <slot name="imageInput"></slot>
            </div>
            <div class="col-8">
                <div class="form-group">
                    <label for="title" class="col-form-label">Titulo*</label>
                    <input type="text" class="form-control" id="title" v-model="title" placeholder="Titulo do evento">
                </div>
                <div class="form-group">
                    <label for="description" class="col-form-label">Descrição*</label>
                    <textarea class="form-control" id="description" v-model="description" placeholder="Descrição sobre o evento..."></textarea>
                </div>
                <div class="form-group">
                    <label for="localization" class="col-form-label">Onde será realizado o evento ?*</label>
                    <input type="text" class="form-control" id="localization" v-model="localization" placeholder="Ex: Praça dos Amigos">
                </div>
                <div class="form-group">
                    <label for="started" class="col-form-label">Qual será a data e hora ?*</label>
                    <input type="datetime-local" class="form-control" id="started" v-model="started">
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import {mapFields} from "../../services";
import toastfy from "../../mixins/toastify-mixin";

export default {
    mixins:[toastfy],
    computed: {
        ...mapFields({
      fields: [
        "title",
        "started",
        "description",
        "localization",
      ],
        base: "event",
        mutation: "UPDATE_EVENT"
        }),
    },
}
</script>

<style scoped>
.image__view {
    cursor: pointer;
}

</style>
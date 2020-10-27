import {mapState} from "vuex";
export default {
    data() {
        return {
            cover:false
        }
    },
    computed: {
        ...mapState(["event"]),
        formatData() {
            const form = new FormData();
            form.append("title", this.event.title);
            form.append("description",this.event.description);
            form.append("started", this.event.started);
            form.append("localization", this.event.localization);
            if(this.cover) {
                form.append("cover", this.$refs.fileInput.files[0]);
            }

            return form;
        }
    },
    methods: {
        addImage(event) {
            if(event.srcElement.files.length) {
                const file = event.srcElement.files[0];
                if("type" in file && this.imageIsValid(file.type)) {
                    this.cover = true;
                    const reader = new FileReader();
                    reader.onload = () => {
                        this.$refs.image.src = reader.result
                        this.$refs.image.classList.remove("d-none");
                    }
    
                    reader.readAsDataURL(file);
                } else {
                    this.error("Insira uma imagem do tipo [PNG, JPEG, JPG]");
                }
            }
        },
        imageIsValid(type) {
            const mimes = ["png","jpeg","jpg"];
            if(type.includes("/")) {
                return mimes.includes(type.split("/")[1])
            }
        }
    }
}
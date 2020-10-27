export default {
    methods: {
        success(message) {
            this.$vToastify.success(message);
        },
        error(message) {
            this.$vToastify.error(message);
        },
        errors(messages) {
            Object.values(messages).forEach(item => {
                for(const message of item) {
                    this.error(message);
                }
            });
        }
    }
}
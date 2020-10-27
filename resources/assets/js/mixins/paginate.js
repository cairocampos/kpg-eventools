export default {
    data() {
        return {
            page:1,
            meta:{
                current_page:1,
                last_page:1
            }
        }
    },
    computed: {
        hasNextPage() {
            return this.meta.current_page === this.meta.last_page;
        }
    },
    methods: {
        visibilityChanged(isVisible) {
            if(!isVisible) return;

            if(this.hasNextPage) return;

            this.page++;
        }
    }
}
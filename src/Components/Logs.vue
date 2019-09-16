<template>
    <div>
        <h1>Hello From Logs</h1>
        <pre>{{logs}}</pre>
    </div>
</template>

<script type="text/babel">
    export default {
        name: 'error_log_logs',
        data() {
            return {
                logs: [],
                total_logs: 0,
                per_page: 20,
                page: 1,
                search: '',
                fetching: false
            }
        },
        methods: {
            getLogs() {
                this.fetching = true;
                this.$get('get_logs', {
                    search: this.search,
                    page: this.page,
                    per_page: this.per_page
                })
                    .then(response => {
                        this.logs = response.data.logs.data;
                        this.total_logs = response.data.logs.total;
                        this.per_page = response.data.logs.per_page;
                        this.page = response.data.logs.current_page;
                    })
                    .fail(error => {
                        // handle error here
                        console.log(error);
                    })
                    .always(() => {
                        this.fetching = false;
                    });
            }
        },
        mounted() {
            this.getLogs();
        }
    }
</script>

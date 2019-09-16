<template>
    <el-menu
        :router="true"
        mode="horizontal"
        class="wpmn-navigation"
        :default-active="active"
    >
        <el-menu-item
            :key="item.route"
            :index="item.route"
            v-html="item.title"
            v-for="item in items"
            :route="{ name: item.route }"
        />
    </el-menu>
</template>

<script>
    export default {
        name: 'Navigation',
        data() {
            return {
                active: null,
                items: []
            }
        },
        watch: {
            '$route'(to, from) {
                if (this.$route.name) {
                    this.setActive();
                }
            }
        },
        methods: {
            defaultRoutes() {
                return [
                    {
                        route: 'home',
                        title: 'Dashboard'
                    },
                    {
                        route: 'logs',
                        title: 'Logs'
                    },
                    {
                        route: 'notifications',
                        title: 'Notification Settings'
                    }
                ]
            },
            setMenus() {
                this.items = this.defaultRoutes();
            },
            setActive() {
                this.active = this.$route.meta.parent || this.$route.name;
            }
        },
        mounted() {
            this.setMenus();
        }
    }
</script>

<template>
    <div class="wpns-app">
        <div class="wpns-header">
            <navigation/>
        </div>
        <div class="wpmn_body">
            <router-view :key="$route.fullPath"></router-view>
        </div>
    </div>
</template>

<script type="text/babel">
    import Navigation from './Layouts/Navigation';
    import Clipboard from 'clipboard';

    export default {
        name: 'Application',
        components: {
            Navigation
        },
        mounted() {
            var clipboard = new Clipboard('.copy');
            clipboard.on('success', (e) => {
                this.$message({
                    message: 'Copied to Clipboard!',
                    type: 'success'
                });
            });
        }
    }
</script>

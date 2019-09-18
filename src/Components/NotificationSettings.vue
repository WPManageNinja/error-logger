<template>
    <div>
        <el-container>
            <el-row class="email-section">
                <el-col>
                    <h3> Email Notification Settings :</h3>
                </el-col>
                <el-col>
                    <label>Email to :</label>
                    <input type="text" Placeholder="Email" v-model="email"/>
                </el-col>
            </el-row>
        </el-container>

        <el-container>
            <el-row class="notification-section">
                <el-col>
                    <h3>Notification Types Settings :</h3>
                </el-col>

                <el-col>
                    <ul class="myclass">
                        <li v-for="name in error_names" :label="name" :key="name"><input type="checkbox"><span> {{name}}</span></li>
                    </ul>
                </el-col>
            </el-row>
        </el-container>

        <el-container>
            <el-row class="database-log-section">
                <el-col>
                    <h3>Database Logs Settings :</h3>
                </el-col>
                <el-col>
                    <ul class="myclass">
                        <li v-for="name in error_names" :label="name" :key="name"><input type="checkbox"><span> {{name}}</span></li>
                    </ul>
                </el-col>
            </el-row>
        </el-container>

        <el-row>
            <div class="btn-save">
                <el-button type="success" size="mini" @click.prevent="saveSettings">Save</el-button>
            </div>
        </el-row>
           
    </div>

</template>

<script type="text/babel">
    export default {
        name: 'error_log_notifications',
        data() {
            return {
                checkList1: [],
                checkList2: ['selected and disabled', 'Option A'],
                error_names: [
                    'E_ERROR', 'E_WARNING', 'E_PARSE', 'E_NOTICE', 'E_CORE_ERROR', 'E_CORE_WARNING', 'E_COMPILE_ERROR', 'E_COMPILE_WARNING', 'E_USER_ERROR', 'E_USER_WARNING', 'E_USER_NOTICE', 'E_STRICT', 'E_RECOVERABLE_ERROR', 'E_DEPRECATED', 'E_USER_DEPRECATED'
                ],
                email: ''
            };
        },

        methods: {
            saveSettings() {
                this.$post('save_notification_settings', {
                    name: 'Donald Duck',
                    value: 'Duckburg',
                    email: this.email
                }).then(response => {
                    console.log(response);
                });

                console.log('ok dude');
            },
            getNotifactionSettings() {
                this.$get('get_notification_settings')
                    .then(response => {
                        this.email = response.data.email;
                        console.log(response.data.email);
                    })
                    .fail(error => {
                        // handle error here
                        console.log(error);
                    })
            }
        },

        mounted() {
            this.getNotifactionSettings();
        }
    }
</script>

<style typle="text/css">

    .email-section{
        margin-top: 15px;
        margin-bottom:15px;
    }

    .notification-section{
        margin-bottom:15px;
    }

    .database-log-section{
        margin-bottom:15px;
    }

    .btn-save{
        margin-top: 15px;
    }

</style>

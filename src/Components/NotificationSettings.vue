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
                    <el-checkbox-group v-model="checkList1">
                        <el-checkbox  v-for="name in error_lavels" :label="name" :key="name" style="display:block;margin-top:10px;"></el-checkbox>
                    </el-checkbox-group>
                </el-col>
            </el-row>
        </el-container>

        <el-container>
            <el-row class="database-log-section">
                <el-col>
                    <h3>Database Logs Settings :</h3>
                </el-col>
                <el-col>
                    <el-checkbox-group v-model="checkList2">
                        <el-checkbox  v-for="name in error_lavels" :label="name" :key="name" style="display:block;margin-top:10px;">{{name}}</el-checkbox>
                    </el-checkbox-group>
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
                checkList2: [],
                email: '',
                error_lavels: []
            };
        },

        methods: {
            saveSettings() {
                this.$post('save_notification_settings', {
                    email: this.email,
                    notification_type_settings: this.checkList1,
                    database_logs_settings: this.checkList2
                }).then(response => {
                    console.log(response);
                });

                console.log(this.checkList1);
            },
            getNotifactionSettings() {
                this.$get('get_notification_settings')
                    .then(response => {
                        let dbEmail = response.data.email_settings.db_email_to;
                        if (dbEmail) {
                            this.email = response.data.email_settings.db_email_to;
                        } else {
                            this.email = response.data.email_settings.email_to;
                        }                        
                        this.error_lavels = Object.keys(response.data.error_levels);    
                        if (response.data.notification_settings) {
                            this.checkList1 = response.data.notification_settings;
                        }                   
                        if (response.data.database_settings) {
                            this.checkList2 = response.data.database_settings;
                        } 
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

<template>
    <div>
        <el-row>
            <el-form>
                <el-col :span="4">
                    <el-form-item>
                        <el-select v-model="value" placeholder="Type">
                            <el-option v-for="item in logs" :key= "item.log_type" :label= "item.log_type" :value= "item.log_type">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
                <el-col :span="4">
                    <el-form-item>
                        <el-input placeholder="Search in logs" prefix-icon="el-icon-search"></el-input>
                    </el-form-item>
                </el-col>
            </el-form>
            <el-col>
                <el-table :data="logs" border style="width: 100%">
                    <el-table-column type="index" label="Id" width="80">
                        
                    </el-table-column>

                    <el-table-column prop="log_data" label="log data" width="500">

                    </el-table-column>

                    <el-table-column prop="request_method" label="request method">

                    </el-table-column>

                    <el-table-column  prop="created_at" label="created at">

                    </el-table-column>

                    <el-table-column prop="log_type" label="type">

                    </el-table-column>
                </el-table>

                <div class="pagination-section">
                    <el-pagination background layout="prev, pager, next" :total="total_logs">
                    </el-pagination>
                </div>
            </el-col>
        </el-row>
        <!-- <pre>{{logs}}</pre> -->
    </div>
</template>

<style>

    .pagination-section{
        margin-top:20px;
        float: right;
    }

</style>

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
                fetching: false,
                formInline: {
                    user: '',
                    region: ''
                },
                value: ''               
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

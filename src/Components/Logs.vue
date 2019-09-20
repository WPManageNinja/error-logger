<template>
    <div>
        <el-row>
            <el-col>
                <el-form>
                    <el-col :span="4">
                        <el-form-item>
                            <el-select v-model="filterValue" placeholder="Type" @change="selectItem">
                                <el-option v-for="item in error_levels" :key= "item" :label= "item" :value= "item"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item>
                            <!-- <el-input placeholder="Search in logs" prefix-icon="el-icon-search" @keyup.enter="searchData" v-model="input_search"></el-input> -->
                            <input placeholder="Search in logs" @keyup="searchData" v-model="input_search" />
                        </el-form-item>
                    </el-col>
                </el-form>
            </el-col>
            <el-col>
                <el-table :data="logs" style="width: 100%">
                    <el-table-column type="expand">
                        <template slot-scope="props">
                            <p><strong>About Log Data : </strong>{{ props.row.log_data }}</p>
                            <p><strong>Log Type : </strong>{{ props.row.log_type }}</p>
                            <p><strong>Method : </strong>{{ props.row.request_method }}</p>
                            <p><strong>Created At : </strong>{{ props.row.created_at }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Id" prop="id">
                    </el-table-column>
                    <el-table-column prop="log_type" label="type">
                    </el-table-column>
                    <el-table-column prop="request_method" label="request method">
                    </el-table-column>
                    <el-table-column prop="created_at" label="created at">
                    </el-table-column>
                </el-table>

                <div class="pagination-section">
                    <el-pagination background layout="prev, pager, next" :current-page="page" :page-size="per_page" @current-change="handleCurrentChange" :total="total_data">
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

    div.cell{
        text-align: center;
    }

</style>

<script type="text/babel">
    export default {
        name: 'error_log_logs',
        data() {
            return {
                input_search: '',
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
                filterValue: '',
                error_levels: [],
                total_data: 0               
            }
        },
        methods: {
            handleCurrentChange(val) {
                console.log(val);
                this.$post('get_logs_pagination', {
                    value: val
                })
                    .then(response => {
                        this.logs = response.data.logs.data;
                        this.total_logs = response.data.logs.total;
                        this.per_page = response.data.logs.per_page;
                        this.page = response.data.logs.current_page;
                        this.total_data = response.data.total_count; 
                        console.log(response);
                    })
            },
            selectItem() {
                console.log('i m pressed ' + this.filterValue);
                this.$post('get_logs', {
                    search: this.input_search,
                    select_filter: this.filterValue
                })
                    .then(response => {
                        this.logs = response.data.logs.data;
                        this.total_logs = response.data.logs.total;
                        this.per_page = response.data.logs.per_page;
                        this.page = response.data.logs.current_page;
                        console.log(this.logs);
                    })
            },
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
                        this.error_levels = Object.keys(response.data.error_levels); 
                        this.total_data = response.data.total_count; 
                        console.log(response);
                    })
                    .fail(error => {
                        // handle error here
                        console.log(error);
                    })
                    .always(() => {
                        this.fetching = false;
                    });
            },

            searchData() {
                this.$post('get_logs', {
                    search: this.input_search,
                    select_filter: this.filterValue
                }).then(response => { 
                    this.logs = response.data.logs.data;
                    this.total_logs = response.data.logs.total;
                    this.per_page = response.data.logs.per_page;
                    this.page = response.data.logs.current_page;
                    this.logs = response.data.logs.data;
                    console.log(response);
                });
            }
        },
        mounted() {
            this.getLogs();
        }
    }
</script>

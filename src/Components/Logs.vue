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
                            <el-input placeholder="Search in logs" prefix-icon="el-icon-search" @keyup.enter.native="searchData()" v-model="input_search"></el-input>
                            <!-- <input placeholder="Search in logs" @keyup="searchData" v-model="input_search" /> -->
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
                            <p><strong>Line Number : </strong>{{ props.row.log_line }}</p>
                            <p><strong>File Name : </strong>{{ props.row.log_file }}</p>
                            <p><strong>Created At : </strong>{{ props.row.created_at }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Id" prop="id"></el-table-column>
                    <el-table-column prop="log_type" label="type"></el-table-column>
                    <el-table-column prop="request_method" label="request method"></el-table-column>
                    <el-table-column prop="created_at" label="created at"></el-table-column>
                    <el-table-column @click.prevent = "deleteRow()" label="Action">
                        <template slot-scope="scope">
                            <a @click.prevent="confirmDelete(scope.row.id)" href="#"><i class="el-icon-delete"></i></a>
                        </template>
                    </el-table-column>
                </el-table>

                <div class="pagination-section">
                    <el-pagination background layout="prev, pager, next" :current-page="page" :page-size="per_page" @current-change="handleCurrentChange" :total="total_logs">
                    </el-pagination>
                </div>

                <!--Delete form Confimation Modal-->
                <el-dialog
                    title="Are You Sure, You want to delete this Log?"
                    :visible.sync="deleteDialogVisible"
                    width="60%">
                    <div class="modal_body">
                        <p>All the data assoscilate with this log  will be deleted</p>                     
                    </div>
                    <span slot="footer" class="dialog-footer">
                        <el-button @click="deleteDialogVisible = false">Cancel</el-button>
                        <el-button type="primary" @click="deleteFormNow()">Confirm</el-button>
                    </span>
                </el-dialog>
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
                per_page: 5,
                page: 1,
                search: '',
                fetching: false,
                formInline: {
                    user: '',
                    region: ''
                },
                filterValue: '',
                error_levels: [],
                total_data: 0,
                deleteDialogVisible: false,
                rowTODelete: ''              
            }
        },
        methods: {

            deleteFormNow() {
                console.log('delete row now ' + this.rowTODelete);

                this.$post('delete_logs', {
                    row_id: this.rowTODelete
                })
                    .then(response => {
                        console.log(response);
                    })
                    .fail(error => {
                        console.log(error);
                    })
                    .always(() => {
                        this.deleteDialogVisible = false;
                    });
            },
            confirmDelete(row) {
                this.deleteDialogVisible = true;
                this.rowTODelete = row;
                console.log('delete log ' + row);
            },
            handleCurrentChange(val) {
                console.log(this.filterValue);
                this.$post('get_logs', {
                    value: val,
                    search: this.input_search,
                    select_filter: this.filterValue
                })
                    .then(response => {
                        this.logs = response.data.logs;
                        this.total_logs = response.data.total;
                        this.per_page = response.data.per_page;
                        this.page = response.data.current_page;
                        // this.total_data = response.data.total_count; 
                        console.log(response);
                    })
            },
            selectItem() {
                console.log('i m pressed ' + this.filterValue);

                this.filterValue = this.filterValue;

                this.$post('get_logs', {
                    search: this.input_search,
                    select_filter: this.filterValue
                })
                    .then(response => {
                        this.logs = response.data.logs;
                        this.total_logs = response.data.total;
                        this.per_page = response.data.per_page;
                        this.page = response.data.current_page;
                        // this.page = response.data.logs.current_page;
                        console.log(response);
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
                        this.logs = response.data.logs;
                        this.total_logs = response.data.total;
                        this.per_page = response.data.per_page;
                        this.page = response.data.current_page;
                        this.error_levels = Object.keys(response.data.error_levels); 
                        // this.total_data = response.data.total_count; 
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
                this.input_search = this.input_search;
                console.log(this.input_search);
                this.$post('get_logs', {
                    search: this.input_search,
                    select_filter: this.filterValue
                }).then(response => { 
                    this.logs = response.data.logs;
                    this.total_logs = response.data.total;
                    this.per_page = response.data.per_page;
                    this.page = response.data.current_page;
                    // this.logs = response.data.logs.data;
                    console.log(response);
                });
            }
        },
        mounted() {
            this.getLogs();
        }
    }
</script>

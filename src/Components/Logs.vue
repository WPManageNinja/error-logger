<template>
    <div>
        <el-row>
            <el-col>
                <el-form>
                    <el-col :span="7" style="display:inline-block;">
                        <el-select v-model="optionValue" placeholder="Bulk Actions">
                            <el-option key="bulk" label="Bulk Actions" value="bulk"></el-option>
                            <el-option key="delete" label="Delete Logs" value="delete"></el-option>
                        </el-select>
                        <el-button type="primary" @click.prevent="doAction()">Apply</el-button>
                    </el-col>
                    <div class="filtering-data">
                        <el-col :span="12">
                            <el-form-item>
                                <el-select v-model="filterValue" placeholder="Type" @change="selectItem">
                                    <el-option v-for="item in error_levels" :key= "item" :label= "item" :value= "item"></el-option>
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :span="10">
                            <el-form-item>
                                <el-input placeholder="Search in logs" prefix-icon="el-icon-search" @keyup.enter.native="searchData()" v-model="input_search"></el-input>
                                <!-- <input placeholder="Search in logs" @keyup="searchData" v-model="input_search" /> -->
                            </el-form-item>
                        </el-col>
                    </div>
                </el-form>
            </el-col>
            <el-col>
                <el-table v-loading="loading" element-loading-text="Loading..." :data="logs" style="width: 100%"  @selection-change="handleSelectionChange" ref="multipleTable">
                    <el-table-column type="selection" width="55"></el-table-column>
                    <el-table-column type="expand">
                        <template slot-scope="props">
                            <p><strong>About Log Data : </strong>{{ props.row.log_data }}</p>
                            <p><strong>Log Type : </strong>{{ mapError[props.row.log_type] }}</p>
                            <p><strong>Method : </strong>{{ props.row.request_method }}</p>
                            <p><strong>Line Number : </strong>{{ props.row.log_line }}</p>
                            <p><strong>File Name : </strong>{{ props.row.log_file }}</p>
                            <p><strong>Created At : </strong>{{ props.row.created_at }}</p>
                        </template>
                    </el-table-column>
                    <el-table-column label="Id" prop="id"></el-table-column>
                    <el-table-column label="type">
                        <template slot-scope="props">
                            {{mapError[props.row.log_type]}}
                        </template>
                    </el-table-column>
                    <el-table-column prop="request_method" label="request method"></el-table-column>
                    <el-table-column prop="created_at" label="created at"></el-table-column>
                    <el-table-column @click.prevent = "deleteRow()" label="Action">
                        <template slot-scope="scope">
                            <a @click.prevent="confirmDelete(scope.row.id)" href="#"><i class="el-icon-delete"></i></a>
                        </template>
                    </el-table-column>
                </el-table>

                <div class="pagination-section">
                    <el-pagination background layout="prev, pager, sizes, next" :page-sizes="pageSizes" :current-page="page" :page-size="per_page_item" @size-change="handleSizeChange" @current-change="handleCurrentChange" :total="total_logs">
                    </el-pagination>
                </div>

                <!--Delete form Confimation Modal-->
                <el-dialog
                    title="Are You Sure, You want to delete these selected Log?"
                    :visible.sync="deleteDialogVisibleMultiple"
                    width="60%">
                    <div class="modal_body">
                        <p>All the data assoscilate with this log  will be deleted</p>                     
                    </div>
                    <span slot="footer" class="dialog-footer">
                        <el-button @click="deleteDialogVisibleMultiple = false">Cancel</el-button>
                        <el-button type="primary" @click="deleteMultiple()">Confirm</el-button>
                    </span>
                </el-dialog>

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

    .filtering-data{
        float: right;
    }

    .item-type{
        margin-right: 5px;
    }

    ..el-notification {
        margin-top:10px;
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
                per_page_item: 5,
                page: 1,
                pageSizes: [5, 10, 20, 30, 40, 50, 100, 200],
                fetching: false,
                formInline: {
                    user: '',
                    region: ''
                },
                filterValue: '',
                error_levels: [],
                total_data: 0,
                deleteDialogVisible: false,
                rowTODelete: '',
                multipleSelection: [],
                optionValue: '',
                deleteDialogVisibleMultiple: false,
                loading: true,
                mapError: 
                    {
                        64: 'E_COMPILE_ERROR',
                        128: 'E_COMPILE_WARNING',
                        16: 'E_CORE_ERROR',
                        32: 'E_CORE_WARNING',
                        8192: 'E_DEPRECATED',
                        1: 'E_ERROR',
                        8: 'E_NOTICE',
                        4: 'E_PARSE',
                        4096: 'E_RECOVERABLE_ERROR',
                        2048: 'E_STRICT',
                        16384: 'E_USER_DEPRECATED',
                        256: 'E_USER_ERROR',
                        1024: 'E_USER_NOTICE',
                        512: 'E_USER_WARNING',
                        2: 'E_WARNING'
                    }
                            
            }
        },
        methods: {
            getLogs() {
                this.loading = true;
                console.log('i am now getlogs ' + this.per_page_item);

                this.fetching = true;
                this.$get('get_logs', {
                    search: this.input_search,
                    page: this.page,
                    per_page_total: this.per_page_item,
                    select_filter: this.filterValue,
                    value: this.value
                })
                    .then(response => {
                        this.loading = false;
                        this.logs = response.data.logs;
                        this.total_logs = response.data.total;
                        this.per_page_item = response.data.per_page;
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
            // for multiple delete dialog
            deleteMultiple() {
                this.$post('delete_bulk_logs', {
                    row_ids: this.multipleSelection
                })
                    .then(response => {
                        this.$notify({
                            title: 'Success',
                            message: 'Logs Deleted Successfully',
                            type: 'success',
                            position: 'bottom-right'
                        });
                        console.log(response);
                        this.getLogs();
                    })
                    .fail(error => {
                        console.log(error);
                    })
                    .always(() => {
                        console.log('always');
                        this.deleteDialogVisibleMultiple = false;
                    });
            },
            doAction() {
                if (this.multipleSelection.length == 0) {
                    console.log('need to select one to ' + this.optionValue);
                } else if (this.optionValue == 'delete') {
                    this.deleteDialogVisibleMultiple = true;
                    console.log('selected items is = ' + this.multipleSelection.length + ' and we have to ' + this.optionValue);
                }
            },
            deleteFormNow() {
                console.log('delete row now ' + this.rowTODelete);
                this.loading = true;
                this.$post('delete_logs', {
                    row_id: this.rowTODelete
                })
                    .then(response => {
                        this.loading = false;
                        this.$notify({
                            title: 'Success',
                            message: 'Logs Deleted Successfully',
                            type: 'success',
                            position: 'bottom-right'
                        });
                        console.log(response);
                        this.getLogs();
                    })
                    .fail(error => {
                        console.log(error);
                    })
                    .always(() => {
                        this.deleteDialogVisible = false;
                    });
            },
            handleSelectionChange(val) {
                this.multipleSelection = val;
                console.log('selected items ' + this.multipleSelection);
            },
            handleSizeChange(val) {
                this.per_page_item = val;
                console.log('per page item ' + this.per_page_item);
                this.getLogs();
            },
            confirmDelete(row) {
                this.deleteDialogVisible = true;
                console.log('delete log ' + row);
                this.rowTODelete = row;
            },
            handleCurrentChange(val) {
                this.value = val;
                console.log(this.value);
                this.getLogs();
            },
            searchData() {
                this.input_search = this.input_search;
                console.log(this.input_search);
                this.getLogs();
            },
            selectItem() {
                this.filterValue = this.filterValue;
                console.log('i m pressed ' + this.filterValue);
                this.getLogs();
            }
        },
        mounted() {
            console.log(this.mapError[2]);
            this.getLogs();
        }
    }
</script>

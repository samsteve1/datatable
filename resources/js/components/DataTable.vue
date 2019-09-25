<template>

    <div class="card">
        <div class="card-header">
            {{ response.table }}
            <a href="#" @click.prevent="toggleCreationForm" v-if="response.allow.creation" class="float-right">
                {{ creating.active ? 'Cancel' : 'New record' }}
            </a>
        </div>


        <div class="card-body">

            <div class="well" v-if="creating.active">
                <form action="#" class="form-horizontal" @submit.prevent="store">
                    <div class="form-group" v-for="(column, index) in response.creatable" :key="index">
                        <label :for="column" class="col-md-3 control-label">{{ column }}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" :id="column" v-model="creating.form[column]" :class="{' is-invalid': creating.errors[column]}">
                            <span class="invalid-feedback" v-if="creating.errors[column]">
                                <strong>{{ creating.errors[column][0] }}</strong>
                            </span>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-6">
                            <button class="btn btn-primary" type="submit">Create</button>
                        </div>
                    </div>


                </form>
            </div>
            <form action="#" @submit.prevent="getRecords">
                <label for="search">Search</label>

                <div class="row row-fluid">
                    <div class="form-group col-md-3">
                        <select class="form-control" v-model="search.column">
                            <option v-for="(column, index) in response.displayable" :value="column" :key="index">{{ column }}</option>
                        </select>
                    </div>
                     <div class="form-group col-md-3">
                        <select class="form-control" v-model="search.operator">
                            <option value="equals">=</option>
                            <option value="contains">contains</option>
                            <option value="starts_with">starts with</option>
                            <option value="ends_with">ends with</option>
                            <option value="greater_than">greater than</option>
                            <option value="less_than">less than</option>
                            <option value="less_than_equal">less than or equal</option>
                            <option value="greater_than_equal">greater than or equal</option>
                        </select>
                    </div>
                     <div class="form-group col-md-6">
                        <div class="input-group">

                            <input type="text" id="search" v-model="search.value" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        <div class="row">
            <div class="form-group col-md-10">
                <label for="filter">Quick search current results</label>
                <input type="text" id="filter" class="form-control" v-model="quickSearchQuery" >
            </div>

            <div class="form-group col-md-2">
                <label for="limit">Display records</label>

                <select id="limit" class="form-control" v-model="limit" @change="getRecords">
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="1000">1000</option>
                    <option value="">All</option>
                </select>
            </div>
        </div>
        <div class="table-responsive">
               <table class="table table-striped">
                    <thead>
                        <tr>
                            <th v-for="(column, index) in response.displayable" :key="index">
                               <span class="sortable" @click="sortBy(column)"> {{ column }}</span>

                               <div v-if="sort.key === column" :class="{'arrow arrow--asc': sort.order === 'asc', 'arrow arrow--desc': sort.order === 'desc'}"></div>
                            </th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr v-for="(record, index) in filteredRecords" :key="index">
                            <td v-for="(columnValue, column, index) in record" :key="index">
                            <template v-if="editing.id === record.id  && isUpdatable(column)">
                                <div class="form-group">
                                    <input type="text" class="form-control " :class="{' is-invalid': editing.errors[column]}"  v-model="editing.form[column]">

                                    <span class="invalid-feedback" v-if="editing.errors[column]">
                                        <strong>{{ editing.errors[column][0] }}</strong>
                                    </span>

                                </div>

                            </template>
                                <template v-else>
                                    {{ columnValue }}

                                </template>
                            </td>

                            <td>
                                <a href="#" @click.prevent="edit(record)" v-if="editing.id !== record.id">Edit</a>
                                <template v-if="editing.id === record.id">
                                    <a href="#" @click.prevent="update">Save</a> <br>
                                    <a href="#" @click.prevent="editing.id = null">Cancel</a>
                                </template>

                            </td>
                        </tr>
                    </tbody>
               </table>
               </div>
        </div>
    </div>

</template>

<script>
import queryString from 'query-string';
    export default {
        data() {
            return {
                response: {
                    'table' : '',
                    'creatable': [],
                    displayable : [],
                    records: [],
                    allow: {}
                },
                sort: {
                    key: 'id',
                    order: 'asc'
                },
                quickSearchQuery: '',
                limit: 50,
                editing: {
                    id: null,
                    form: {},
                    errors: []
                },
                'creating': {
                    active: false,
                    form: {},
                    errors: []
                },
                search: {
                    value: '',
                    'operator': 'equals',
                    'column' : 'id'
                }
            }
        },
        computed: {
            filteredRecords () {

                let data = this.response.records

                data = data.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLowerCase().indexOf(this.quickSearchQuery.toLowerCase()) > -1
                    })
                })
                if (this.sort.key) {
                    data = _.orderBy(data, (i) => {
                        let value = i[this.sort.key]

                        if(!isNaN(parseFloat(value))) {
                            return parseFloat(value)
                        }

                        return String(i[this.sort.key]).toLowerCase()
                    }, this.sort.order)
                }
                return data
            }
        },
        props: ['endpoint'],


        methods: {
            getRecords () {
                console.log(this.getQueryParameters())
                return axios.get(`${this.endpoint}?${this.getQueryParameters()}`).then((response) => {
                    this.response = response.data.data

                })
            },
            getQueryParameters () {
                return queryString.stringify({
                    limit: this.limit,
                    'column': this.search.column,
                    'operator': this.search.operator,
                    'value': this.search.value

                })
            },

            sortBy (column) {
               this.sort.key = column
               this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
            },
            edit (record) {
                this.editing.errors = []
                this.editing.id = record.id
                this.editing.form = _.pick(record, this.response.updatable)
                console.log(this.editing.form)
            },
            isUpdatable (column) {
                return this.response.updatable.includes(column)
            },
            update () {
                axios.patch(`${this.endpoint}/${this.editing.id}`, this.editing.form).then(() => {
                    this.getRecords().then(() => {
                        this.editing.id = null,
                        this.editing.form = {}
                    })
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.editing.errors = error.response.data.errors;
                    }

                })
            },
            store () {
                axios.post(`${this.endpoint}`, this.creating.form).then(() => {
                    this.getRecords().then(() => {
                        this.creating.active = false
                        this.creating.form = {}
                        this.creating.errors = []
                    })
                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.creating.errors = error.response.data.errors
                    }

                })
            },

            toggleCreationForm () {
                this.creating.active = !this.creating.active;
            },

        },
         mounted() {
            this.getRecords()

        }
    }

</script>


<style lang ="scss" scoped>
    .sortable {
        cursor: pointer
    }

    .arrow {
        display: inline-block;
        vertical-align: middle;
        width:0;
        height:0;
        margin-left:5px;
        opacity: .6;

        &--asc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid #222;
        }

         &--desc {
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #222;
        }
    }
</style>
div.user

<template>

    <div class="card">
        <div class="card-header">{{ response.table }}</div>


        <div class="card-body">
        <div class="row">
            <div class="form-group col-md-10">
                <label for="filter">Quick search current results</label>
                <input type="text" id="filter" class="form-control" v-model="quickSearchQuery">
            </div>

            <div class="form-group col-md-2">
                {{ quickSearchQuery }}
            </div>
        </div>
        <div class="table-responsive">
               <table class="table table-striped">
                    <thead>
                        <tr>
                            <th v-for="column in response.displayable">
                               <span class="sortable" @click="sortBy(column)"> {{ column }}</span>

                               <div v-if="sort.key === column" :class="{'arrow arrow--asc': sort.order === 'asc', 'arrow arrow--desc': sort.order === 'desc'}"></div>
                            </th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="record in filteredRecords">
                            <td v-for="columnValue, column in record">{{ columnValue }}</td>

                            <td>

                            </td>
                        </tr>
                    </tbody>
               </table>
               </div>
        </div>
    </div>

</template>

<script>
    export default {
        data() {
            return {
                response: {
                    'table' : '',
                    displayable : [],
                    records: []
                },
                sort: {
                    key: 'id',
                    order: 'asc'
                },
                quickSearchQuery: ''
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
                return axios.get(`${this.endpoint}`).then((response) => {
                    this.response = response.data.data

                })
            },

            sortBy (column) {
               this.sort.key = column
               this.sort.order = this.sort.order === 'asc' ? 'desc' : 'asc'
            }
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

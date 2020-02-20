<template>
  <div id="_table">
    <table
      :style="`width:${parseInt(width)}%;`"
      :class="{
      'striped': striped,
      'hoverable': hoverable,
      'loading loading-lg': is_loading
    }">
      <tr class="title" v-if="title || navbar" :class="navbar ? 'navbar_title' : ''">
        <td align="justify" :colspan="columns.length">
          <span class="title_name">
            <slot name="title" v-if="title" />
          </span>
          <div class="input-group input-inline has-icon-right" v-if="navbar">
            <input class="form-input" type="text" 
            :placeholder="navbar" v-model="search.message" @keyup="searchData">
            <i class="form-icon icon icon-search"></i>
          </div>
        </td>
      </tr>

      <tr class="table_columns">
        <td
          v-for="column in columns"
          :key="column.label"
          :class="`col_${column.field}`"
        >{{column.label}}</td>
      </tr>

      <tr v-if="tableData.length == 0 && !is_loading && emptyMessage">
        <td align="justify" :colspan="columns.length"
        style="padding: 0;">
          <div class="empty">
            <div class="empty-icon">
              <i class="icon icon-people"></i>
            </div>
            <p class="empty-title h6">{{ emptyMessage }}</p>
          </div>
        </td>
      </tr>

      
      <tr v-for="(row, row_num) in displayData" :key="row_num" :class="`row row_${row_num}`">
        <td v-for="column in columns" :key="column.field" :class="`col_${column.field}`">
          <slot
            :name="column.field"
            :data="row">
            <template>
              {{row[column.field]}}
            </template>
          </slot>
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    columns: Array,
    tableData: {
      type: Array,
      default: () => []
    },
    width: {
      type: [Number, String],
      default: 100,
    },
    striped: Boolean,
    hoverable: Boolean,
    title: Boolean,
    navbar: String,
    emptyMessage: String,
  },
  data: () => ({
    is_loading: true,
    search: {
      columns: [],
      message: '',
    },
    originalData: [],
    displayData : [],
  }),
  methods: {
    searchData() {
      if (this.search.message == '') {
        this.displayData = this.originalData;
      } else {
        var searchColumns = this.search.columns;
        var searchMessage = this.search.message;
        this.displayData = this.originalData.filter((row) => {
          let found = false;
          Object.keys(row).forEach((k) => {
            if(searchColumns.indexOf(k) > -1) {
              if(JSON.stringify(row[k]).includes(searchMessage)) {
                found = true;
              }
            }
          });
          return found;
        })
      }
    }
  },
  watch: {
    tableData(data) {
      this.is_loading = false;
      if (this.tableData) {
        this.originalData = this.tableData;
        this.displayData  = this.tableData;
      }
      this.search.columns = this.columns
        .filter((x) => x.search)
        .map(({field}) => field);
    }
  },
};
</script>

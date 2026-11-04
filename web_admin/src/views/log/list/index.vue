<template>
  <div :class="{'app-container':!userInfo}" ref="container">
    <!--工具条-->
    <el-col :span="24" class="no-bottom-form">
      <el-form :inline="true" class="list-pre-table">

        <el-form-item class="mobile-full-width">
          <el-date-picker size="small" v-model="date_range" type="daterange" align="right"
                          placeholder="日期范围" :picker-options="pickerOptions">
          </el-date-picker>
        </el-form-item>
        <el-form-item v-if="!isMobile">
          <el-button @click="handleSearch" size="small">搜索</el-button>
        </el-form-item>

      </el-form>
    </el-col>
    <el-table :data="list.data" v-loading="loading" stripe border class="list-table"
              @filter-change="handleFilterChange"
              @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
      
      <el-table-column prop="id" label="ID" width="70">
      </el-table-column>

      <el-table-column prop="created_at" label="登录时间" width="160">
      </el-table-column>

      <el-table-column prop="ip" label="登录IP" min-width="150" show-overflow-tooltip>
      </el-table-column>

      <el-table-column prop="address" label="登录地址" min-width="150" show-overflow-tooltip>
      </el-table-column>

    </el-table>

    <el-col :span="24" class='list-after-action' style="padding-top: 12px;text-align: right;">
      <el-pagination
          v-if='list.total>0'
          class='pagination'
          :page-size="list.per_page"
          :page-sizes="[10, 20, 30, 40, 50, 100]"
          :total="list.total"
          :current-page.sync='list.current_page'
          layout="total, sizes, prev, pager, next, jumper"
          @current-change='onChangePage'
          @size-change='onChangePageSize'>
      </el-pagination>
    </el-col>

  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { getList as getLogList } from '~/api/log'

  export default {
    props: {
      userInfo: {
        type: Object,
        default: null
      },
      filterType: {
        default: null
      }
    },
    data () {
      return {
        isMobile: this.$store.state.app.isMobile,
        loading: false,
        search: {
          type: 'id',
          value: ''
        },
        date_range: '',
        where: {},
        list: {
          current_page: 1,
          data: [],
          from: 1,
          last_page: 1,
          next_page_url: null,
          per_page: +localStorage.getItem('pageSize') || 10,
          prev_page_url: null,
          to: 0,
          total: 0
        },
        checkedIds: '',
        pickerOptions: {
          shortcuts: [{
            text: '最近一周',
            onClick (picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近一个月',
            onClick (picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近三个月',
            onClick (picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit('pick', [start, end]);
            }
          }]
        }
      }
    },
    watch: {
      loading (val, old) {
        if (val) {
          if (!old && !NProgress.isStarted()) NProgress.start();
        } else {
          NProgress.done();
        }
      }
    },
    mounted () {
      this.getList();
    },
    methods: {
      getList () {
        this.loading = true;
        this.where.current_page = this.list.current_page;
        this.where.per_page = this.list.per_page;
        if (this.filterType !== null) {
          this.where.filterType = this.filterType;
        }
        if (this.userInfo) {
          this.where.user_id = this.userInfo.id;
        }
        getLogList(this.where).then(res => {
          this.list = res.data;
          this.loading = false;
        });
      },
      handleSearch () {
        const where = { search: this.search.type, val: this.search.value };
        if (this.date_range && this.date_range[0] && this.date_range[1]) {
          where.start_at = this.date_range[0].format('yyyy-MM-dd');
          where.end_at = this.date_range[1].format('yyyy-MM-dd');
        } else {
          where.start_at = null;
          where.end_at = null;
        }
        Object.assign(this.where, where);
        this.getList();
      },
      handleFilterChange (obj) {
        const where = {};
        Object.assign(this.where, where);
        this.getList();
      },
      onChangePage () {
        this.getList();
      },
      onChangePageSize (per_page) {
        localStorage.setItem('pageSize', per_page);
        this.list.per_page = per_page;
        this.getList();
      }
    }
  }
</script>


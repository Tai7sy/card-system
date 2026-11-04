<template>
  <div :class="{'app-container':!inbox}" ref="container">

    <el-collapse-transition>
      <div v-show="showAddBox" class="apply-box">
        <el-button icon="el-icon-close" type="danger" @click="onBoxSubmit(false)" size="small"
                   style="float: right; margin: 10px 15px;"/>
        <el-card class="my-card" v-if="showAddBox || addBoxLoaded">
          <pay-way-box :in-box="true" :info="editInfo" :type="type" @submit="onBoxSubmit"/>
        </el-card>
      </div>
    </el-collapse-transition>

    <!--工具条-->
    <el-col :span="24" class="no-bottom-form">
      <el-form :inline="true" class="list-pre-table">
        <el-form-item class="mobile-full-width">
          <el-input placeholder="请输入" v-model="search.value" size="small">
            <el-select v-model="search.type" slot="prepend" placeholder="搜索类型" value=""
                       size="small" style="width:90px">
              <el-option label="ID" value="id"/>
              <el-option :label="type===PAYWAY_TYPE.SHOP?'名称':'payway'" value="name"/>
            </el-select>
          </el-input>
        </el-form-item>
        <el-form-item>
          <el-button @click="handleSearch" size="small">搜索</el-button>
        </el-form-item>

        <el-form-item class="right-toolbox">
          <el-link @click="handleAdd">
            <icon name="regular/plus"/>&nbsp;添加
          </el-link>
        </el-form-item>
      </el-form>
    </el-col>
    <el-table :data="list.data" v-loading="loading" stripe border class="list-table"
              @filter-change="handleFilterChange"
              @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">

      

      <el-table-column prop="name" :label="type===PAYWAY_TYPE.SHOP?'名称':'payway'" min-width="100" show-overflow-tooltip>
        <template slot-scope="scope">
          <span class="text-nowrap">{{scope.row.name}}</span>
        </template>
      </el-table-column>

      <el-table-column prop="comment" label="备注" min-width="120" show-overflow-tooltip/>

      <el-table-column prop="sort" label="排序" width="70" v-if="type===PAYWAY_TYPE.SHOP">
        <template slot-scope="scope">
          <input v-if="typeof(scope.row.sort) === 'string'" size="small" class="table-input"
                 v-model="scope.row.sort"
                 @blur="handleSetSort(scope.row)" @keyup.enter="handleSetSort(scope.row)" title="排序"/>
          <a v-if="typeof(scope.row.sort) === 'number'" class="line-btn"
             @click="()=>{scope.row.sort_old = scope.row.sort; scope.row.sort = scope.row.sort+''}">
            {{scope.row.sort}}
          </a>
        </template>
      </el-table-column>

      <el-table-column prop="enabled" label="启用" width="100" v-if="type===PAYWAY_TYPE.SHOP"
                       column-key="enabled"
                       :filters="enabledFilter"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <check-box-group-bin v-model="scope.row.enabled" size="mini" :items="enabledCheckbox"
                               @change="handlePaywayEnable(scope.row)"/>
        </template>
      </el-table-column>

      <el-table-column prop="enabled" label="启用" width="60" v-if="type===PAYWAY_TYPE.API"
                       column-key="enabled"
                       :filters="enabledFilter"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <el-switch v-model="scope.row.enabled" @change="handlePayEnable(scope.row)"
                     :active-value="1" :inactive-value="0" active-color="#4cd964"/>
        </template>
      </el-table-column>

      <el-table-column label="操作" width="50" fixed="right">
        <template slot-scope="scope">
          <a class="icon-btn" @click="handleEdit(scope.row)" style="font-size: 14px">
            <icon name="light/pencil"/>
          </a>
          <a class="icon-btn" @click="handleDelete(scope.row)">
            <icon name="light/trash-alt"/>
          </a>
        </template>
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
  import NProgress from 'nprogress'
  import {
    deleteAction,
    enable as enablePayway,
    getList as getPayWayList,
    TYPE as PAYWAY_TYPE,
    PAYWAY_ENABLED,
    sort as sortPayway
  } from '~/api/payway'
  import PayWayBox from './_edit.vue'
  import CheckBoxGroupBin from '~/components/CheckBoxGroupBin';

  export default {
    props: {
      inbox: {
        type: Boolean,
        default: false
      },
      type: {
        type: Number,
      }
    },
    components: {
      PayWayBox, CheckBoxGroupBin
    },
    data() {
      return {
        loading: false,
        showAddBox: false,
        addBoxLoaded: false,
        PAYWAY_TYPE,
        PAYWAY_ENABLED,
        search: {
          type: 'name',
          value: ''
        },
        where: {
          type: this.type
        },
        list: {
          current_page: 1,
          data: [],
          from: 1,
          last_page: 1,
          next_page_url: null,
          per_page: +localStorage.getItem('pageSize') || 20,
          prev_page_url: null,
          to: 0,
          total: 0
        },
        checkedIds: '',
        enabledFilter: (() => {

          if (this.type === PAYWAY_TYPE.SHOP) {
            const filtered = [];
            for (const key in PAYWAY_ENABLED) {
              if (Object.prototype.hasOwnProperty.call(PAYWAY_ENABLED, key) && typeof PAYWAY_ENABLED[key] === 'object') {
                filtered.push({
                  text: PAYWAY_ENABLED[key].name,
                  value: PAYWAY_ENABLED[key].id,
                  type: PAYWAY_ENABLED[key].type
                })
              }
            }
            return filtered;
          } else {
            return [
              { text: '禁用', value: 0, type: 'info' },
              { text: '电脑', value: PAYWAY_ENABLED.PC.id, type: 'primary' },
              { text: '手机', value: PAYWAY_ENABLED.MOBILE.id, type: 'primary' },
              { text: '电脑+手机', value: PAYWAY_ENABLED.PC.id | PAYWAY_ENABLED.MOBILE.id, type: 'primary' },
            ]
          }
        })(),
        enabledCheckbox: [
          {
            text: '电脑',
            value: PAYWAY_ENABLED.PC.id,
          },
          {
            text: '手机',
            value: PAYWAY_ENABLED.MOBILE.id,
          }
        ],
        editInfo: null,
      }
    },
    watch: {
      loading(val, old) {
        if (val) {
          if (!old && !NProgress.isStarted()) NProgress.start();
        } else {
          NProgress.done();
        }
      }
    },
    mounted() {
      this.getList();
    },
    methods: {
      getList() {
        this.loading = true;
        this.where.current_page = this.list.current_page;
        this.where.per_page = this.list.per_page;

        getPayWayList(this.where).then(res => {
          this.list = res.data;
          this.loading = false;
        });
      },
      handleSearch() {
        const where = { search: this.search.type, val: this.search.value };
        Object.assign(this.where, where);
        this.getList();
      },
      handleFilterChange(obj) {
        const where = {};
        if (obj['enabled']) {
          where.enabled = obj['enabled'].join(',');
        } else {
          where.enabled = null;
        }
        Object.assign(this.where, where);
        this.getList();
      },
      handleSetSort(row) {
        console.log('handleSetSort', row);
        if (parseInt(row.sort) === row.sort_old) {
          row.sort = parseInt(row.sort);
          return;
        }
        this.loading = true;
        sortPayway(row.id, row.sort).then(() => {
          this.$notify({ title: '操作成功', message: '已修改排序', type: 'success' });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      },
      handlePaywayEnable(row) {
        enablePayway(row.id, row.enabled)
      },
      onChangePage() {
        this.getList();
      },
      onChangePageSize(per_page) {
        localStorage.setItem('pageSize', per_page);
        this.list.per_page = per_page;
        this.getList();
      },
      handleDisable(row) {
        const name = row.name;
        this.$confirm('禁用支付方式<' + name + '>?', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enablePayway(row.id, 0).then(() => {
            this.$notify({ title: '操作成功', message: '支付方式<' + name + '>已禁用', type: 'info' });
            row.enabled = 0;
            this.loading = false;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleEnable(row) {
        const name = row.name;
        this.$confirm('启用支付方式<' + name + '>?', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enablePayway(row.id, 1).then(() => {
            this.$notify({ title: '操作成功', message: '支付方式<' + name + '>已启用', type: 'success' });
            row.enabled = 1;
            this.loading = false;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleDelete(row) {
        const name = row.name;
        this.$confirm('删除支付方式<' + name + '>? 此操作不可恢复', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deleteAction(row.id).then(() => {
            this.$notify({ title: '操作成功', message: '支付方式<' + name + '>已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      async handleEdit(row) {
        window.scrollTo(0, 0);
        if (this.showAddBox) {
          // 已经正在编辑了, 取消掉现有的
          await this.onBoxSubmit(false);
        }
        this.editInfo = row;
        this.addBoxLoaded = true;
        await this.$nextTick();
        this.showAddBox = true;
      },
      async handleAdd() {
        window.scrollTo(0, 0);
        this.addBoxLoaded = true;
        await this.$nextTick();
        this.showAddBox = true;
      },
      async onBoxSubmit (ret) {
        if (ret) this.getList();
        this.showAddBox = false;
        await this.$nextTick();
        this.addBoxLoaded = false;
        this.editInfo = null;
      }
    }
  }
</script>


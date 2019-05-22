<template>
  <div class="app-container" ref="container">
    <!--工具条-->
    <el-col :span="24" class="no-bottom-form" style="padding-bottom: 0px;">
      <el-form :inline="true" class="list-pre-table">
        <el-form-item class="mobile-full-width">
          <el-input placeholder="请输入" v-model="search.value" size="small">
            <el-select v-model="search.type" slot="prepend" placeholder="搜索类型" value=""
                       size="small" style="width:90px">
              <el-option label="ID" value="id"/>
              <el-option label="名称" value="name"/>
              <el-option label="备注" value="comment"/>
              <el-option label="内容" value="content"/>
            </el-select>
          </el-input>
        </el-form-item>

        <el-form-item class="fix-height">
          <el-button @click="handleSearch" size="small">搜索</el-button>
        </el-form-item>

        <el-form-item style="float: right;margin-right: 0">
          <el-button @click="handleAdd" size="small">添加</el-button>
        </el-form-item>
      </el-form>
    </el-col>


    <el-table :data="list" v-loading="loading" stripe border class="list-table"
              @filter-change="handleFilterChange"
              @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
      <!--el-table-column type="selection" width="40"/-->
      <el-table-column prop="id" label="ID" width="70"/>
      <el-table-column prop="name" label="名称" width="150" show-overflow-tooltip>
        <template slot-scope="scope">
          <a class="line-btn" @click="handleEdit(scope.row)">{{scope.row.name}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="way" label="方式" width="90">
        <template slot-scope="scope">
          <a class="line-btn" @click="handleEdit(scope.row)">{{scope.row.way}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="driver" label="驱动" width="90">
        <template slot-scope="scope">
          <a class="line-btn" @click="handleEdit(scope.row)">{{scope.row.driver}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="fee_system" label="系统费率" width="80">
        <template slot-scope="scope">
          <input v-if="typeof(scope.row.fee_system) === 'string'" size="small" class="table-input"
                 v-model="scope.row.fee_system"
                 @blur="handleEditSystemFee(scope.row)" @keyup.enter="handleEditSystemFee(scope.row)" title="系统费率"/>
          <a v-if="typeof(scope.row.fee_system) === 'number'" class="line-btn"
             @click="()=>{scope.row.fee_system_old = scope.row.fee_system; scope.row.fee_system = scope.row.fee_system+''}">{{(scope.row.fee_system*100).toFixed(2)}}%</a>
        </template>
      </el-table-column>

      <el-table-column prop="comment" label="备注" min-width="120" show-overflow-tooltip>
        <template slot-scope="scope">
          <a class="line-btn" @click="handleEditComment(scope.row)">{{scope.row.comment}}</a>
        </template>
      </el-table-column>


      <el-table-column prop="sort" label="排序" width="70">
        <template slot-scope="scope">
          <input v-if="typeof(scope.row.sort) === 'string'" size="small" class="table-input"
                 v-model="scope.row.sort"
                 @blur="handleSetSort(scope.row)" @keyup.enter="handleSetSort(scope.row)" title="排序"/>
          <a v-if="typeof(scope.row.sort) === 'number'" class="line-btn"
             @click="()=>{scope.row.sort_old = scope.row.sort; scope.row.sort = scope.row.sort+''}">{{scope.row.sort}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="enabled" label="启用" width="80"
                       column-key="enabled"
                       :filters="enabledFilter"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <a @click="handleEnable(scope.row)" style="cursor: pointer">
            <el-tag :type="PAYWAY_ENABLED.getEnabled(scope.row.enabled).type" close-transition>
              {{PAYWAY_ENABLED.getEnabled(scope.row.enabled).name}}
            </el-tag>
          </a>
        </template>
      </el-table-column>


      <el-table-column label="操作" width="75" fixed="right">
        <template slot-scope="scope">
          <a class="icon-btn" @click="handleEdit(scope.row)" style="font-size: 14px">
            <icon name="light/pencil"/>
          </a>
          <!--a v-if="scope.row.enabled !== PAYWAY_ENABLED.DISABLED.id"
             class="icon-btn" @click="handleDisable(scope.row)">禁用</a>
          <a v-if="scope.row.enabled === PAYWAY_ENABLED.DISABLED.id"
             class="icon-btn" @click="handleEnable(scope.row)">启用</a-->
          <a class="icon-btn" @click="handleDelete(scope.row)">
            <icon name="light/trash-alt"/>
          </a>
          <a class="icon-btn" @click="handleSeeLink(scope.row)" style="font-size: 14px">
            <icon name="link"/>
          </a>
        </template>
      </el-table-column>

    </el-table>


    <payway-edit v-if="boxAct" :act="boxAct" :info="boxInfo" @close="boxCallback"/>
    <payway-enable v-if="enableInfo" :info="enableInfo" @close="enableCallback"/>
    <payway-edit-comment v-if="editCommentInfo" :info="editCommentInfo" @close="onEditCommentCallback"/>
  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import {
    PAYWAY_ENABLED, getList, deletePayway,
    enable as enablePayway,
    sort as sortPayway,
    fee_system, fee
  } from '~/api/pay'
  import paywayEdit from './_edit.vue'
  import paywayEnable from './_enable.vue'
  import paywayEditComment from './_edit_comment'

  export default {
    components: {
      paywayEdit, paywayEnable, paywayEditComment
    },
    data () {
      return {
        loading: false,
        PAYWAY_ENABLED,
        search: {
          type: 'id',
          value: ''
        },
        date_range: '',
        where: {},
        list: [],
        checkedIds: '',
        enabledFilter: (() => {
          const filtered = [];
          for (const key in PAYWAY_ENABLED) {
            if (PAYWAY_ENABLED.hasOwnProperty(key) && typeof PAYWAY_ENABLED[key] === 'object') {
              filtered.push({
                text: PAYWAY_ENABLED[key].name,
                value: PAYWAY_ENABLED[key].id,
                type: PAYWAY_ENABLED[key].type
              })
            }
          }
          return filtered;
        })(),
        boxAct: null,
        boxInfo: null,
        enableInfo: null,
        editCommentInfo: null,
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
        getList(this.where).then(res => {
          this.list = res.data;
          this.loading = false;
        });
      },
      handleSearch () {
        const where = { search: this.search.type, val: this.search.value };
        Object.assign(this.where, where);
        this.getList();
      },
      handleFilterChange (obj) {
        const where = {};
        if (obj['enabled']) {
          where.enabled = obj['enabled'].join(',');
        } else {
          where.enabled = null;
        }
        Object.assign(this.where, where);
        this.getList();
      },
      handleSetSort (row) {
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
      handleEditSystemFee (row) {
        console.log('handleEditSystemFee', row);
        if (row.fee_system * 1 === row.fee_system_old) {
          row.fee_system = row.fee_system_old * 1;
          return;
        }
        this.loading = true;
        fee_system(row.id, row.fee_system).then(() => {
          this.$notify({ title: '操作成功', message: '已修改系统费率', type: 'success' });
          row.fee_system = row.fee_system * 1;
          this.loading = false;
        }).catch(() => {
          row.fee_system = row.fee_system_old;
          this.loading = false;
        });
      },
      handleEditFee (row) {
        console.log('handleEditFee', row);
        if (row.fee * 1 === row.fee_old) {
          row.fee = row.fee_old * 1;
          return;
        }
        this.loading = true;
        fee(row.id, row.fee).then(() => {
          this.$notify({ title: '操作成功', message: '已修改费率', type: 'success' });
          row.fee = row.fee * 1;
          this.loading = false;
        }).catch(() => {
          row.fee = row.fee_old;
          this.loading = false;
        });
      },
      handleEditComment (row) {
        this.editCommentInfo = row;
      },
      onEditCommentCallback (ret) {
        this.editCommentInfo = null;
        if (ret) this.getList();
      },
      handleDelete (row) {
        this.$confirm('删除选中支付方式? 此操作不可恢复', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deletePayway(row.id).then(() => {
            this.$notify({ title: '操作成功', message: '选中支付方式已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleDisable (row) {
        this.$confirm('禁用选中支付方式? ', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enablePayway(row.id, PAYWAY_ENABLED.DISABLED.id).then(() => {
            this.$notify({ title: '操作成功', message: '选中支付方式已禁用', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleAdd () {
        this.boxAct = 'add';
      },
      handleSeeLink (row) {
        this.$msgbox({
          title: '通知地址',
          message: `前台返回地址: ${window.config.url}/pay/return/${row.id}<br>异步通知回调: ${window.config.url}/pay/notify/${row.id}`,
          dangerouslyUseHTMLString: true
        });
      },
      handleEdit (row) {
        this.boxInfo = row;
        this.boxAct = 'edit';
      },
      boxCallback (ret) {
        this.boxAct = null;
        this.boxInfo = null;
        if (ret) this.getList();
      },
      handleEnable (row) {
        this.enableInfo = row;
      },
      enableCallback (ret) {
        this.enableInfo = null;
        if (ret) this.getList();
      },
    }
  }
</script>



<template>
<div>

  <el-alert class="my-card"
            title="提示"
            type="warning">
    <div>
      <span class="info">配置子渠道之后，您需要配置【前台支付】后，才能在购买页面正常显示。</span>
    </div>
  </el-alert>

  <!--工具条-->
  <el-col :span="24" class="no-bottom-form">
    <el-form :inline="true" class="list-pre-table">
      <el-form-item class="mobile-full-width">
        <el-input placeholder="请输入" v-model="search.value" size="small">
          <el-select v-model="search.type" slot="prepend" placeholder="搜索类型" value=""
                     size="small" style="width:90px">
            <el-option label="ID" value="id"/>
            <el-option label="名称" value="name"/>
            <el-option label="备注" value="comment"/>
          </el-select>
        </el-input>
      </el-form-item>

      <el-form-item>
        <el-button @click="handleSearch" size="small">搜索</el-button>
      </el-form-item>

      <el-form-item class="right-toolbox">
        <el-link @click="handleAdd" size="small">
          <icon name="regular/plus"/>
          添加
        </el-link>
      </el-form-item>
    </el-form>
  </el-col>

  <el-table :data="list" v-loading="loading" stripe border class="list-table"
            @filter-change="handleFilterChange"
            @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">

    <el-table-column type="selection" width="40"/>
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

    <el-table-column prop="driver" label="驱动" width="120">
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
           @click="()=>{scope.row.fee_system_old = scope.row.fee_system; scope.row.fee_system = scope.row.fee_system+''}">
          {{(scope.row.fee_system*100).toFixed(2)}}%
        </a>
      </template>
    </el-table-column>

    

    <el-table-column prop="comment" label="备注" min-width="120" show-overflow-tooltip>
      <template slot-scope="scope">
        <a class="line-btn" @click="editCommentInfo=scope.row">{{scope.row.comment}}</a>
      </template>
    </el-table-column>


    <el-table-column prop="enabled" label="启用" width="60"
                     column-key="enabled"
                     :filters="enabledFilter"
                     filter-placement="bottom">
      <template slot-scope="scope">
        <el-switch v-model="scope.row.enabled" @change="handlePayEnable(scope.row)"
                   :active-value="1" :inactive-value="0" active-color="#4cd964"/>
      </template>
    </el-table-column>


    <el-table-column label="操作" width="90" fixed="right">
      <template slot-scope="scope">
        <a class="icon-btn" @click="handleEdit(scope.row)" title="编辑" style="font-size: 14px">
          <icon name="light/pencil"/>
        </a>
        <a class="icon-btn" @click="handleCopy(scope.row)" title="复制" style="font-size: 14px">
          <icon name="light/copy"/>
        </a>
        <a class="icon-btn" @click="handleDelete(scope.row)" title="删除" style="font-size: 14px">
          <icon name="light/trash-alt"/>
        </a>
        <a class="icon-btn" @click="handleSeeLink(scope.row)" title="查看链接" style="font-size: 14px">
          <icon name="light/link"/>
        </a>
      </template>
    </el-table-column>

  </el-table>


  <el-col :span="24" class='list-after-action' style="padding-top: 8px;text-align: left;">
    <el-form :inline="true" class="no-bottom-form">
      <el-form-item label="选中">
        <el-button size="small" @click="checkDelete">删除</el-button>
        <el-button size="small" @click="checkEnable">启用</el-button>
        <el-button size="small" @click="checkDisable">禁用</el-button>
      </el-form-item>
    </el-form>
  </el-col>

  <payway-edit v-if="boxAct" :act="boxAct" :info="boxInfo" @close="boxCallback"/>
  <payway-edit-comment v-if="editCommentInfo" :info="editCommentInfo" @close="onEditCommentCallback"/>
</div>
</template>

<script>
import NProgress from 'nprogress';
import { getList, delete_ as deletePay, enable as enablePay, fee_system, fee, edit } from '~/api/pay';
import paywayEdit from './_edit.vue';
import paywayEditComment from './_edit_comment';
export default {
  props: {},
  components: {
    paywayEdit,
    paywayEditComment
  },
  data() {
    return {
      loading: false,
      search: {
        type: 'id',
        value: ''
      },
      date_range: '',
      where: {},
      list: [],
      checkedIds: '',
      enabledFilter: [{
        text: '启用',
        value: 1,
        type: 'primary'
      }, {
        text: '禁用',
        value: 0,
        type: 'info'
      }],
      boxAct: null,
      boxInfo: null,
      editCommentInfo: null,
      url: '',
      url_api: ''
    };
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
      getList(this.where).then(res => {
        const data = res.data;
        this.list = data.list;
        this.url = data.urls.url;
        this.url_api = data.urls.url_api;
        this.loading = false;
      });
    },
    handleSearch() {
      const where = {
        search: this.search.type,
        val: this.search.value
      };
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
    handlePayEnable(row) {
      enablePay(row.id, row.enabled);
    },
    handleEditSystemFee(row) {
      console.log('handleEditSystemFee', row);
      if (row.fee_system * 1 === row.fee_system_old) {
        row.fee_system = row.fee_system_old * 1;
        return;
      }
      this.loading = true;
      fee_system(row.id, row.fee_system).then(() => {
        this.$notify({
          title: '操作成功',
          message: '已修改系统费率',
          type: 'success'
        });
        row.fee_system = row.fee_system * 1;
        this.loading = false;
      }).catch(() => {
        row.fee_system = row.fee_system_old;
        this.loading = false;
      });
    },
    handleEditFee(row) {
      console.log('handleEditFee', row);
      if (row.fee * 1 === row.fee_old) {
        row.fee = row.fee_old * 1;
        return;
      }
      this.loading = true;
      fee(row.id, row.fee).then(() => {
        this.$notify({
          title: '操作成功',
          message: '已修改费率',
          type: 'success'
        });
        row.fee = row.fee * 1;
        this.loading = false;
      }).catch(() => {
        row.fee = row.fee_old;
        this.loading = false;
      });
    },
    onEditCommentCallback(ret) {
      this.editCommentInfo = null;
      if (ret) this.getList();
    },
    handleDelete(row) {
      this.$confirm('删除选中支付方式? 此操作不可恢复', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        deletePay(row.id).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中支付方式已删除',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleDisable(row) {
      this.$confirm('禁用选中支付方式? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        enablePay(row.id, 0).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中支付方式已禁用',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkDelete() {
      if (!this.checkedIds.length) return;
      this.$confirm('批量删除选中支付方式? 此操作不可恢复', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        deletePay(this.checkedIds).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中支付方式已删除',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkEnable() {
      if (!this.checkedIds.length) return;
      this.$confirm('批量启用选中支付方式? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        enablePay(this.checkedIds, 1).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中支付方式已启用',
            type: 'success'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkDisable() {
      if (!this.checkedIds.length) return;
      this.$confirm('批量禁用选中支付方式? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        enablePay(this.checkedIds, 0).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中支付方式已禁用',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleAdd() {
      this.boxAct = 'add';
    },
    handleSeeLink(row) {
      this.$msgbox({
        title: '通知地址',
        message: `前台返回地址: ${this.url}/pay/return/${row.id}<br>异步通知回调: ${this.url_api}/pay/notify/${row.id}`,
        dangerouslyUseHTMLString: true
      });
    },
    handleEdit(row) {
      this.boxInfo = row;
      this.boxAct = 'edit';
    },
    boxCallback(ret) {
      this.boxAct = null;
      this.boxInfo = null;
      if (ret) this.getList();
    },
    handleCopy(row) {
      this.$confirm('复制此支付方式? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        const tmp = Object.assign({}, row);
        delete tmp.id;
        tmp.act = 'add';
        tmp.name += '-副本';
        edit(tmp).then(() => {
          this.getList();
        }).finally(() => {
          this.loading = false;
        });
      });
    }
  }
};
</script>



<template>
<div :class="{'app-container':!userInfo}" ref="container">

  <el-collapse-transition>
    <div v-show="showAddBox" class="apply-box">
      <el-button icon="el-icon-close" type="danger" @click="showAddBox = false" size="small"
                 style="float: right; margin: 10px 15px;"/>
      <el-card class="my-card" v-if="showAddBox || addBoxLoaded">
        <card-add :in-box="true" :userInfo="userInfo" :category_id="where.category_id" :product_id="where.product_id"
                  @submit="onAdd"/>
      </el-card>
    </div>
  </el-collapse-transition>

  <!--工具条-->
  <el-col :span="24" class="no-bottom-form">
    <el-form :inline="true" class="list-pre-table">
      <el-form-item class="mobile-full-width">
        <el-select v-model="where.category_id" filterable size="small" class="mobile-full-width"
                   @change="getProducts" no-data-text="无分类数据">
          <el-option label="全部分类" :value="-1"/>
          <el-option v-for="item in categories"
                     :key="item.id" :label="item.name" :value="item.id"/>
        </el-select>
        <el-select v-if="where.category_id > 0" v-model="where.product_id" filterable size="small"
                   class="mobile-full-width"
                   @change="getList">
          <el-option label="全部商品" :value="-1"/>
          <el-option v-for="item in products" :value="item.id" :key="item.id" :label="item.name"/>
        </el-select>
      </el-form-item>

      <el-form-item class="mobile-full-width">
        <el-input placeholder="请输入" v-model="search.value" size="small">
          <el-select v-model="search.type" slot="prepend" placeholder="搜索类型" value=""
                     size="small" style="width:90px">
            <el-option label="ID" value="id"/>
            <el-option label="卡号" value="card"/>
          </el-select>
        </el-input>
      </el-form-item>
      <el-form-item>
        <el-button @click="handleSearch" size="small">搜索</el-button>
      </el-form-item>

      <el-form-item class="mobile-full-width">
        <el-switch v-if="!onlyTrashed"
                   v-model="where.onlyCanSell"
                   inactive-text="只显示可售"
                   :active-value="1"
                   :inactive-value="0"
                   @change="handleOnlyCanSell">
        </el-switch>
        
      </el-form-item>

      <el-form-item class="right-toolbox">
        <el-link @click="handleExport">
          <icon name="light/file-alt"/>&nbsp;{{'导出('+list.total+')'}}
        </el-link>
        <el-link @click="handleAdd" v-if="!onlyTrashed">
          <icon name="regular/plus"/>&nbsp;添加
        </el-link>
      </el-form-item>
    </el-form>
  </el-col>
  <el-table v-if="!destroying"
            :data="list.data" v-loading="loading" stripe border class="list-table"
            @filter-change="handleFilterChange"
            @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
    <el-table-column type="selection" width="40"/>
    

    

    <el-table-column prop="card" label="卡密" min-width="180" show-overflow-tooltip>
    </el-table-column>

    <el-table-column prop="product" label="所属商品" min-width="120" show-overflow-tooltip>
      <template slot-scope="scope">
        {{scope.row.product?scope.row.product.name:'未知商品#'+scope.row.product_id}}
      </template>
    </el-table-column>

    <el-table-column prop="type" label="类型" width="90"
                     column-key="type"
                     :filters="typeFilter"
                     filter-placement="bottom">
      <template slot-scope="scope">
        <el-tag :type="scope.row.type===CARD_TYPE.TYPE_ONETIME?'info':'info'">
          {{scope.row.type===CARD_TYPE.TYPE_ONETIME?'一次性':'重复卖'}}
        </el-tag>
      </template>
    </el-table-column>

    <el-table-column prop="status" label="售出状态" width="90"
                     column-key="status"
                     :filters="statusFilter"
                     :filtered-value="statusFilterValue"
                     filter-placement="bottom">
      <template slot-scope="scope">
        <el-tag v-if="scope.row.type===CARD_TYPE.TYPE_ONETIME"
                :type="scope.row.status===CARD_STATUS.STATUS_SOLD?'info':'primary'">
          {{scope.row.status===CARD_STATUS.STATUS_SOLD?'已售':'未售'}}
        </el-tag>
        <el-tag v-else :type="scope.row.count_sold>0?'info':'primary'">
          {{scope.row.count_sold+'/'+scope.row.count_all}}
        </el-tag>
      </template>
    </el-table-column>

    <el-table-column v-if="!onlyTrashed" prop="created_at" label="添加时间" width="150"/>

    <el-table-column v-if="onlyTrashed" prop="deleted_at" label="删除时间" width="150"/>

    <el-table-column label="操作" :width="onlyTrashed?'120':'50'" fixed="right">
      <template slot-scope="scope">
        <a v-if="!onlyTrashed" class="icon-btn" @click="handleEdit(scope.row)" style="font-size: 14px">
          <icon name="light/pencil"/>
        </a>
        <a v-if="!onlyTrashed" class="icon-btn" @click="handleDelete(scope.row)">
          <icon name="light/trash-alt"/>
        </a>
        <a v-if="onlyTrashed" class="icon-btn" @click="handleTrashDelete(scope.row)">
          <icon name="light/trash-alt"/>
          销毁
        </a>
        <a v-if="onlyTrashed" class="icon-btn" @click="handleRestore(scope.row)">
          <icon name="regular/undo"/>
          还原
        </a>
      </template>
    </el-table-column>
  </el-table>

  <el-col :span="24" class='list-after-action' style="padding-top: 8px;text-align: left;">
    <el-form class="no-bottom-form">
      <el-form-item label="">
        <el-button v-if="!onlyTrashed" size="small" @click="checkDelete">选中删除</el-button>
        <el-button v-if="onlyTrashed" size="small" @click="checkTrashDelete">选中销毁</el-button>
        <el-button v-if="onlyTrashed" size="small" @click="checkRestore">选中还原</el-button>
        <el-button v-if="onlyTrashed" size="small" type="primary" @click="handleRestoreAll">全部还原</el-button>
        <el-button size="small" type="warning" @click="handleDeleteAll">
          {{onlyTrashed ? '全部销毁' : '全部删除'}}
        </el-button>
      </el-form-item>
    </el-form>

  </el-col>

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

  <card-edit v-if="editInfo" :info="editInfo" @close="editCallback"/>

  <card-export-dialog v-if="CARD_EXPORT_ADVANCED && cardExportDialogVisible"
                      :where="where" :list="list"
                      @close="cardExportDialogVisible = false"/>

</div>
</template>

<script>
import NProgress from 'nprogress';
import { param } from '~/utils/helper';
import { CARD_STATUS, CARD_TYPE, deleteAll, getList as getCardList, moveToTrash, trashDelete, trashRestore, trashRestoreAll } from '~/api/card';
import { getList as getCategoryList } from '~/api/category';
import { getList as getProductList } from '~/api/product';
import cardEdit from './_edit.vue';
import cardAdd from '../add/index';
import CardExportDialog from "./card-export-dialog";

// 是否开启卡密导出功能
const CARD_EXPORT_ADVANCED = true; 

export default {
  props: {
    userInfo: {
      type: Object,
      default: null
    },
    _trashed: {
      default: false
    },
    filter: {}
  },
  components: {
    CardExportDialog,
    cardEdit,
    cardAdd
  },
  data() {
    return {
      loading: false,
      showAddBox: false,
      addBoxLoaded: false,
      destroying: false,
      onlyTrashed: false,
      CARD_TYPE,
      CARD_STATUS,
      CARD_EXPORT_ADVANCED,
      search: {
        type: 'card',
        value: ''
      },
      where: {
        category_id: -1,
        product_id: -1,
        onlyCanSell: 1
      },
      categories: [{
        id: -2,
        name: '加载中'
      }],
      products: [{
        id: -2,
        name: '加载中'
      }],
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
      statusFilter: [{
        text: '未售',
        value: CARD_STATUS.STATUS_NORMAL,
        type: 'primary'
      }, {
        text: '已售',
        value: CARD_STATUS.STATUS_SOLD,
        type: 'info'
      }],
      statusFilterValue: [],
      typeFilter: [{
        text: '重复卖',
        value: CARD_TYPE.TYPE_REPEAT,
        type: 'info'
      }, {
        text: '一次性',
        value: CARD_TYPE.TYPE_ONETIME,
        type: 'info'
      }],
      editInfo: null,
      cardExportDialogVisible: false
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
  created() {
    this.onlyTrashed = this._trashed;
  },
  mounted() {
    let filterType, filterCategoryId, filterProductId;
    if (this.$route.query.category_id && this.$route.query.product_id && this.$route.query.type) {
      filterType = this.$route.query.type;
      filterCategoryId = parseInt(this.$route.query.category_id);
      filterProductId = parseInt(this.$route.query.product_id);
    }
    if (this.filter && this.filter.category_id && this.filter.product_id && this.filter.type) {
      filterType = this.filter.type;
      filterCategoryId = parseInt(this.filter.category_id);
      filterProductId = parseInt(this.filter.product_id);
    }
    if (filterType && filterCategoryId && filterProductId) {
      this.searchBy_(filterType, filterCategoryId, filterProductId);
    } else {
      this.handleSearch();
      this.getCategories();
    }
  },
  methods: {
    searchBy_(type, category_id, product_id) {
      this.where.category_id = category_id;
      this.where.product_id = product_id;
      if (type === 'count') {
        // 库存
        this.where.onlyCanSell = 1;
        this.statusFilterValue.splice(0, this.statusFilterValue.length);
        this.where.status = '';
      } else if (type === 'count_sold') {
        // 已卖
        this.where.onlyCanSell = 0;
        this.statusFilterValue.push(CARD_STATUS.STATUS_SOLD);
        this.where.status = this.statusFilterValue.join(',');
      }
      this.handleSearch();
      this.getCategories();
    },
    handleOnlyCanSell() {
      if (this.where.onlyCanSell) {
        this.statusFilterValue.splice(0, this.statusFilterValue.length);
        this.where.status = '';
      }
      this.handleSearch();
    },
    getCategories() {
      this.loading = true;
      const where = {
        search: 'simple',
        val: 'list'
      };
      if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getCategoryList(where).then(ret => {
        this.categories = ret.data;
        if (this.where.category_id > 0 && this.where.product_id > 0) {
          this.getProducts(this.where.category_id, this.where.product_id);
        }
      });
    },
    getProducts(category_id, product_id = -1) {
      this.where.product_id = product_id;
      this.getList();
      this.products = [];
      const where = {
        search: 'simple',
        val: category_id,
        from: 'card'
      };
      if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getProductList(where).then(ret => {
        this.products = ret.data;
      });
    },
    _generateWhere() {
      this.where.current_page = this.list.current_page;
      this.where.per_page = this.list.per_page;
      this.where.trashed = this.onlyTrashed;
      if (this.userInfo) {
        this.where.user_id = this.userInfo.id;
      }
      return this.where;
    },
    getList() {
      this.loading = true;
      getCardList(this._generateWhere()).then(res => {
        this.list = res.data;
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
    handleExport() {
      this.cardExportDialogVisible = true;
    },
    handleFilterChange(obj) {
      const where = {};
      if (obj['status']) {
        where.status = obj['status'].join(',');
      } else {
        where.status = null;
      }
      if (obj['type']) {
        where.type = obj['type'].join(',');
      } else {
        where.type = null;
      }
      Object.assign(this.where, where);
      this.getList();
    },
    onChangePage() {
      this.getList();
    },
    onChangePageSize(per_page) {
      localStorage.setItem('pageSize', per_page);
      this.list.per_page = per_page;
      this.getList();
    },
    handleDelete(row) {
      const card = row.card;
      this.$confirm('删除卡密' + card + '? 您可以稍后在回收站查看', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        moveToTrash(row.id).then(() => {
          this.$notify({
            title: '操作完成',
            message: '卡密' + card + '已删除',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleTrashDelete(row) {
      const card = row.card;
      this.$confirm('销毁卡密' + card + '? 此操作不可恢复', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        trashDelete(row.id).then(() => {
          this.$notify({
            title: '操作完成',
            message: '卡密' + card + '已销毁',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleRestore(row) {
      const card = row.card;
      this.$confirm('还原卡密' + card + '? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        trashRestore(row.id).then(() => {
          this.$notify({
            title: '操作完成',
            message: '卡密' + card + '已还原',
            type: 'success'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkDelete() {
      if (!this.checkedIds.length) return;
      this.$confirm('批量删除选中卡密? 您可以稍后在回收站查看', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        moveToTrash(this.checkedIds).then(() => {
          this.$notify({
            title: '操作完成',
            message: '选中卡密已删除',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkTrashDelete() {
      if (!this.checkedIds.length) return;
      this.$confirm('批量销毁选中卡密? 此操作不可恢复', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        trashDelete(this.checkedIds).then(() => {
          this.$notify({
            title: '操作完成',
            message: '选中卡密已销毁',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkRestore() {
      if (!this.checkedIds.length) return;
      this.$confirm('批量还原选中卡密? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        trashRestore(this.checkedIds).then(() => {
          this.$notify({
            title: '操作完成',
            message: '选中卡密已还原',
            type: 'success'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleDeleteAll() {
      this.$confirm('是否' + (this.onlyTrashed ? '清空当前回收站' : '删除当前卡密') + '列表?<br> (当前列表共' + this.list.total + '条)', '警告', {
        type: 'warning',
        dangerouslyUseHTMLString: true
      }).then(() => {
        this.loading = true;
        deleteAll(this._generateWhere()).then(() => {
          this.$notify({
            title: '操作完成',
            message: this.onlyTrashed ? '当前回收站列表已清空' : '当前卡密列表已删除',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleRestoreAll() {
      this.$confirm('是否还原当前回收站列表?<br> (当前列表共' + this.list.total + '条)', '询问', {
        type: 'warning',
        dangerouslyUseHTMLString: true
      }).then(() => {
        this.loading = true;
        trashRestoreAll(this._generateWhere()).then(() => {
          this.$notify({
            title: '操作完成',
            message: '当前回收站列表已还原',
            type: 'success'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    async handleAdd() {
      const query = {};
      if (+this.where.category_id > 0) query['category_id'] = +this.where.category_id;
      if (+this.where.product_id > 0) query['product_id'] = +this.where.product_id;
      this.$router.push({
        path: '/admin/product/card/add',
        query
      });
    },
    onAdd() {
      this.getList();
      this.showAddBox = false;
    },
    handleEdit(row) {
      this.editInfo = row;
    },
    editCallback(success) {
      this.editInfo = null;
      if (success) this.getList();
    },
    async toggleTrash() {
      this.onlyTrashed = !this.onlyTrashed;
      this.destroying = true;
      await this.$nextTick();
      this.destroying = false;
      this.getList();
    }
  }
};
</script>



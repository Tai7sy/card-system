<template>
<div :class="{'app-container':!userInfo && !_inBox}" ref="container">

  <el-collapse-transition>
    <div v-show="showAddBox" class="apply-box">
      <el-button icon="el-icon-close" type="danger" @click="onBoxSubmit(false)" size="small"
                 style="float: right; margin: 10px 15px;"/>
      <el-card class="my-card" v-if="showAddBox || addBoxLoaded">
        <product-box :in-box="true" :userInfo="userInfo" :info="editInfo" :_categories="categories"
                     @refreshCategories="getCategories"
                     @changeTab="v => $emit('changeTab', v)"
                     @submit="onBoxSubmit"/>
      </el-card>
    </div>
  </el-collapse-transition>

  <!--工具条-->
  <el-col :span="24" class="no-bottom-form">
    <el-form :inline="true" class="list-pre-table">
      <el-form-item v-if="loadCategory"
                    class="mobile-full-width">
        <el-select v-model="where.category_id" filterable
                   placeholder="商品分类" size="small" class="mobile-full-width"
                   @change="getList" no-data-text="无分类数据">
          <el-option label="全部" :value="-1"/>
          <el-option v-for="item in categories"
                     :key="item.id" :label="item.name" :value="item.id"/>
        </el-select>
      </el-form-item>

      <el-form-item class="mobile-full-width">
        <el-input placeholder="请输入" v-model="search.value" size="small">
          <el-select v-model="search.type" slot="prepend" placeholder="搜索类型"
                     size="small" style="width:90px">
            <el-option label="ID" value="id"/>
            
            <el-option label="名称" value="name"/>
            <el-option label="分类ID" value="category_id"/>
          </el-select>
        </el-input>
      </el-form-item>
      <el-form-item>
        <el-button @click="handleSearch" size="small">搜索</el-button>
      </el-form-item>

      <el-form-item class="right-toolbox">
        
        <el-link @click="handleAdd">
          <icon name="regular/plus"/>&nbsp;添加商品</el-link>
      </el-form-item>

      <span style="clear: both"></span>
    </el-form>
  </el-col>
  <el-table :data="list.data" v-loading="loading" stripe border class="list-table"
            @filter-change="handleFilterChange"
            @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
    <el-table-column type="selection" width="40"/>
    

    

    <el-table-column prop="name" label="商品名称(点击查看)" min-width="180" show-overflow-tooltip>
      <template slot-scope="scope">
        <a class="line-btn" @click="showProductInfo(scope.row)"
        >{{scope.row.name}}
          <el-tag class="el-tag-label" v-if="scope.row.retail_from_id">分销商品</el-tag>
          <el-tag class="el-tag-label" v-if="scope.row.delivery === DELIVERY.MANUAL">手动发货</el-tag>
        </a>
      </template>
    </el-table-column>

    <el-table-column prop="category" label="分类(点击修改)"
                     min-width="120" show-overflow-tooltip>
      <template slot-scope="scope">
        <a @click="handleEditCategory(scope.row)"
           class="line-btn">{{scope.row.category?scope.row.category.name:'未知#'+scope.row.category_id}}</a>
      </template>
    </el-table-column>

    

    <el-table-column prop="count_sold" label="已售" width="80">
      <template slot-scope="scope">
        <span v-if="scope.row.retail_from_id">{{scope.row.count_sold}}</span>
        <a class="line-btn" v-else @click="goCards('count_sold',scope.row)">{{scope.row.count_sold}}</a>
      </template>
    </el-table-column>

    <el-table-column prop="count" label="库存" width="90">
      <template slot="header">
        <a class="icon-btn" style="font-weight: normal" title="刷新库存" @click="handleCountSync">
          库存&nbsp;<icon name="light/sync"/>
        </a>
      </template>
      <template slot-scope="scope">
        <span v-if="scope.row.retail_from_id">{{scope.row.count}}</span>
        <div v-else-if="scope.row.delivery === DELIVERY.MANUAL">
          <input v-if="typeof(scope.row.count) === 'string'"
                 size="small" class="table-input"
                 v-model="scope.row.count"
                 @blur="handleSetCount(scope.row)" @keyup.enter="handleSetCount(scope.row)" title="库存"/>
          <a v-if="typeof(scope.row.count) === 'number'" class="line-btn"
             @click="()=>{scope.row.count_old = scope.row.count; scope.row.count = scope.row.count+''}">{{scope.row.count}}</a>

        </div>
        <a class="line-btn" v-else @click="goCards('count',scope.row)">{{scope.row.count}}</a>
      </template>
    </el-table-column>

    <el-table-column prop="enabled" label="上架" width="70"
                     column-key="enabled"
                     :filters="enabledFilter"
                     filter-placement="bottom">
      <template slot-scope="scope">
        <el-switch v-model="scope.row.enabled" @change="handleProductEnabled(scope.row)"
                   :active-value="1" :inactive-value="0" active-color="#4cd964"/>
      </template>
    </el-table-column>

    <el-table-column prop="sort" label="排序" width="70">
      <template slot-scope="scope">
        <input v-if="typeof(scope.row.sort) === 'string'"
               size="small" class="table-input"
               v-model="scope.row.sort"
               @blur="handleSetSort(scope.row)" @keyup.enter="handleSetSort(scope.row)" title="排序"/>
        <a v-if="typeof(scope.row.sort) === 'number'" class="line-btn"
           @click="()=>{scope.row.sort_old = scope.row.sort; scope.row.sort = scope.row.sort+''}">{{scope.row.sort}}</a>
      </template>
    </el-table-column>

    <el-table-column label="操作" width="80" fixed="right">
      <template slot-scope="scope">
        <a class="icon-btn" @click="showUrl(scope.row)">直链</a>
        <a class="icon-btn" @click="handleEdit(scope.row)">
          <icon name="light/pencil"/>
        </a>
        <a class="icon-btn" @click="handleDelete(scope.row)">
          <icon name="light/trash-alt"/>
        </a>
      </template>
    </el-table-column>
  </el-table>

  <el-col :span="24" class='list-after-action' style="padding-top: 8px;text-align: left;">
    <el-form :inline="true" class="no-bottom-form">
      <el-form-item label="选中">
        <el-button size="small" @click="checkDelete">删除</el-button>
        <el-button size="small" @click="checkEnable">上架</el-button>
        <el-button size="small" @click="checkDisable">下架</el-button>
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

  <!--product-edit v-if="editInfo" :info="editInfo" @close="editCallback"/-->
  <product-category :categories="categories" v-if="editCategory" :info="editCategory"
                    :loadCategory="loadCategory"
                    @close="editCategoryCallback"/>
</div>
</template>

<script>
import NProgress from 'nprogress';
import { DELIVERY, getList as getProductList, deleteAction, enable, sort, setCount, countSync } from '~/api/product';
import { getList as getCategoryList } from '~/api/category';
import productCategory from './_category';
import productBox from '../add/index';
import { renderDelta, randomString, htmlspecialchars } from '~/utils/helper';
export default {
  props: {
    userInfo: {
      type: Object,
      default: null
    },
    filter: {
      default: null
    },
    _inBox: {}
  },
  components: {
    productBox,
    productCategory
  },
  data() {
    return {
      loading: false,
      showAddBox: false,
      addBoxLoaded: false,
      DELIVERY,
      search: {
        type: 'name',
        value: ''
      },
      where: {
        category_id: -1
      },
      categories: [{
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
      enabledFilter: [{
        text: '上架',
        value: 1,
        type: 'primary'
      }, {
        text: '未上架',
        value: 0,
        type: 'info'
      }],
      editInfo: null,
      editCategory: null
    };
  },
  computed: {
    loadCategory() {
      return true;
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
    if (this.$route.query.category_id) {
      this.where.category_id = this.$route.query.category_id;
    }
    if (this.filter && this.filter.category_id) {
      this.where.category_id = this.filter.category_id;
    }
    this.handleSearch();
    if (this.loadCategory) {
      this.getCategories();
    } else {
      this.categories = [];
    }
  },
  methods: {
    searchByCategory(category_id) {
      this.where.category_id = category_id;
      this.handleSearch();
    },
    getCategories() {
      const where = {
        search: 'simple',
        val: 'list'
      };
      if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getCategoryList(where).then(ret => {
        this.categories = ret.data;
      });
    },
    getList() {
      this.loading = true;
      this.where.current_page = this.list.current_page;
      this.where.per_page = this.list.per_page;
      if (this.userInfo) {
        this.where.user_id = this.userInfo.id;
      }
      getProductList(this.where).then(res => {
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
    onChangePage() {
      this.getList();
    },
    onChangePageSize(per_page) {
      localStorage.setItem('pageSize', per_page);
      this.list.per_page = per_page;
      this.getList();
    },
    showProductInfo(row) {
      const id = randomString(8);
      let message = `
          <p>商品名称:&nbsp;${htmlspecialchars(row.name)}${row.retail_from_id ? '<span class="el-tag el-tag-label">分销商品</span>' : ''}</p>
          <p>商品分类:&nbsp;${row.category ? htmlspecialchars(row.category.name) : '未知#' + row.category_id}</p>
          <p>商品介绍:&nbsp;<a class="expand-description" href="javascript:$('#${id}').toggle()">查看</a>
            <div id="${id}" style="border: 1px solid #eee;display: none">${renderDelta(row.description)}</div>
          </p>
          <p>售价:&nbsp;￥${(row.price / 100).toFixed(2)}</p>
          <p>成本价:&nbsp;￥${(row.cost / 100).toFixed(2)}</p>
          <p>商品链接:&nbsp;<br><a class="icon-btn" href="${row.url}" target="_blank">${row.url}</a>
          &nbsp;${row.password_open ? '密码:&nbsp;' + htmlspecialchars(row.password) : ''}</p>
          `;
      if (row.retail_open) {
        message += `
          <p>对接密钥:&nbsp;<br><input class="small-input" readonly value="${htmlspecialchars(row.retail_code)}" onclick="select();" style="width: 260px"></p>
          `;
      }
      this.$msgbox({
        title: '商品详情',
        message,
        dangerouslyUseHTMLString: true,
        customClass: 'ann-box' // 假装是公告
      });
    },
    showUrl(row) {
      let message = `<a class="icon-btn" href="${row.url}" target="_blank">${row.url}</a>`;
      if (row.password_open) {
        message += '<br>密码: ' + htmlspecialchars(row.password);
      }
      this.$msgbox({
        title: '单独链接',
        message,
        dangerouslyUseHTMLString: true
      });
    },
    goCards(type, row) {
      this.$router.push({
        path: '/admin/product/card/list',
        query: {
          type,
          category_id: row.category_id,
          product_id: row.id
        }
      });
    },
    handleProductEnabled(row) {
      if (row.retail_from_id) {
        // 后台也会判断

        if (!row.retail_from) {
          // 供货商品已经删除
          this.$msgbox({
            message: '当前商品为分销商品，供货商已将商品删除, 因此您无法上架此商品',
            title: '操作失败',
            type: 'error'
          });
          row.enabled = false;
          return false;
        }
        if (!row.retail_from.enabled) {
          // 供货商品已经删除
          this.$msgbox({
            message: '当前商品为分销商品，供货商已将商品下架, 因此您无法上架此商品',
            title: '操作失败',
            type: 'error'
          });
          row.enabled = false;
          return false;
        }
        if (!row.retail_from.retail_open) {
          // 供货商品已经关闭分销
          this.$msgbox({
            message: '当前商品为分销商品，供货商已关闭商品分销, 因此您无法上架此商品',
            title: '操作失败',
            type: 'error'
          });
          row.enabled = false;
          return false;
        }
      }
      enable(row.id, row.enabled);
    },
    handleSetSort(row) {
      if (parseInt(row.sort) === row.sort_old) {
        row.sort = parseInt(row.sort);
        return;
      }
      this.loading = true;
      sort(row.id, row.sort).then(() => {
        this.getList();
      }).catch(() => {
        this.loading = false;
      });
    },
    handleSetCount(row) {
      if (parseInt(row.count) === row.count_old) {
        row.count = parseInt(row.count);
        return;
      }
      this.loading = true;
      setCount(row.id, row.count).finally(() => {
        row.count = parseInt(row.count);
        this.loading = false;
      });
    },
    handleDelete(row) {
      const name = row.name;
      this.$confirm('删除商品<' + htmlspecialchars(name) + '>? <br>选中商品的卡密将会一起删除<br>' + '此操作不可恢复', '提示', {
        type: 'warning',
        dangerouslyUseHTMLString: true
      }).then(() => {
        this.loading = true;
        deleteAction(row.id).then(() => {
          this.$notify({
            title: '操作成功',
            message: '商品<' + name + '>已删除',
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
      this.$confirm("批量删除选中商品? <br>选中商品的卡密将会一起删除<br>此操作不可恢复", '提示', {
        type: 'warning',
        dangerouslyUseHTMLString: true
      }).then(() => {
        this.loading = true;
        deleteAction(this.checkedIds).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中商品已删除',
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
      this.$confirm('批量上架选中商品? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        enable(this.checkedIds, 1).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中商品已上架',
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
      this.$confirm('批量下架选中商品? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        enable(this.checkedIds, 0).then(() => {
          this.$notify({
            title: '操作成功',
            message: '选中商品已下架',
            type: 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleCountSync() {
      this.$notify.info({
        title: '请稍等',
        message: '刷新库存可能会消耗较长时间',
        duration: 2000
      });
      this.loading = true;
      countSync(this.userInfo ? this.userInfo.id : null).then(() => {
        this.$notify.success({
          title: '成功',
          message: '商品库存已刷新',
          duration: 2000
        });
        this.getList();
      }).catch(() => {
        this.loading = false;
      });
    },
    async handleAdd() {
      this.$router.push('/admin/product/add');
    },
    handleRetailAdd() {
      if (!this.userInfo) {
        return this.$alert('请在商户列表选择商户后添加', {
          type: 'error'
        });
      }
      this.$emit('changeTab', {
        to: 'product_retail'
      });
    },
    async onBoxSubmit(ret) {
      if (ret) this.getList();
      this.showAddBox = false;
      await this.$nextTick();
      this.addBoxLoaded = false;
      this.editInfo = null;
    },
    goUserDetail(user_id) {},
    async handleEdit(row) {
      if (row.retail_from_id) {
        // 后台会判断已经删除 or 关闭分销 是否可以编辑

        if (!row.retail_from) {
          // 供货商品已经删除
          this.$msgbox({
            message: '当前商品为分销商品，供货商已将商品删除 因此您无法编辑此商品',
            title: '操作失败',
            type: 'error'
          });
          return;
        }
        if (!row.retail_from.retail_open) {
          // 供货商品已经关闭分销
          this.$msgbox({
            message: '当前商品为分销商品，供货商已关闭商品分销, 因此您无法编辑此商品',
            title: '操作失败',
            type: 'error'
          });
          return;
        }
      }
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
    // @deprecated
    editCallback(ret) {
      this.editInfo = null;
      if (ret) this.getList();
    },
    handleEditCategory(row) {
      this.editCategory = row;
    },
    editCategoryCallback(payload) {
      if (payload.isAddCategory) {
        this.categories.push(payload.category);
      }
      if (payload && payload.category) {
        this.editCategory.category_id = payload.category.id;
        this.editCategory.category = payload.category;
      }
      this.editCategory = null;
    }
  }
};
</script>

<style lang="scss">
  @import "../list/info-msgbox";

  .expand-description {
    color: #58B7FF !important;
    font-size: 14px;
    padding: 0;

    &:hover {
      text-decoration: underline;
    }
  }
</style>


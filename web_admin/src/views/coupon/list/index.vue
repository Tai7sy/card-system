<template>
  <div :class="{'app-container':!userInfo}" ref="container">

    <el-collapse-transition>
      <div v-show="showAddBox" class="apply-box">
        <el-button icon="el-icon-close" @click="showAddBox = false" size="small"
                   style="float: right; margin: 10px 15px;"/>
        <el-card class="my-card" v-if="showAddBox || addBoxLoaded">
          <coupon-add :in-box="true" :userInfo="userInfo" @submit="onAdd"/>
        </el-card>
      </div>
    </el-collapse-transition>

    <!--工具条-->
    <el-col :span="24" class="no-bottom-form" style="padding-bottom: 0px;">
      <el-form :inline="true" class="list-pre-table">
        <el-form-item class="mobile-full-width">
          <el-select v-model="where.category_id" size="small" class="mobile-full-width"
                     @change="getProducts" no-data-text="无分类数据">
            <el-option label="全部" :value="-1"/>
            <el-option v-for="item in categories"
                       :key="item.id" :label="item.name" :value="item.id"/>
          </el-select>
          <el-select v-if="where.category_id > 0" v-model="where.product_id" size="small" class="mobile-full-width"
                     @change="getList">
            <el-option label="全部" :value="-1"/>
            <el-option v-for="item in products" :value="item.id" :key="item.id" :label="item.name"/>
          </el-select>
        </el-form-item>

        <el-form-item class="mobile-full-width">
          <el-input placeholder="请输入" v-model="search.value" size="small">
            <el-select v-model="search.type" slot="prepend" placeholder="搜索类型" value=""
                       size="small" style="width:90px">
              <el-option label="ID" value="id"/>
              <el-option label="优惠券" value="coupon"/>
              <el-option label="备注" value="remark"/>
            </el-select>
          </el-input>
        </el-form-item>
        <el-form-item class="fix-height">
          <el-button @click="handleSearch" size="small">搜索</el-button>
        </el-form-item>

        <el-form-item style="float: right;margin-right: 0;">
          <a class="icon-btn" @click="handleAdd">
            <icon name="plus"/>&nbsp;添加</a>
        </el-form-item>
      </el-form>
    </el-col>
    <el-table :data="list.data" v-loading="loading" stripe border class="list-table"
              @filter-change="handleFilterChange"
              @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
      <el-table-column type="selection" width="40"/>
      <el-table-column prop="id" label="ID" width="70" v-if="_project === 'admin'"/>
      <el-table-column prop="user_id" label="商户ID" width="70" v-if="_project === 'admin' && !userInfo"/>

      <el-table-column prop="coupon" label="优惠券" min-width="180" show-overflow-tooltip>
      </el-table-column>

      <el-table-column prop="coupon" label="优惠额" width="80">
        <template slot-scope="scope">
          {{scope.row.discount_type === couponDiscountType.AMOUNT?
          ('￥'+(scope.row.discount_val/100).toFixed(2)): (scope.row.discount_val + '%')}}
        </template>
      </el-table-column>

      <el-table-column prop="use_for" label="适用商品" min-width="180" show-overflow-tooltip>
        <template slot-scope="scope">
          {{renderUseFor(scope.row)}}
        </template>
      </el-table-column>

      <el-table-column prop="type" label="类型" width="90"
                       column-key="type"
                       :filters="typeFilter"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <el-tag :type="scope.row.type===couponType.TYPE_ONETIME?'info':'info'" close-transition>
            {{scope.row.type===couponType.TYPE_ONETIME?'一次性':'重复使用'}}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="status" label="使用状态" width="90"
                       column-key="status"
                       :filters="statusFilter"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.type===couponType.TYPE_ONETIME"
                  :type="scope.row.status===couponStatus.STATUS_USED?'info':'primary'" close-transition>
            {{scope.row.status===couponStatus.STATUS_USED?'已用':'未用'}}
          </el-tag>
          <el-tag v-else :type="scope.row.count_used>0?'info':'primary'" close-transition>
            {{scope.row.count_used+'/'+scope.row.count_all}}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column prop="expire_at" label="过期时间" width="150">
        <template slot-scope="scope">
          <span :class="(new Date(scope.row.expire_at)) < new Date() ? 'amber-text' : ''">{{ scope.row.expire_at }}</span>
        </template>
      </el-table-column>


      <el-table-column label="操作" width="50" fixed="right">
        <template slot-scope="scope">
          <!--a class="icon-btn" @click="handleDisable(scope.row)" v-if="scope.row.enabled">禁用</a>
          <a class="icon-btn" @click="handleEnable(scope.row)" v-else="scope.row.enabled">启用</a-->
          <a class="icon-btn" @click="handleEdit(scope.row)" style="font-size: 14px">
            <icon name="pencil"/>
          </a>
          <a class="icon-btn" @click="handleDelete(scope.row)">
            <icon name="trash-o"/>
          </a>
        </template>
      </el-table-column>
    </el-table>

    <el-col :span="24" class='list-after-action' style="padding-top: 8px;text-align: left;">
      <el-form :inline="true" class="no-bottom-form">
        <el-form-item label="选中">
          <el-button size="small" @click="checkDelete">删除</el-button>
        </el-form-item>
      </el-form>
    </el-col>

    <el-col :span="24" class='list-after-action' style="padding-top: 12px;text-align: right;">
      <el-pagination
        v-if='list.total>0'
        class='pagination'
        :page-size="20"
        :page-sizes="[10, 20, 30, 40, 50, 100]"
        :total="list.total"
        :current-page.sync='list.current_page'
        layout="total, sizes, prev, pager, next, jumper"
        @current-change='onChangePage'
        @size-change='onChangePageSize'>
      </el-pagination>
    </el-col>

    <coupon-edit v-if="editInfo" :info="editInfo" @close="editCallback"></coupon-edit>

  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import {
    getList as getCardList,
    couponType, couponStatus, couponDiscountType,
    deleteAction, enable
  } from 'api/coupon'
  import { getList as getCategoryList } from 'api/category'
  import { getList as getProductList } from 'api/product'
  import couponEdit from './_edit'
  import couponAdd from '../add/index'

  export default {
    props: {
      userInfo: {
        type: Object,
        default: null
      },
      onlyTrashed: {
        default: false
      },
      _project: {
        default: process.env.PROJECT
      }
    },
    components: {
      couponEdit, couponAdd
    },
    data () {
      return {
        loading: false,
        exporting: false,
        showAddBox: false,
        addBoxLoaded: false,
        couponType,
        couponStatus,
        couponDiscountType,
        search: {
          type: 'coupon',
          value: ''
        },
        where: {
          category_id: -1,
          product_id: -1,
        },
        categories: [{ id: -2, name: '加载中' }],
        products: [{ id: -2, name: '加载中' }],
        list: {
          current_page: 1,
          data: [],
          from: 1,
          last_page: 1,
          next_page_url: null,
          per_page: 20,
          prev_page_url: null,
          to: 0,
          total: 0
        },
        checkedIds: '',
        statusFilter: [
          { text: '未用', value: couponStatus.STATUS_NORMAL, type: 'primary' },
          { text: '已用', value: couponStatus.STATUS_USED, type: 'info' },
        ],
        typeFilter: [
          { text: '重复使用', value: couponType.TYPE_REPEAT, type: 'info' },
          { text: '一次性', value: couponType.TYPE_ONETIME, type: 'info' },
        ],
        editInfo: null,
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
      this.getCategories();
    },
    methods: {
      getCategories () {
        this.loading = true;
        const where = { search: 'simple', val: 'list', };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getCategoryList(where).then(ret => {
          this.categories = ret.data;
          this.getList();
        })
      },
      getProducts (category_id) {
        this.where.product_id = -1;
        this.getList();
        this.products = [];
        const where = { search: 'simple', val: category_id };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getProductList(where).then(ret => {
          this.products = ret.data;
        });
      },
      getList () {
        this.loading = true;
        this.where.current_page = this.list.current_page;
        this.where.per_page = this.list.per_page;
        this.where.trashed = this.onlyTrashed;
        if (this.userInfo) {
          this.where.user_id = this.userInfo.id;
        }
        getCardList(this.where).then(res => {
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
      onChangePage () {
        this.getList();
      },
      onChangePageSize (per_page) {
        this.list.per_page = per_page;
        this.getList();
      },
      renderUseFor (row) {
        if (row.product_id > 0) {
          if (row.product && row.product.name) return '商品：' + row.product.name;
          else return '商品：#' + row.product_id
        } else {
          if (row.category_id > 0) {
            if (row.category && row.category.name) return '分类：' + row.category.name;
            else return '分类：#' + row.category_id
          } else {
            return '店铺通用(商品有规定除外)';
          }
        }
      },
      handleDisable (row) {
        const coupon = row.coupon;
        this.$confirm('禁用优惠券<' + coupon + '>?', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enable(row.id, 0).then(() => {
            this.$notify({ title: '操作成功', message: '优惠券<' + coupon + '>已禁用', type: 'info' });
            row.enabled = 0;
            this.loading = false;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleEnable (row) {
        const coupon = row.coupon;
        this.$confirm('启用优惠券<' + coupon + '>?', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enable(row.id, 1).then(() => {
            this.$notify({ title: '操作成功', message: '优惠券<' + coupon + '>已启用', type: 'success' });
            row.enabled = 1;
            this.loading = false;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleDelete (row) {
        const coupon = row.coupon;
        this.$confirm('删除优惠券' + coupon + '? 您可以稍后在回收站查看', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deleteAction(row.id).then(() => {
            this.$notify({ title: '操作成功', message: '优惠券' + coupon + '已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      checkDelete () {
        if (!this.checkedIds.length) return;
        this.$confirm('批量删除选中优惠券? 您可以稍后在回收站查看', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deleteAction(this.checkedIds).then(() => {
            this.$notify({ title: '操作成功', message: '选中优惠券已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleAdd () {
        if (process.env.PROJECT === 'merchant') {
          this.$router.push('/admin/coupon/add');
        } else {
          window.scrollTo(0, 0);
          this.addBoxLoaded = true;
          this.$nextTick(() => {
            this.showAddBox = !this.showAddBox;
          })
        }
      },
      onAdd () {
        this.getList();
        // this.showAddBox = false; // 优惠券要渲染页面, 不关闭窗口!!
      },
      handleEdit (row) {
        this.editInfo = row;
      },
      editCallback (success) {
        this.editInfo = null;
        if (success) this.getList();
      }
    }
  }
</script>


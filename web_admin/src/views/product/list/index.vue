<template>
  <div :class="{'app-container':!userInfo}" ref="container">

    <el-collapse-transition>
      <div v-show="showAddBox" class="apply-box">
        <el-button icon="el-icon-close" @click="onBoxSubmit(false)" size="small"
                   style="float: right; margin: 10px 15px;"/>
        <el-card class="my-card" v-if="showAddBox || addBoxLoaded">
          <product-add :in-box="true" :userInfo="userInfo" :info="editInfo" @submit="onBoxSubmit"/>
        </el-card>
      </div>
    </el-collapse-transition>

    <!--工具条-->
    <el-col :span="24" class="no-bottom-form" style="padding-bottom: 0px;">
      <el-form :inline="true" class="list-pre-table">
        <el-form-item class="mobile-full-width">
          <el-select v-model="where.category_id" placeholder="商品分类" size="small" class="mobile-full-width"
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

        <span style="clear: both"></span>
      </el-form>
    </el-col>
    <el-table :data="list.data" v-loading="loading" stripe border class="list-table"
              @filter-change="handleFilterChange"
              @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
      <el-table-column type="selection" width="40"/>
      <el-table-column prop="id" label="ID" width="70" v-if="_project === 'admin'"/>
      <el-table-column prop="user_id" label="商户ID" width="70" v-if="_project === 'admin' && !userInfo"/>

      <el-table-column prop="name" label="名称(点击查看商品介绍)" min-width="180" show-overflow-tooltip>
        <template slot-scope="scope">
          <a class="line-btn" @click="showDescription(scope.row)">{{scope.row.name}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="name" label="分类(点击修改)" min-width="100" show-overflow-tooltip>
        <template slot-scope="scope">
          <a @click="handleEditCategory(scope.row)" class="line-btn"
          >{{scope.row.category?scope.row.category.name:'未知#'+scope.row.category_id}}</a>
        </template>
      </el-table-column>

      <!--el-table-column prop="description" label="描述" min-width="180" show-overflow-tooltip>
        <template slot-scope="scope">
          <a class="line-btn" @click="showDescription(scope.row)">{{scope.row.description}}</a>
        </template>
      </el-table-column-->

      <el-table-column prop="count_sold" label="已售" width="80">
        <template slot-scope="scope">
          <a class="line-btn" @click="goCards('count_sold',scope.row)">{{scope.row.count_sold}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="count" label="库存" width="90">
        <template slot-scope="scope">
          <a class="line-btn" @click="goCards('count',scope.row)">{{scope.row.count}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="enabled" label="状态" width="80"
                       column-key="enabled"
                       :filters="enabledFilter"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <el-tag :type="scope.row.enabled===1?'primary':'info'" close-transition>
            {{scope.row.enabled===1?'上架':'下架'}}
          </el-tag>
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

      <el-table-column label="操作" width="130" fixed="right">
        <template slot-scope="scope">
          <a class="icon-btn" @click="handleDisable(scope.row)" v-if="scope.row.enabled">下架</a>
          <a class="icon-btn" @click="handleEnable(scope.row)" v-else="scope.row.enabled">上架</a>
          <a class="icon-btn" @click="handleDomain(scope.row)">直链</a>
          <a class="icon-btn" @click="handleEdit(scope.row)">
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
          <el-button size="small" @click="checkEnable">上架</el-button>
          <el-button size="small" @click="checkDisable">下架</el-button>
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

    <!--product-edit v-if="editInfo" :info="editInfo" @close="editCallback"/-->
    <product-category :categories="categories" v-if="editCategory" :info="editCategory"
                      @close="editCategoryCallback"/>
  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { getList as getProductList, deleteAction, enable, sort } from 'api/product'
  import { getList as getCategoryList } from 'api/category'
  import productEdit from './_edit'
  import productCategory from './_category'
  import productAdd from '../add/index'
  import { renderQuill } from '@/components/QuillEditor'

  export default {
    props: {
      userInfo: {
        type: Object,
        default: null
      },
      filter: {
        default: null
      },
      _project: {
        default: process.env.PROJECT
      },
    },
    components: {
      productEdit, productAdd, productCategory
    },
    data () {
      return {
        loading: false,
        showAddBox: false,
        addBoxLoaded: false,
        search: {
          type: 'name',
          value: ''
        },
        where: {
          category_id: -1,
        },
        categories: [{ id: -2, name: '加载中' }],
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
        enabledFilter: [
          { text: '上架', value: 1, type: 'primary' },
          { text: '下架', value: 0, type: 'info' },
        ],
        editInfo: null,
        editCategory: null,
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
      if (this.$route.query.category_id) {
        this.where.category_id = this.$route.query.category_id;
      }
      if (this.filter && this.filter.category_id) {
        this.where.category_id = this.filter.category_id;
      }
      this.handleSearch();
      this.getCategories();
    },
    methods: {
      searchByCategory (category_id) {
        this.where.category_id = category_id;
        this.handleSearch();
      },
      getCategories () {
        const where = { search: 'simple', val: 'list', };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getCategoryList(where).then(ret => {
          this.categories = ret.data;
        })
      },
      getList () {
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
      onChangePage () {
        this.getList();
      },
      onChangePageSize (per_page) {
        this.list.per_page = per_page;
        this.getList();
      },
      showDescription (row) {
        this.$msgbox({
          title: '商品描述',
          message: renderQuill(row.description),
          dangerouslyUseHTMLString: true,
          customClass: 'ann-box' // 假装是公告
        });
      },
      goCards (type, row) {
        if (process.env.PROJECT === 'merchant') {
          this.$router.push({
            path: '/admin/product/card/list',
            query: { type, category_id: row.category_id, product_id: row.id }
          })
        } else {
          this.$emit('changeTab', {
            to: 'card',
            type,
            category_id: row.category_id,
            product_id: row.id
          });
        }
      },
      handleSetSort (row) {
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
      handleDisable (row) {
        const name = row.name;
        this.$confirm('下架商品<' + name + '>?', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enable(row.id, 0).then(() => {
            this.$notify({ title: '操作成功', message: '商品<' + name + '>已下架', type: 'info' });
            row.enabled = 0;
            this.loading = false;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleEnable (row) {
        const name = row.name;
        this.$confirm('上架商品<' + name + '>?', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enable(row.id, 1).then(() => {
            this.$notify({ title: '操作成功', message: '商品<' + name + '>已上架', type: 'success' });
            row.enabled = 1;
            this.loading = false;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleDelete (row) {
        const name = row.name;
        this.$confirm('删除商品<' + name + '>? 此操作不可恢复', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deleteAction(row.id).then(() => {
            this.$notify({ title: '操作成功', message: '商品<' + name + '>已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleDomain (row) {
        this.$msgbox({
          title: '单独链接',
          message: `<a href="${row.url}" target="_blank">${row.url}</a>`,
          dangerouslyUseHTMLString: true,
        });
      },
      checkDelete () {
        if (!this.checkedIds.length) return;
        this.$confirm('批量删除选中商品? 此操作不可恢复', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deleteAction(this.checkedIds).then(() => {
            this.$notify({ title: '操作成功', message: '选中商品已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      checkEnable () {
        if (!this.checkedIds.length) return;
        this.$confirm('批量上架选中商品? ', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enable(this.checkedIds, 1).then(() => {
            this.$notify({ title: '操作成功', message: '选中商品已上架', type: 'success' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      checkDisable () {
        if (!this.checkedIds.length) return;
        this.$confirm('批量下架选中商品? ', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          enable(this.checkedIds, 0).then(() => {
            this.$notify({ title: '操作成功', message: '选中商品已下架', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleAdd () {
        if (process.env.PROJECT === 'merchant') {
          this.$router.push('/admin/product/add');
        } else {
          window.scrollTo(0, 0);
          this.addBoxLoaded = true;
          this.$nextTick(() => {
            this.showAddBox = !this.showAddBox;
          })
        }
      },
      onBoxSubmit (ret) {
        if (ret) this.getList();
        this.showAddBox = false;
        this.$nextTick(() => {
          this.addBoxLoaded = false;
          this.editInfo = null;
        })
      },
      handleEdit (row) {
        const next = () => {
          window.scrollTo(0, 0);
          this.editInfo = row;
          this.addBoxLoaded = true;
          this.$nextTick(() => {
            this.showAddBox = true;
          });
        };
        if (this.editInfo) {
          this.onBoxSubmit(false);
          this.$nextTick(() => {
            this.addBoxLoaded = false;
            this.$nextTick(() => {
              next();
            })
          })
        } else {
          next();
        }
      },
      // @deprecated
      editCallback (ret) {
        this.editInfo = null;
        if (ret) this.getList();
      },
      handleEditCategory (row) {
        this.editCategory = row;
      },
      editCategoryCallback (payload) {
        if (payload && payload.category) {
          this.editCategory.category_id = payload.category.id;
          this.editCategory.category = payload.category;
        }
        this.editCategory = null;
      }
    }
  }
</script>


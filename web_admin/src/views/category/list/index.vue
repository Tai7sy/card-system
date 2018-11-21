<template>
  <div :class="{'app-container':!userInfo}" ref="container">

    <el-collapse-transition>
      <div v-show="showAddBox" class="apply-box">
        <el-button icon="el-icon-close" @click="showAddBox = false" size="small"
                   style="float: right; margin: 10px 15px;"/>
        <el-card class="my-card" v-if="showAddBox || addBoxLoaded">
          <category-add :in-box="true" :userInfo="userInfo" @submit="onAdd"/>
        </el-card>
      </div>
    </el-collapse-transition>

    <!--工具条-->
    <el-col :span="24" class="no-bottom-form" style="padding-bottom: 0px;">
      <el-form :inline="true" class="list-pre-table">
        <el-form-item class="mobile-full-width">
          <el-input placeholder="请输入" v-model="search.value" size="small">
            <el-select v-model="search.type" slot="prepend" placeholder="搜索类型" value=""
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
      </el-form>
    </el-col>
    <el-table :data="list.data" v-loading="loading" stripe border class="list-table"
              @filter-change="handleFilterChange"
              @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
      <el-table-column type="selection" width="40"/>
      <el-table-column prop="id" label="ID" width="70" v-if="_project === 'admin'"/>

      <el-table-column prop="user_id" label="商户ID" width="70" v-if="_project === 'admin' && !userInfo"/>

      <el-table-column prop="name" label="名称(点击编辑)" min-width="150" show-overflow-tooltip>
        <template slot-scope="scope">
          <a class="text-nowrap line-btn" @click="handleEdit(scope.row)">{{scope.row.name}}</a>
        </template>
      </el-table-column>

      <el-table-column prop="enabled" label="状态" width="80"
                       column-key="enabled"
                       :filters="enabledFilter"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <el-tag :type="scope.row.enabled===1?'primary':'info'" close-transition>
            {{scope.row.enabled===1?'启用':'禁用'}}
          </el-tag>
        </template>
      </el-table-column>


      <el-table-column prop="products_count" label="商品数" width="80">
        <template slot-scope="scope">
          <a class="line-btn" @click="goProducts(scope.row)">{{scope.row.products_count}}</a>
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

      <el-table-column label="操作" width="90" fixed="right">
        <template slot-scope="scope">
          <a class="icon-btn" @click="handleDomain(scope.row)">直链</a>
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

    <category-edit v-if="editAction" :info="editInfo" :action="editAction" :userInfo="userInfo" @close="editCallback"/>

  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { getList as getCategoryList, deleteAction, sort } from 'api/category'
  import categoryEdit from './_edit'
  import categoryAdd from '../add/index'

  export default {
    props: {
      userInfo: {
        type: Object,
        default: null
      },
      filterType: {
        default: null
      },
      _project: {
        default: process.env.PROJECT
      }
    },
    components: {
      categoryEdit, categoryAdd
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
        where: {},
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
          { text: '启用', value: 1, type: 'primary' },
          { text: '禁用', value: 0, type: 'info' },
        ],
        editInfo: null,
        editAction: null,
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
        getCategoryList(this.where).then(res => {
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
      goProducts (row) {
        if (process.env.PROJECT === 'merchant') {
          this.$router.push({ path: '/admin/product/list', query: { category_id: row.id } })
        } else {
          this.$emit('changeTab', {
            to: 'product',
            category_id: row.id
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
      handleEdit (row) {
        this.editAction = 'edit';
        this.editInfo = row;
      },
      handleDomain (row) {
        this.$msgbox({
          title: '单独链接',
          message: `<a href="${row.url}" target="_blank">${row.url}</a>`,
          dangerouslyUseHTMLString: true,
        });
      },
      handleDelete (row) {
        const name = row.name;
        this.$confirm('删除分类<' + name + '>? 此操作不可恢复', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deleteAction(row.id).then(() => {
            this.$notify({ title: '操作成功', message: '分类<' + name + '>已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      checkDelete () {
        if (!this.checkedIds.length) return;
        this.$confirm('批量删除选中分类? 此操作不可恢复', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          deleteAction(this.checkedIds).then(() => {
            this.$notify({ title: '操作成功', message: '选中分类已删除', type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      editCallback (success) {
        this.editAction = null;
        if (success) this.getList();
      },
      handleAdd () {
        window.scrollTo(0, 0);
        this.addBoxLoaded = true;
        this.$nextTick(() => {
          this.showAddBox = !this.showAddBox;
        })
      },
      onAdd () {
        this.getList();
        this.showAddBox = false;
      }
    }
  }
</script>



<template>
  <div class="content-wrapper">

    <mu-dialog :open="!!process" title="提示">
      <mu-circular-progress :size="40" style="vertical-align:middle;"/>
      <span v-html="process"
            style="line-height: 40px;display:inline-block;margin-left: 24px"></span>
    </mu-dialog>

    <div class="table-title">

      <mu-float-button icon="add" mini class="float-right" @click="handleAdd" style="margin-right: 8px"/>
      <mu-float-button icon="refresh" mini class="float-right" @click="getList" style="margin-right: 8px"/>
    </div>

    <div class="table-container">
      <mu-table ref="table" :showCheckbox="false" :selectable="false">
        <mu-thead>
          <mu-tr>
            <mu-th style="width: 120px">分类名称</mu-th>
            <mu-th></mu-th>
            <mu-th style="width: 90px">商品数</mu-th>
            <mu-th style="width: 80px">状态</mu-th>
            <mu-th style="width: 140px">操作</mu-th>
          </mu-tr>
        </mu-thead>
        <mu-tbody>
          <mu-tr v-for="item in list" :key="item.id">
            <mu-td class="over-ellipsis">{{item.name}}</mu-td>
            <mu-td></mu-td>
            <mu-td>{{item.goods_count}}</mu-td>
            <mu-td>{{item.enabled?'已启用':'禁用'}}</mu-td>
            <mu-td>
              <a class="cursor-pointer" @click="handleEdit(item)">编辑</a>&nbsp;
              <a class="cursor-pointer" @click="handleEnabled(item)">{{item.enabled?'禁用':'启用'}}</a>&nbsp;
              <a class="cursor-pointer" @click="handleDelete(item)">删除</a>&nbsp;
            </mu-td>
          </mu-tr>
        </mu-tbody>
      </mu-table>
    </div>


    <mu-dialog :open="!!act" title="编辑信息" @close="editCallback">
      <mu-text-field label="名称" hintText="请输入分类名称" v-model="editForm.name" class="edit-input" labelFloat/>
      <mu-switch label="启用" labelLeft v-model="editForm.enabled_bool"/>
      <mu-flat-button slot="actions" @click="editCallback" primary label="取消"/>
      <mu-flat-button slot="actions" primary @click="handleEditSubmit" label="确定"/>
    </mu-dialog>
  </div>
</template>

<script>
  import myFunc from '../../../utils/myfunc.js'

  export default {
    data () {
      return {
        process: false,
        list: [],
        act: null,
        editForm: {
          id: '',
          name: '',
          enabled: 1,
          enabled_bool: false
        }
      }
    },
    mounted () {
      this.checkLogin();
      this.getList();
    },
    methods: {
      checkLogin () {
        if (!myFunc.cookie.get('login')) {
          this.$router.push('/admin/login');
        }
      },
      getList () {
        this.process = '加载中...'
        this.$http.post('admin/group/get').then(e => {
          this.list = e.data
          this.process = false
        }).catch(() => {
          this.process = false
        });
      },
      handleAdd () {
        this.act = 'add'
        this.editForm = {
          id: '',
          name: '',
          enabled: '0',
          enabled_bool: false
        };
      },
      handleEdit (item) {
        this.act = 'edit'
        Object.assign(this.editForm, item);
        this.editForm.enabled_bool = item.enabled === 1
      },
      editCallback () {
        this.act = null
      },
      handleEditSubmit () {
        let editInfo = Object.assign({}, this.editForm);
        editInfo.enabled = editInfo.enabled_bool ? '1' : '0';
        this.$http.post('admin/group/edit', editInfo).then(() => {
          this.getList();
          this.act = null;
        })
      },
      handleEnabled (item) {
        let enabled = item.enabled ? 0 : 1;
        this.$http.post('admin/group/enabled', {
          id: item.id,
          enabled
        }).then(() => {
          item.enabled = enabled;
        })
      },
      handleDelete (item) {
        if (confirm('是否删除选中分组?')) {
          this.$http.post('admin/group/delete', {
            id: item.id
          }).then(() => {
            this.getList()
          })
        }
      }
    }
  }
</script>

<style lang="less" scoped>
  .edit-input {
    width: 100% !important;
    display: block !important;;
  }

  .table-title {
    width: 100%;
    height: 44px;
  }

  .table-container {
    overflow: auto;
    .table {
      min-width: 650px;
    }
    .today-tip {
      word-break: normal;
      width: auto;
      display: inline-block;
      white-space: pre-wrap;
      word-wrap: break-word;
    }
  }

</style>

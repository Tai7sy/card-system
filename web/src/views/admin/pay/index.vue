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
            <mu-th>名称</mu-th>
            <mu-th style="width: 90px">驱动</mu-th>
            <mu-th style="width: 90px">方式</mu-th>
            <mu-th style="width: 90px">备注</mu-th>
            <mu-th></mu-th>
            <mu-th style="width: 80px">启用</mu-th>
            <mu-th style="width: 140px">操作</mu-th>
          </mu-tr>
        </mu-thead>
        <mu-tbody>
          <mu-tr v-for="item in list" :key="item.id">
            <mu-td class="over-ellipsis">{{item.name}}</mu-td>
            <mu-td>{{item.driver}}</mu-td>
            <mu-td>{{item.way}}</mu-td>
            <mu-td class="over-ellipsis"><a class="cursor-pointer" @click="showCommentDetail(item)">{{item.comment}}</a></mu-td>
            <mu-td></mu-td>
            <mu-td :class="{'green-text':item.enabled}">
              {{item.enabled===1?'电脑':item.enabled===2?'手机':item.enabled===3?'电脑+手机':'禁用'}}
            </mu-td>
            <mu-td>
              <a class="cursor-pointer" @click="handleEdit(item)">编辑</a>&nbsp;
              <a class="cursor-pointer" @click="handleEnabled(item)">{{item.enabled?'禁用':'启用'}}</a>&nbsp;
              <a class="cursor-pointer" @click="handleDelete(item)">删除</a>&nbsp;
            </mu-td>
          </mu-tr>
        </mu-tbody>
      </mu-table>
    </div>


    <pay-edit v-if="act && 1" :act="act" :info="editInfo" @ret="editCallback"></pay-edit>
    <pay-enable v-if="enableInfo && 1" :info="enableInfo" @ret="enableCallback"></pay-enable>
  </div>
</template>

<script>
  import myFunc from '../../../utils/myfunc.js'
  import PayEdit from './edit'
  import PayEnable from './enable'

  export default {
    components: { PayEdit, PayEnable },
    data () {
      return {
        process: false,
        list: [],
        act: null,
        editInfo: null,
        enableInfo: null
      }
    },
    mounted () {
      this.checkLogin()
      this.getList()
    },
    methods: {
      checkLogin () {
        if (!myFunc.cookie.get('login')) {
          this.$router.push('/admin/login')
        }
      },
      getList () {
        this.process = '加载中...'
        this.$http.post('admin/pay/get').then(e => {
          this.list = e.data
          this.process = false
        }).catch(() => {
          this.process = false
        });
      },
      handleAdd () {
        this.act = 'add'
      },
      handleEdit (item) {
        this.editInfo = item
        this.act = 'edit'
      },
      editCallback (ret) {
        this.act = null
        if (ret) this.getList()
      },
      enableCallback (ret) {
        this.enableInfo = null
        if (ret) this.getList()
      },
      handleEnabled (item) {
        this.enableInfo = item;
      },
      handleDelete (item) {
        if (confirm('是否删除选中支付方式?')) {
          this.$http.post('admin/pay/delete', {
            id: item.id
          }).then(() => {
            this.getList()
          })
        }
      },
      showCommentDetail (item) {
        this.$alert(item.comment, '备注详情');
      }
    }
  }
</script>

<style lang="less" scoped>
  .edit-input {
    width: 100% !important;
    display: block !important;
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

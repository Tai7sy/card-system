<template>
  <div>
    <mu-dialog :open="!!process" title="提示">
      <mu-circular-progress :size="40" style="vertical-align:middle;"/>
      <span v-html="process"
            style="line-height: 40px;display:inline-block;margin-left: 24px"></span>
    </mu-dialog>

    <mu-paper :zDepth="1" class="container">
      <mu-icon-button tooltip="关闭" icon="close" style="float: right;margin-top: -12px;margin-right: -12px;"
                      @click="handleClose"/>
      <mu-icon-button tooltip="添加" icon="add" style="float: right;margin-top: -12px;"
                      @click="handleAdd"/>
      <h3 style="margin: 0">{{good.name}}</h3>
      <mu-table ref="table" :showCheckbox="false" :selectable="false" style="padding-bottom: 48px">
        <mu-thead>
          <mu-tr>
            <mu-th style="width: 120px">卡密</mu-th>
            <mu-th></mu-th>
            <mu-th style="width: 100px">类型</mu-th>
            <mu-th style="width: 80px">状态</mu-th>
            <mu-th style="width: 100px">操作</mu-th>
          </mu-tr>
        </mu-thead>
        <mu-tbody>
          <mu-tr v-for="item in list.data" :key="item.id">
            <mu-td class="over-ellipsis">{{item.card}}</mu-td>
            <mu-td></mu-td>
            <mu-td>{{item.type===0?'一次性':'重复使用'}}</mu-td>
            <mu-td>{{item.status===0?'正常':item.status===1?'已售出':'已使用'}}</mu-td>
            <mu-td>
              <a class="cursor-pointer" @click="handleEdit(item)">编辑</a>&nbsp;
              <a class="cursor-pointer" @click="handleDelete(item)">删除</a>&nbsp;
            </mu-td>
          </mu-tr>
        </mu-tbody>
      </mu-table>
      <mu-pagination :total="list.total" :current="list.current_page" :pageSize="list.per_page"
                     :showSizeChanger='true' @pageSizeChange="handlePageSizeChange"
                     @pageChange="handlePageChange" style="float: right; margin-top: -40px">
      </mu-pagination>
    </mu-paper>


    <mu-dialog :open="!!act" title="编辑卡号" @close="editCallback">

      <mu-text-field label="卡号信息" hintText="请输入卡号信息/一行一个"
                     v-model="editForm.card" multiLine :rows="3" :rowsMax="6" style="width: 100%"/>


      <div style="margin-top: 12px">
        <mu-radio label="一次性" name="type" nativeValue="0" v-model="editForm.type"/>
        <mu-radio label="重复使用" name="type" nativeValue="1" v-model="editForm.type"/>
      </div>

      <div style="margin-top: 12px">
        <mu-radio label="正常" name="status" nativeValue="0" v-model="editForm.status"/>
        <mu-radio label="已售出" name="status" nativeValue="1" v-model="editForm.status"/>
        <mu-radio label="已使用" name="status" nativeValue="2" v-model="editForm.status"/>
      </div>


      <mu-flat-button slot="actions" @click="editCallback" primary label="取消"/>
      <mu-flat-button slot="actions" primary @click="handleEditSubmit" label="确定"/>
    </mu-dialog>
  </div>
</template>

<script>
  export default {
    props: {
      good: {}
    },
    data () {
      const initForm = {
        good_id: this.good.id,
        id: '',
        card: '',
        status: '0',
        type: '0'
      };
      return {
        process: null,
        list: {
          current_page: 1,
          per_page: 10,
          total: 10,
          data: []
        },
        act: null,
        initForm,
        editForm: Object.assign({}, initForm)
      }
    },
    mounted () {
      this.getList();
    },
    methods: {
      handleClose () {
        this.$emit('close')
      },
      getList () {
        this.process = '加载中...'
        this.$http.post('admin/card/get', {
          good_id: this.good.id,
          page: this.list.current_page,
          size: this.list.per_page
        }).then(e => {
          e.data.per_page = parseInt(e.data.per_page);
          this.list = e.data
          this.process = null
        }).catch(() => {
          this.process = null
        })
      },
      handlePageChange (v) {
        this.list.current_page = v;
        this.getList()
      },
      handlePageSizeChange (v) {
        this.list.per_page = v;
        this.getList()
      },
      handleDelete (item) {
        if (confirm('是否删除选中卡号?')) {
          this.$http.post('admin/card/delete', {
            id: item.id
          }).then(() => {
            this.getList()
          })
        }
      },
      handleAdd () {
        this.editForm = Object.assign({}, this.initForm)
        this.act = 'add'
      },
      handleEdit (item) {
        this.act = 'edit'
        const buf = Object.assign({}, item);
        buf.status += '';
        buf.type += '';
        Object.assign(this.editForm, buf);
      },
      editCallback () {
        this.act = null
      },
      handleEditSubmit () {
        let editInfo = Object.assign({}, this.editForm);
        this.$http.post('admin/card/edit', editInfo).then(() => {
          this.getList();
          this.act = null;
        })
      },
    }
  }
</script>
<style lang="less" type="text/less" scoped>
  .container {
    padding: 12px 24px 24px;
    margin-bottom: 24px;
  }
</style>

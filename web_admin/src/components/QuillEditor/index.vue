<template>
  <div class="quill-editor">
    <div :id="id_toolbar">
      <span class="ql-formats">
        <select class="ql-size" title="字体">
          <option value="small">小号</option>
          <option selected>普通</option>
          <option value="large">大号</option>
          <option value="huge">超大</option>
        </select>
        <button class="ql-bold" title="加粗"></button>
        <button class="ql-italic" title="倾斜"></button>
      </span>
      <span class="ql-formats">
        <select class="ql-color" title="颜色"></select>
        <select class="ql-background" title="背景色"></select>
      </span>
      <span class="ql-formats">
        <button class="ql-list" value="ordered" title="序号列表"></button>
        <button class="ql-list" value="bullet" title="列表"></button>
        <select class="ql-align" title="布局">
          <option selected=""></option>
          <option value="center"></option>
          <option value="right"></option>
          <option value="justify"></option>
        </select>
      </span>
      <span class="ql-formats">
        <button class="ql-image" title="插入图片"></button>
        <button class="ql-link" title="超链接"></button>
      </span>
    </div>
    <div :id="id_editor"></div>
    <div v-if="showUpload" @click="showUpload=false" style="z-index: 1010" class="v-modal"></div>
    <div v-if="showUpload" ref="upload_wrapper" @click="onUploadClose" style="z-index: 1011"
         class="el-message-box__wrapper">
      <div class="upload-box">
        <el-upload drag
                   list-type="picture"
                   :action="upload_url"
                   :multiple="false"
                   :data="{user_id:userInfo_?userInfo_.id:''}"
                   :headers="{'X-Requested-With': 'XMLHttpRequest'}"
                   :limit="1"
                   :with-credentials="true"
                   :on-error="handleUploadError"
                   :on-change="handleUploadChange">
          <i class="el-icon-upload"></i>
          <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em>
            <div style="font-size: 12px">只能上传图片文件，且不超过5MB</div>
          </div>
        </el-upload>
      </div>
    </div>
  </div>
</template>

<script>
  import Quill from 'quill'
  import { randomString } from '@/utils/helper';

  /**
   * 返回HTML
   * @param delta
   * @returns {*}
   */
  let for_render = null;

  export function renderQuill (delta) {
    if (!delta || delta[0] !== '{') {
      return delta;
    }
    if (!for_render) {
      for_render = new Quill(document.createElement('div'));
    }
    for_render.setContents(JSON.parse(delta));
    return `<div class="ql-snow"><div class="ql-editor quill-html">${for_render.root.innerHTML}<div></div>`;
  }

  export default {
    name: 'QuillEditor',
    props: {
      value: {
        default: ''
      },
      placeholder: {
        default: '请输入内容'
      },
      theme: {
        default: 'snow' // or 'bubble'
      },
      userInfo: {}, // admin 上传图片的时候要记录用户ID
      upload_url: {
        default: window.API_HOST + 'file/upload'
      }
    },
    data () {
      const id = randomString(5);
      return {
        userInfo_: null,
        id_editor: 'editor-' + id,
        id_toolbar: 'editor-toolbar-' + id,
        quill_editor: null,
        showUpload: false
      }
    },
    mounted () {
      if (this.userInfo) {
        this.userInfo_ = this.userInfo;
      } else {
        this.userInfo_ = this.$store.state.user.userInfo;
      }

      this.quill_editor = new Quill('#' + this.id_editor, {
        modules: {
          toolbar: '#' + this.id_toolbar
        },
        placeholder: this.placeholder,
        theme: this.theme
      });
      this.quill_editor.getModule('toolbar').addHandler('image', () => {
        const pos = this.quill_editor.getSelection();
        this.showUpload = true;
        this.uploadSuccess = url => {
          console.log(this.id_editor + ' uploadSuccess', url);
          this.quill_editor.insertEmbed(pos.index, 'image', url);
        }
      });
      this.setValue(this.value);
    },
    methods: {
      onUploadClose (e) {
        if (e.target === this.$refs['upload_wrapper']) {
          this.showUpload = false;
        }
      },
      handleUploadChange (file, fileList) {
        if (fileList.length > 0 && fileList[0].response) {
          const res = fileList[0].response;
          console.log(res);
          if (res.data) {
            if (!res.data.url) {
              this.showUpload = false;
              return this.$message({ message: res.msg, type: 'error' });
            }
            this.uploadSuccess(res.data.url);
            this.showUpload = false;
          } else {
            return this.$msgbox({ message: res, title: '错误', type: 'error' });
          }
        }
      },
      handleUploadError (err, file, fileList) {
        console.warn('handleUploadError');
        console.warn(err);
        try {
          const msg = JSON.parse(err.message).message;
          if (msg) return this.$message({ message: msg, type: 'error' });
        } catch (e) {
          this.$message({ message: '文件上传失败，请检查文件大小和格式', type: 'error' });
        }
      },
      getValue () {
        return JSON.stringify(this.quill_editor.getContents());
      },
      setValue (val) {
        if (val && val[0] === '{') {
          this.quill_editor.setContents(JSON.parse(val));
        } else {
          this.quill_editor.setText(val || '');
        }
      }
    }
  }
</script>

<style lang="scss">
  .upload-box {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    background-color: #fff;
    border-radius: 4px;
    border: 1px solid #ebeef5;
    min-width: 100px;
    min-height: 100px;
    .el-upload-dragger {
      padding: 0 48px;
      width: auto !important;
    }
  }
</style>

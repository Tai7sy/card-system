<template>
<el-card class="my-card">
  <div class="home-account-top">
    
    <div class="info">
      <div class="label">店铺名称：</div>
      <div class="value">{{userInfo.shop_name}}</div>
    </div>
    <div class="info">
      <div class="label">登录账号：</div>
      <div class="value">{{userInfo.email}}</div>
    </div>
    <div class="info">
      <div class="label">最近登录：</div>
      <div class="value">{{loginInfo ? loginInfo.created_at : '暂无记录'}}</div>
    </div>
    <div class="info">
      <div class="label">登录地址：</div>
      <div class="value">
        {{loginInfo ? loginInfo.ip : '暂无记录'}}
        <router-link :to="&quot;/admin/system/logs&quot;" class="el-button--text" style="margin-left: 8px">查看更多</router-link>
      </div>
    </div>
    <div class="action">
      

      <el-dropdown  trigger="click" :show-timeout="0" :hide-timeout="0" placement="bottom-start">
        <el-button>
          网站设置<i class="el-icon-arrow-down el-icon--right"></i>
        </el-button>
        <el-dropdown-menu class="home-top-info-el-dropdown-menu" slot="dropdown">
          <router-link to="/admin/system/set?tab=info">
            <el-dropdown-item>
              <icon name="regular/info-circle"/>
              常规设置
            </el-dropdown-item>
          </router-link>
          <router-link to="/admin/system/set?tab=theme">
            <el-dropdown-item>
              <icon name="cog"/>
              主题设置
            </el-dropdown-item>
          </router-link>
          <router-link to="/admin/system/set?tab=order">
            <el-dropdown-item>
              <icon name="bars"/>
              订单设置
            </el-dropdown-item>
          </router-link>
          <router-link to="/admin/system/set?tab=email">
            <el-dropdown-item>
              <icon name="regular/envelope"/>
              邮件设置
            </el-dropdown-item>
          </router-link>
          <router-link to="/admin/system/set?tab=vcode">
            <el-dropdown-item>
              <icon name="cog"/>
              验证码
            </el-dropdown-item>
          </router-link>
          <router-link to="/admin/system/set?tab=storage">
            <el-dropdown-item>
              <icon name="light/file-alt"/>
              文件储存
            </el-dropdown-item>
          </router-link>
          <router-link to="/admin/system/security">
            <el-dropdown-item>
              <icon name="regular/shield-alt"/>
              修改密码
            </el-dropdown-item>
          </router-link>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
  </div>
</el-card>
</template>

<script>
export default {
  name: 'HomeAccount',
  props: {
    userInfo: {},
    loginInfo: {}
  },
  data() {
    return {
      showVerifyRoute: !!window.config.user_verify && (!!window.config.user_verify.open || window.config.user_verify.show_in_withdraw === 0)
    };
  }
};
</script>


<style lang="scss" scoped>

  .home-account-top {
    height: 200px;
    padding: 10px;

    & > .avatar {
      margin-bottom: 20px;
      height: 48px;

      & > img {
        float: left;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        vertical-align: top;
      }

      & > .avatar-name {
        font-size: 16px;
        line-height: 24px;
        word-break: break-all;
        text-overflow: ellipsis;
        overflow: hidden;
        height: 48px;
        color: #181818;
        padding-left: 16px;
        font-weight: 500;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -ms-box-orient: vertical;
        -webkit-line-clamp: 2;
        -ms-line-clamp: 2;
        -webkit-box-pack: center;
        -ms-box-pack: center;
      }
    }

    & > .info {
      display: flex;
      height: 20px;
      line-height: 20px;
      font-size: 14px;
      color: #333;
      margin: 9px 0;

      & > * {
        display: flex;
      }

      & > .label {
        font-size: 14px;
        color: #8b93a7;
        letter-spacing: 0;
      }

      & > .value {
        max-width: 212px;
        white-space: nowrap;
        word-break: keep-all;
        word-wrap: normal;
        overflow: hidden;
        text-overflow: ellipsis;
      }
    }

    & > .action {
      margin: 24px 0 16px;

      .el-dropdown {
        width: 100%;
      }

      button {
        border: 1px solid #108ee9;
        border-radius: 4px;
        font-size: 14px;
        color: #108ee9;
        width: 100%;
        height: 32px;
        padding: 8px;
      }
    }
  }


  .home-top-info-el-dropdown-menu {
    width: 274px;
  }
</style>

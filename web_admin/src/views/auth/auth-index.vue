<template>
  <div class="auth-page">
    <div class="auth-middle">
      <router-view class="auth-content"/>
    </div>
  </div>
</template>

<style lang="scss">
  .auth-page {
    

    display: table;
    position: absolute;
    height: 100%;
    width: 100%;
  }

  .auth-middle {
    display: table-cell;
    vertical-align: middle;
  }

  .auth-content {
    margin-left: auto;
    margin-right: auto;
    position: relative;
    width: calc(100% - 40px);
    max-width: 440px;
    padding: 44px;
    margin-bottom: 28px;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
    min-width: 320px;
    overflow: hidden;

    a{

    }

    .title {
      margin-top: 0;
      margin-bottom: 16px;

      h2 {
        color: #1b1b1b;
        font-size: 24px;
        display: inline-block;
      }

      .right {
        float: right;
        line-height: 30px;
      }
    }
  }

  @media (max-width: 600px), (max-height: 366px) {
    .auth-page {
      background: none;
    }
    .auth-middle {
      vertical-align: top;
    }
    .auth-content {
      padding: 24px;
      margin: 24px auto 68px auto;
      width: 100%;
      box-shadow: none;
      border: 0;
    }
  }

  .auth-button.el-button {
    width: 100%;
  }
</style>
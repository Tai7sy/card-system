<?php
/* @var \App\User $user */
/* @var \App\Withdraw $withdraw */
if (!isset($withdraw) || !isset($user)) {
    die('看起来系统出了一些小错误，这么关键的参数都没过来');
}
$withdraw->amount = sprintf('%0.2f', $withdraw->amount / 100);
?>
<div class="email-paged"
     style="background-image: url();-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;background-position: center center;background-repeat: no-repeat;">
    <div class="email-content"
         style="opacity:0.8;width:100%;max-width:720px;text-align: left;margin: 0 auto;padding-top: 80px;padding-bottom: 60px">
        <div class="email-title"
             style="-webkit-box-shadow: 10px 10px 10px rgba(0,0,0,.13);box-shadow: 10px 10px 10px rgba(0,0,0,.13);">
            <h1 style="color:#fff;background: #51a0e3;line-height:70px;font-size:24px;font-weight:normal;padding-left:40px;margin:0">
                用户提现申请</h1>
            <div class="email-text" style="background:#fff;padding:20px 32px 0;">
                <p style="color: #6e6e6e;font-size:13px;line-height:24px;margin-top: 4px;">你好，</p>
                <p style="color: #6e6e6e;font-size:13px;line-height:24px;">以下是用户&nbsp;{{ $user->email }}&nbsp;的提现信息</p>
                <p style="color: #6e6e6e;font-size:13px;line-height:24px;">
                    ID#{{ $withdraw->id }}&nbsp;正在等待处理，<br>
                    @if($user->bank)
                        银行：{{ $user->bank->name }}<br>
                        @if($user->bank->need_subbranch)
                            支行：{{ $user->bank_sub}}<br>
                        @endif
                        @if($user->bank->need_qrcode)
                            二维码：<a target="_blank" href="<?php echo config('app.url'); ?>/api/admin/file/<?php echo $user->bank_qrcode_id; ?>/qrcode"><?php echo config('app.url'); ?>/api/admin/file/<?php echo $user->bank_qrcode_id; ?>/qrcode</a><br>
                        @endif
                        姓名：{{ $user->bank_name}}<br>
                        账户：{{ $user->bank_account}}<br>
                    @else
                        银行：未填写<br>
                    @endif
                    金额：￥{{ $withdraw->amount }}
                </p>
                <p style="color: #6e6e6e;font-size:13px;line-height:24px;">请您及时处理</p>
                <br>
            </div>
            <p style="color: #999999;font-size:13px;line-height:24px;text-align:right;padding:0 32px 16px">
                此邮件为系统自动发送，请勿回复。</p>
        </div>
    </div>
</div>
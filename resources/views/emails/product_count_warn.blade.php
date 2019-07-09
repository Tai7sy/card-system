<div class="email-paged"
     style="background-image: url();-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;background-position: center center;background-repeat: no-repeat;">
    <div class="email-content"
         style="opacity:0.8;width:100%;max-width:720px;text-align: left;margin: 0 auto;padding-top: 80px;padding-bottom: 60px">
        <div class="email-title"
             style="-webkit-box-shadow: 10px 10px 10px rgba(0,0,0,.13);box-shadow: 10px 10px 10px rgba(0,0,0,.13);">
            <h1 style="color:#fff;background: #51a0e3;line-height:70px;font-size:24px;font-weight:normal;padding-left:40px;margin:0">
                库存预警</h1>
            <div class="email-text" style="background:#fff;padding:20px 32px 0;">
                <p style="color: #6e6e6e;font-size:13px;line-height:24px;margin-top: 4px;">尊敬的用户:</p>
                <p style="color: #6e6e6e;font-size:13px;line-height:24px;">
                    商品#{{ $product['name'] }}&nbsp;库存仅剩余&nbsp;{{ $product_count }}&nbsp;件，请及时补货<br>
                    您可以将商品的库存预警设置为0来禁用本通知</p>
                <p style="color: #6e6e6e;font-size:13px;line-height:24px;">感谢您的访问，祝您使用愉快！<br>此致</p>
            </div>
            <p style="color: #999999;font-size:13px;line-height:24px;text-align:right;padding:0 32px 16px">
                此邮件为系统自动发送，请勿回复。</p>
        </div>
    </div>
</div>
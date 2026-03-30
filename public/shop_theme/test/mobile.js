
$(function () {
    if (!config.shop) {
        // 只显示三个按钮
        $('.top_bg>.top').hide();
        $('.top_bg ~ div').hide()
    }

    $('.payments li').click(function () {
        if (!detectBrowser($(this))) {
            // return;
        }
        $(this).children('span').addClass('pay_choose');
        $(this).siblings('li').find('span').removeClass('pay_choose');
        $(this).children('input').attr('checked', true);
        $(this).siblings('li').find('input').attr('checked', false);
    });

    // 选中第一个

    $('#browser_shadow').click(function () {
        $(this).hide();
    });

    if (device.isQQ()) {
        if ($('li.qq').length > 0) {
            $('li.qq:first').trigger('click');
            // QQ内可以跳转支付宝和微信
            // $('li[class!=qq]>span').addClass('disabled');
        } else {
            // $('#browser_shadow').show();
        }
    } else if (device.isWeChat()) {
        if ($('li.wx').length > 0) {
            $('li.wx:first').trigger('click');
            $('li[class!=wx]>span').addClass('disabled');
        } else {
            $('#browser_shadow').show();
        }
    } else if (device.isAlipay()) {
        if ($('li.ali').length > 0) {
            $('li.ali:first').trigger('click');
            $('li[class!=ali]>span').addClass('disabled');
        } else {
            $('#browser_shadow').show();
        }
    } else {
        $('.payments li:first-child').trigger('click');
    }
});


/**
 * 浏览器探测
 */
function detectBrowser($that) {
    var li_id = $that.attr('class');
    if (device.isQQ()) {
        if (li_id === 'qq') {
            return true;
        } else {
            $('#browser_shadow').show();
            return false;
        }
    } else if (device.isWeChat()) {
        if (li_id === 'wx') {
            return true;
        } else {
            $('#browser_shadow').show();
            return false;
        }
    } else if (device.isAlipay()) {
        if (li_id === 'ali') {
            return true;
        } else {
            $('#browser_shadow').show();
            return false;
        }
    }
    return true;
}
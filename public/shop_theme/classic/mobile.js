function msg(options) {
    if (!options.btn) {
        options.btn = ['确定', '取消'];
    }
    swal({
        type: options.type,
        title: options.title,
        html: options.content,
        confirmButtonText: options.btn[0],
        cancelButtonText: options.btn[1]
    }).then(options.then);
}


function showToast(type, content) {
    switch (type) {
        case 'success':
            YsToast.ok(content);
            break;
        case 'error':
            YsToast.error(content);
            break;
        case 'warn':
            YsToast.warn(content);
            break;
        default:
            YsToast.info(content)
    }
}

function showShopInfo() {
    swal({
        title: '店铺信息',
        html: $('#shop_html').html(),
        confirmButtonText: '关闭'
    })
}

function showAnn() {
    swal({
        title: '商家公告',
        html: $('#ann').html(),
        confirmButtonText: '关闭'
    })
}

function showPfyh() {
    swal({
        title: '批发优惠',
        html: $('#discount-tip').html(),
        confirmButtonText: '关闭'
    })
}


function showOrderTip(tip, then) {
    swal({
        html: tip,
        confirmButtonText: '我已付款',
        cancelButtonText: '返回',
        showConfirmButton: true,
        showCancelButton: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    }).then(function (event) {
        if(event.value) then();
    });
}


function passwordDialog(title, then) {
    swal({
        type: 'info',
        title: title,
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        confirmButtonText: '确定',
        showCancelButton: true,
        cancelButtonText: '取消',
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        animation: false
    }).then(function (a) {
        then(a ? a.value : '');
    }).catch(function () { then() })
}

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
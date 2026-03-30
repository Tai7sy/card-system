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


function inputDialog(title, then) {
    swal({
        type: 'info',
        title: typeof title === 'object' ? title.title : title,
        text: typeof title === 'object' ? title.content : '',
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

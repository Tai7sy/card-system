function msg(options) {
    var icon = undefined;
    if (options.type === 'warning') {
        icon = 0;
    } else if (options.type === 'success') {
        icon = 1;
    } else if (options.type === 'error') {
        icon = 2;
    }
    if (!options.btn) {
        options.btn = ['确定'];
    }
    layer.open({
        title: options.title,
        content: options.content,
        btn: options.btn,
        icon: icon,
        yes: function (index) {
            options.then && options.then();
            layer.close(index);
        }
    });
}

function showToast(type, content) {
    var icon = undefined;
    switch (type) {
        case 'success':
            icon = 1;
            break;
        case 'error':
            icon = 2;
            break;
        case 'warn':
            icon = 0;
            break;
    }
    layer.open({
        content: content,
        btn: ['确定'],
        icon: icon
    });
}

function showAnn() {
    layer.open({
        title: '商家公告',
        content: $('#ann').html(),
        shadeClose: true
    });
}

function showOrderTip(tip, then) {
    layer.open({
        title: '提示',
        content: tip,
        btn: ['已付款, 查询订单', '返回'],
        yes: function (index) {
            then && then();
            layer.close(index);
        }
    })
}

function inputDialog(title, then) {

    var options = {
        formType: 0,
        value: '',
        title: typeof title === 'object' ? title.title : title,
        btn2: function () {
            console.log('cancel ?');
            then();
        }
    };

    if(typeof title === 'object'){
        options.content = title.content;
    }

    layer.prompt(options, function (value, index, elem) {
        then(value);
        layer.close(index);
    });
}

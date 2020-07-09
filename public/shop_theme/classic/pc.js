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

function passwordDialog(title, then) {
    layer.prompt({
        formType: 0,
        value: '',
        title: title,
        btn2: function () {
            console.log('cancel ?');
            then();
        }
    }, function (value, index, elem) {
        then(value);
        layer.close(index);
    });
}


$(function () {
    if (!config.shop) {
        // 只显示三个按钮
        $('.nyroModal').hide();
    }

    $('.right_form li input,.right_form li select').focus(function () {
        // $(this).siblings('span').addClass('span_up');
    });
    $('.right_form li input').blur(function () {
        if ($(this).val() == '') {
            // $(this).siblings('span').removeClass('span_up');
        }
    });
    $('.right_form li select').blur(function () {
        var sel_option = $(this).find('option:selected').text();
        if (sel_option == '') {
            $(this).siblings('span').removeClass('span_up');
        }
    });
    $('.fz_lab').click(function () {
        if ($(this).children("input").attr('checked')) {
            $(this).addClass("lab_on");
        } else {
            $(this).removeClass("lab_on");
        }
    });

    $(".pay").click(function () {
        $(this).siblings(".pay").removeClass("checked1");
        $(this).addClass("checked1");
    });
    $(".lab3").click(function () {
        $(this).children('input').attr('checked', true);
        $(this).siblings("label").children('input').attr('checked', false);
        $(this).siblings("label").removeClass("checked2");
        $(this).addClass("checked2");
        calcTotalPrice();
    });


    /**
     * 显示批发优惠价格
     */
    $('#discount-btn').hover(
        function () {
            var index = layer.tips($('#discount-tip').html(), $('#discount-btn'), {
                tips: [2, '#4B4B4B'],
                time: 0
            });
            $(this).attr("data-index", index);
        },
        //4.关闭显示
        function () {
            layer.close($(this).attr("data-index"));
        }
    );
});

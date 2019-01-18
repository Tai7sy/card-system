var currentCategory = null;
var currentProduct = null;
var currentCouponInfo = null;
var buyParam = {
    category_password: '',
    product_password: ''
};
var codeValidate = null;
var shopType = 'shop';
if (config.product && config.product.id > 0) {
    shopType = 'product'
}

$(function () {
    $.ajaxSetup({
        xhrFields: {
            withCredentials: true
        },
        error: function (e, _, type) {
            msg({
                title: '请求失败',
                content: e.responseText ? JSON.parse(e.responseText).message : '网络连接错误: ' + type,
                type: 'error'
            });
        }
    });
});


function renderQuill(delta, checkEmpty) {
    if (!delta) {
        return '';
    }
    if (typeof delta === 'string') {
        if (delta[0] !== '{') {
            return delta;
        }
        try {
            delta = JSON.parse(delta);
        } catch (e) {
            return delta;
        }
    }
    if (checkEmpty) {
        // {"ops":[{"insert":"\n"}]}
        if (!(delta.ops && delta.ops.length)) {
            return false;
        }
        if (delta.ops.length === 1 && delta.ops[0].insert === '\n') {
            return false;
        }
    }
    var for_render = new Quill(document.createElement('div'));
    for_render.setContents(delta);
    return for_render.root.innerHTML;
}

function selectCategory(id) {
    var categories = $('#categories');
    if (config.theme.list_type === 'button') {
        categories.children().removeClass('checked');
        categories.children('[data-id=' + id + ']').addClass('checked');
    } else {
        categories.val(id);
    }
}

function selectProduct(id) {
    var products = $('#products');
    if (config.theme.list_type === 'button') {
        products.children().removeClass('checked');
        products.children('[data-id=' + id + ']').addClass('checked');
    } else {
        products.val(id);
    }
}


function clearProductInfo() {
    currentProduct = null;
    currentCouponInfo = null;
    $('#price').html(' - ');
    // 批发优惠
    $('#discount-btn').hide();
    $('#discount-tip').html('');
    // 库存
    $('#invent').html('');
    $('#description').html('');

    $('#coupon-box').hide();
    $('#coupon').val('');
    $('#should-pay').html(' - ')
}


function getProducts(category_id) {
    if (category_id < 1) return;

    selectProduct(-1);
    if (config.theme.list_type === 'dropdown') {
        $('#products > option:first-child').text('加载中...');
    }

    for (var i = 0; i < config.categories.length; i++) {
        if (config.categories[i].id === +category_id) {
            currentCategory = config.categories[i];
            break;
        }
    }

    var queryData = {
        category_id: category_id
    };

    var next = function () {
        $.post('/api/shop/product', queryData).success(function (res) {
            if (currentCategory.id !== +category_id) {
                return;
            }
            config.products = res.data;
            clearProductInfo();
            var children = [];
            res.data.forEach(function (e) {
                var tmp = document.createElement(config.theme.list_type === 'button' ? 'div' : 'option');
                if (config.theme.list_type === 'button') {
                    tmp.setAttribute('class', 'button-select-item');
                    tmp.setAttribute('data-id', e.id);
                    tmp.setAttribute('onclick', 'productsChange(' + e.id + ');');
                }
                tmp.setAttribute('value', e.id);
                tmp.innerText = e.name;
                children.push(tmp)
            });
            if (config.theme.list_type === 'button') {
                $('#products').html(children);
            } else {
                $('#products').html('<option value="-1">请选择商品</option>').append(children);
            }

            buyParam.category_password = queryData.password;
        }).error(function () {
            if (config.theme.list_type === 'button') {
                $('#products').html('');
            } else {
                $('#products').html('<option value="-1">请选择商品</option>');
            }
            selectCategory(-1);
            currentCategory = null;
        });
    };

    if (currentCategory.password_open) {
        passwordDialog('请输入分类密码', function (password) {
            if (!password || !password.length) {
                showToast('warn', '请输入分类密码');
                selectCategory(-1);
                if (config.theme.list_type === 'button') {
                    $('#products').html('');
                } else {
                    $('#products > option:first-child').text('请选择商品');
                }
                return;
            }
            queryData.password = password;
            next();
        });
    } else {
        next();
    }
}


function showProductInfo(product) {
    clearProductInfo();

    var next = function () {
        $('#price').text((product.price / 100).toFixed(2));
        $('#invent').html('库存: ' + product.count2);
        $('#description').html(renderQuill(product.description)).show();

        if (product.price_whole) {
            try {
                var price_whole = JSON.parse(product.price_whole)
            } catch (e) {
                price_whole = [];
            }
            if (price_whole.length) {
                var msg = '';
                price_whole.forEach(function (e) {
                    msg += '满' + e[0] + '件，单价<b>' + (e[1] / 100).toFixed(2) + '</b>元<br>';
                });
                $('#discount-btn').fadeIn();
                $('#discount-tip').html('<p>优惠<br>' + msg + '<p>')
            }
        }

        if (product.support_coupon) {
            $('#coupon-box').fadeIn();
        }

        currentProduct = product;
        calcTotalPrice();
    };

    if (product.password_open) {
        passwordDialog('请输入商品密码', function (password) {
            if (!password || !password.length) {
                showToast('warn', '请输入商品密码');
                selectProduct(-1);
                return;
            }
            $.post('/api/shop/product/password', {
                product_id: product.id,
                password: password
            }).success(function () {
                buyParam.category_password = password;
                next();
            }).error(function () {
                selectProduct(-1);
            });
        });
    } else {
        buyParam.product_password = undefined;
        next();
    }
}

function getCouponInfo() {
    $.post('/api/shop/coupon', {
        category_id: currentCategory.id,
        product_id: currentProduct.id,
        coupon: $('#coupon').val()
    }).success(function (res) {
        currentCouponInfo = res.data;
        calcTotalPrice();
    }).error(function () {
        showToast('warn', '优惠券信息无效')
    });
}

function calcTotalPrice() {
    if (!currentProduct) {
        $('#should-pay').html(' - ');
        return false;
    }
    if (!assertTradeAmount()) {
        $('#should-pay').html(' - ');
        return false;
    }

    var buyCount = $('#quantity').val();
    var price = currentProduct.price * buyCount;
    try {
        var price_whole = JSON.parse(currentProduct.price_whole)
    } catch (e) {
        price_whole = [];
    }
    if (price_whole) {
        for (var i = price_whole.length - 1; i >= 0; i--) {
            if (buyCount >= parseInt(price_whole[i][0])) {
                $('#price').text((price_whole[i][1] / 100).toFixed(2));
                price = price_whole[i][1] * buyCount;
                break;
            }
        }
    }

    var price_pay = price;
    if (currentCouponInfo) {
        var DISCOUNT_TYPE_AMOUNT = 0;
        var DISCOUNT_TYPE_PERCENT = 1;
        var off = 0;
        if (currentCouponInfo.discount_type === DISCOUNT_TYPE_AMOUNT && price > currentCouponInfo.discount_val) {
            price_pay = price_pay - currentCouponInfo.discount_val;
            off = (currentCouponInfo.discount_val / 100).toFixed(2)
        } else if (currentCouponInfo.discount_type === DISCOUNT_TYPE_PERCENT) {
            price_pay = price_pay - parseInt(price_pay * currentCouponInfo.discount_val / 100);
            off = currentCouponInfo.discount_val + '%'
        }
        $('#coupon-tip').text('立减' + off + ', 已优惠:' + ((price - price_pay) / 100).toFixed(2));
    }
    var pay_amount = (price_pay / 100).toFixed(2);
    if (1 === +window.config.shop.fee_type) {
        var fee = 0;
        var payway = getPayway();
        if (payway) {
            // 四舍五入
            fee = Math.round(price_pay * payway.fee / (1 - payway.fee))
        }
        if (fee > 0) {
            price_pay += fee;
            pay_amount = (price_pay / 100).toFixed(2) +
                ' <span style="font-size: 8px">(手续费' + (fee / 100).toFixed(2) + ') </span>';
        }
    }
    $('#should-pay').html(pay_amount);
    return true;
}

function assertTradeAmount() {
    if (!currentProduct) return;
    var count = $('#quantity').val();
    if (count < currentProduct.buy_min || currentProduct.buy_max < count) {
        if (currentProduct.buy_min === currentProduct.buy_max) {
            var tip = '此商品只能购买&nbsp;' + currentProduct.buy_min + '</b>&nbsp;件'
        } else {
            tip = '最少购买&nbsp;<b>' + currentProduct.buy_min + '</b>&nbsp;件<br>最多购买&nbsp;<b>' + currentProduct.buy_max + '</b>&nbsp;件';
        }
        msg({
            title: '提示',
            content: '购买数目限制<br>' + tip,
            btn: ['关闭'],
            then: function () {
                $('#quantity').val(currentProduct.buy_min).focus()
            }
        });
        return false;
    }
    if (currentProduct.count === 0) {
        msg({
            title: '提示',
            content: '当前商品库存不足',
            btn: ['关闭']
        });
        return false;
    }
    if (+currentProduct.count && +currentProduct.count < count) {
        msg({
            title: '提示',
            content: '购买数目不能超出商品库存<br>当前商品库存&nbsp;<b>' + currentProduct.count + '</b>&nbsp;件',
            btn: ['关闭'],
            then: function () {
                $('[name=quantity]').focus();
            }
        });
        return false;
    }
    return true;
}

var device = {
    isQQ: function () {
        return navigator.userAgent.toLowerCase().indexOf('qq/') > -1;
    },

    isWeChat: function () {
        return navigator.userAgent.toLowerCase().indexOf('micromessenger') > -1;
    },

    isAlipay: function () {
        return navigator.userAgent.toLowerCase().indexOf('alipayclient') > -1;
    }
};

function getCookie(name) {
    var parts = ('; ' + document.cookie).split('; ' + name + '=');
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function getPayway() {
    var pay_id = $('input[name=payway]:checked').val();
    if (pay_id !== undefined) {
        pay_id = +pay_id;
    } else {
        return null;
    }

    for (var i = 0; i < config.pays.length; i++) {
        if (config.pays[i].id === pay_id) {
            return config.pays[i];
        }
    }
    return null;
}

function order(type) {
    if (!currentCategory) {
        showToast('error', '请选择商品分类');
        $('#categories').focus();
        return false; // 阻止冒泡
    }

    if (!currentProduct) {
        showToast('error', '请选择商品');
        ('#products').focus();
        return false;
    }
    var contact = $('#contact').val();
    if (!contact || contact.length < 6) {
        msg({
            type: 'error',
            content: '联系方式长度至少为6位',
            then: function () {
                $('#contact').focus();
            }
        });
        return;
    }

    if (!calcTotalPrice()) {
        return;
    }
    var orderUrl = window.config.url + '/api/shop/buy?category_id=' + currentCategory.id + '&product_id=' + currentProduct.id;
    if (buyParam.category_password)
        orderUrl += '&category_password=' + encodeURIComponent(buyParam.category_password);
    if (buyParam.product_password)
        orderUrl += '&product_password=' + encodeURIComponent(buyParam.product_password);
    orderUrl += '&count=' + $('#quantity').val() +
        '&coupon=' + encodeURIComponent($('#coupon').val()) +
        '&email=' + encodeURIComponent(contact) +
        '&pay_id=' + $('input[name=payway]:checked').val() +
        '&customer=' + getCookie('customer');
    if (window.config['vcode']['buy']) {
        for (var key in codeValidate) {
            if (codeValidate.hasOwnProperty(key)) {
                orderUrl += '&' + key + '=' + encodeURIComponent(codeValidate[key]);
            }
        }
    }
    if (type === 'self') {
        location.href = orderUrl;
        return;
    }
    window.open(orderUrl, '_blank');

    showOrderTip('请在弹出的窗口完成付款<br><span style="font-size:13px">如果没有弹出窗口或付款失败，您也可以返回重新发起付款</span>', function () {
        window.open('/s?tab=cookie', '_blank')
    });
}

function checkOrder() {
    if (!currentCategory) {
        showToast('error', '请选择商品分类');
        $('#categories').focus();
        return false; // 阻止冒泡
    }

    if (!currentProduct) {
        showToast('error', '请选择商品');
        ('#products').focus();
        return false;
    }

    var contact = $('#contact').val();
    if (!contact || contact.length < 6) {
        msg({
            type: 'error',
            content: '联系方式长度至少为6位',
            then: function () {
                $('#contact').focus();
            }
        });
        return false;
    }

    return calcTotalPrice();
}

$(function () {
    $('#ann>.container').html(renderQuill(config.shop.ann));
    if (config.shop.ann_pop) {
        var ann_pop = renderQuill(config.shop.ann_pop, true);
        if (ann_pop) {
            swal({
                title: '店铺公告',
                html: '<div class="ql-editor quill-html">' + ann_pop + '</div>'
            });
        }
    }

    var categories = $('#categories');
    var products = $('#products');
    var categoriesElement = [];
    config.categories.forEach(function (e) {
        var tmp = document.createElement(config.theme.list_type === 'button' ? 'div' : 'option');
        if (config.theme.list_type === 'button') {
            if (shopType === 'product') {
                tmp.setAttribute('class', 'button-select-item checked');
            } else {
                tmp.setAttribute('class', 'button-select-item');
                tmp.setAttribute('onclick', 'categoriesChange(' + e.id + ');');
            }
            tmp.setAttribute('data-id', e.id);
        }
        tmp.setAttribute('value', e.id);
        tmp.innerText = e.name;
        categoriesElement.push(tmp)
    });

    if (config.theme.list_type === 'button') {
        window.categoriesChange = function (id) {
            categories.children().removeClass('checked');
            categories.children('[data-id=' + id + ']').addClass('checked');
            getProducts(id);
        };
        window.productsChange = function (id) {
            products.children().removeClass('checked');
            products.children('[data-id=' + id + ']').addClass('checked');

            clearProductInfo();
            for (var i = 0; i < config.products.length; i++) {
                if (config.products[i].id === +id) {
                    showProductInfo(config.products[i]);
                    break;
                }
            }
        }
    }

    if (shopType === 'product') {
        var tmp = document.createElement(config.theme.list_type === 'button' ? 'div' : 'option');
        if (config.theme.list_type === 'button')
            tmp.setAttribute('class', 'button-select-item checked');
        tmp.setAttribute('value', config.product.id);
        tmp.innerText = config.product.name;

        categories.html(categoriesElement).prop('disabled', true);
        currentCategory = config.categories[0];
        products.html(tmp).prop('disabled', true);

        showProductInfo(config.product);
    } else {
        categories.append(categoriesElement);

        if (config.categories.length === 1) {
            categories.val(config.categories[0].id).prop('disabled', true);
            getProducts(config.categories[0].id);
        }

        if (config.theme.list_type === 'button') {
        } else {
            categories.change(function () {
                currentCategory = null;
                clearProductInfo();
                getProducts($(this).val())
            });
            products.change(function () {
                clearProductInfo();
                for (var i = 0; i < config.products.length; i++) {
                    if (config.products[i].id === +$(this).val()) {
                        showProductInfo(config.products[i]);
                        break;
                    }
                }
            })
        }
    }


    $('#quantity').change(calcTotalPrice);

    $('#coupon').change(getCouponInfo);

    if (window.config.vcode.buy) {
        if (window.config.vcode.driver === 'geetest') {
            var data = window.config.vcode['geetest'];
            var gtButton = document.createElement('button');
            gtButton.setAttribute('id', 'gt-btn-verify');
            gtButton.style.display = 'none';
            document.body.appendChild(gtButton);
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                offline: !data.success, // 表示用户后台检测极验服务器是否宕机
                product: 'bind', // 这里注意, 2.0请改成 popup
                width: '300px',
                https: true
                // 更多配置参数说明请参见：http://docs.geetest.com/install/client/web-front/
            }, function (captchaObj) {
                captchaObj.onReady(function () {
                    console.log('geetest: onReady')
                });
                captchaObj.onError(function (e) {
                    console.log('geetest: onError');
                    console.error(e);
                    msg({
                        title: '出错了',
                        content: '下单验证码加载失败, 请刷新重试',
                        type: 'error',
                        then: function () {
                            location.reload();
                        }
                    })
                });
                captchaObj.onSuccess(function () {
                    var result = captchaObj.getValidate();
                    if (!result) {
                        return alert('请完成验证');
                    }
                    codeValidate = {
                        geetest_challenge: result.geetest_challenge,
                        geetest_validate: result.geetest_validate,
                        geetest_seccode: result.geetest_seccode
                    };
                    msg({
                        title: '验证完成',
                        content: '验证成功，请点击按钮跳转支付页面',
                        btn: ['去支付'],
                        then: function () {
                            order();
                        }
                    });
                });

                window.captchaObj = captchaObj;
                $('#order-btn').click(function () {
                    if (checkOrder()) {
                        if (typeof captchaObj.verify === 'function') {
                            captchaObj.verify();
                        } else {
                            $('#gt-btn-verify').click();
                        }
                    }
                });

                // captchaObj.appendTo('#gt-btn-verify');
                if (captchaObj.bindOn) {
                    captchaObj.bindOn('#gt-btn-verify');
                    // 3.0 没有 bindOn 接口
                }
            });
        }
    } else {
        $('#order-btn').click(function () {
            if (checkOrder()) {
                order();
            }
        });
    }
});
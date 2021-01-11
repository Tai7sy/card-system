var currentCategory = null;
var currentProduct = null;
var currentCouponInfo = null;
var codeValidate = null;
var shopType = 'shop';
var contactType = 'any';
var contactTypeText = {
    email: {
        title: '联系邮箱',
        placeholder: '请输入邮箱，用于查询订单'
    },
    mobile: {
        title: '联系手机号',
        placeholder: '请输入手机号，用于查询订单'
    },
    qq: {
        title: '联系QQ',
        placeholder: '请输入QQ号码，用于查询订单'
    },
    any: {
        title: '联系方式',
        placeholder: '可以输入QQ、邮箱、手机号等等，用于查询订单'
    }
};
var contactExt = [];
var contactExtValues = [];
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

function randomString(len) {
    len = len || 16;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    /** **默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
    var maxPos = $chars.length;
    var pwd = '';
    for (var i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function validateEmail(str) {
    var reg = /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/;
    return reg.test(str.trim());
}

function validateMobile(mobile) {
    return !isNaN(mobile) && mobile[0] === '1' && mobile.length === 11;
}

function validatNumbers(str) {
    var reg = /^[0-9]+$/;
    return reg.test(str);
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

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
    if (id <= 0) {
        currentCategory = null;
    }
    var categories = $('#categories');
    if (config.theme.list_type === 'button') {
        categories.children().removeClass('checked');
        categories.children('[data-id=' + id + ']').addClass('checked');
    } else {
        categories.val(id);
    }
}

function selectProduct(id) {
    if (id <= 0) {
        currentProduct = null;
    }
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

    function showProductsHtml(products) {
        clearProductInfo();
        var children = [];
        products.forEach(function (e) {
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
        if (products.length === 1) {
            selectProduct(products[0].id);
            showProductInfo(products[0]);
        }
    }

    if (currentCategory.products) {
        showProductsHtml(currentCategory.products);
        return;
    }

    var queryData = {
        category_id: category_id
    };

    var next = function () {
        $.post('/api/shop/product', queryData).success(function (res) {
            if (currentCategory.id !== +category_id) {
                // 如果在请求过程中已经选择了另一个分类, 则返回
                return;
            }
            if (!res.data.length) {
                res.data = [{
                    id: -2,
                    name: '此分类下没有商品'
                }]
            }
            currentCategory.password = queryData.password;
            currentCategory.products = res.data;
            showProductsHtml(currentCategory.products);

        }).error(function () {
            if (config.theme.list_type === 'button') {
                $('#products').html('');
            } else {
                $('#products').html('<option value="-1">请选择商品</option>');
            }
            selectCategory(-1);
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

// 选择产品时, 显示详细信息
function showProductInfo(product) {
    clearProductInfo();

    if (product.id < 0) return;

    function renderInfoToHtml() {
        // 联系方式类型限制以及自定义字段
        if (product.fields) {
            try {
                if (typeof product.fields === 'string' && product.fields[0] === '{') {
                    product.fields = JSON.parse(product.fields);
                }
                contactType = product.fields.type;
                if (product.fields.need_ext) {
                    contactExt = product.fields.ext;
                }
            } catch (e) {
                contactType = 'any';
                contactExt = [];
            }
        } else {
            contactType = 'any';
            contactExt = [];
        }
        $('#contact-box label').text(contactTypeText[contactType].title);
        $('#contact-box input').attr('placeholder', contactTypeText[contactType].placeholder);

        $('#price').text((product.price / 100).toFixed(2));
        $('#invent').html('库存: ' + product.count2);
        $('#description').html(renderQuill(product.description)).show();

        if (product.price_whole && typeof product.price_whole === 'string') {
            product.price_whole = JSON.parse(product.price_whole);
        }

        if (product.price_whole && product.price_whole.length) {
            var msg = '';
            product.price_whole.forEach(function (e) {
                msg += '满' + e[0] + '件，单价<b>' + (e[1] / 100).toFixed(2) + '</b>元<br>';
            });
            $('#discount-btn').fadeIn();
            $('#discount-tip').html('<p>优惠<br>' + msg + '<p>')
        }

        if (product.support_coupon) {
            $('#coupon-box').fadeIn();
        }

        currentProduct = product;
        calcTotalPrice();
    }


    if (product.password_open && !product.password) {
        var currentChoose = product;
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
                if(currentChoose === product){
                    product.password = password;
                    renderInfoToHtml();
                }
            }).error(function () {
                if(currentChoose === product) {
                    selectProduct(-1);
                }
            });
        });
    } else {
        renderInfoToHtml();
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
    if (currentProduct.price_whole) {
        for (var i = currentProduct.price_whole.length - 1; i >= 0; i--) {
            if (buyCount >= parseInt(currentProduct.price_whole[i][0])) {
                $('#price').text((currentProduct.price_whole[i][1] / 100).toFixed(2));
                price = currentProduct.price_whole[i][1] * buyCount;
                break;
            }
        }
    }

    var price_pay = price;
    if (currentCouponInfo) {
        var DISCOUNT_TYPE_AMOUNT = 0;
        var DISCOUNT_TYPE_PERCENT = 1;
        var off = 0;
        var discount = 0;
        if (currentCouponInfo.discount_type === DISCOUNT_TYPE_AMOUNT && price > currentCouponInfo.discount_val) {
            discount = currentCouponInfo.discount_val;
            off = (currentCouponInfo.discount_val / 100).toFixed(2)
        } else if (currentCouponInfo.discount_type === DISCOUNT_TYPE_PERCENT) {
            discount = Math.round(price_pay * currentCouponInfo.discount_val / 100);
            off = currentCouponInfo.discount_val + '%'
        }
        price_pay -= discount;
        $('#coupon-tip').text('立减' + off + ', 已优惠:' + (discount / 100).toFixed(2));
    }
    var fee_tip = '';
    if (1 === +window.config.shop.fee_type) {
        var fee = 0;
        var payway = getPayway();
        if (payway) {
            // 四舍五入
            fee = Math.round(price_pay * payway.fee / (1 - payway.fee))
        }
        if (fee > 0) {
            price_pay += fee;
            fee_tip = ' <span style="font-size: 8px">(手续费' + (fee / 100).toFixed(2) + ') </span>';
        }
    }

    var sendSms = $('#send-sms')[0];
    if (sendSms && sendSms.checked) {
        price_pay += config.sms_send_order.sms_price;
    }
    $('#should-pay').html((price_pay / 100).toFixed(2) + fee_tip);
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

function setCookie(name, value, expire) {
    if (!name || !value) return;
    if (expire !== undefined) {
        var expTime = new Date();
        expTime.setTime(expTime.getTime() + expire);
        document.cookie = name + '=' + encodeURI(value) + '; expires=' + expTime.toUTCString() + '; path=/'
    } else {
        document.cookie = name + '=' + encodeURI(value) + '; path=/'
    }
}

function getCookie(name) {
    var parts = ('; ' + document.cookie).split('; ' + name + '=');
    if (parts.length >= 2) return parts.pop().split(';').shift();
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

function _calcContactExt() {
    var ret = {};
    for (var i = 0; i < contactExtValues.length; i++) {
        ret[contactExt[i]] = contactExtValues[i];
    }
    var sendSms = $('#send-sms')[0];
    if (sendSms && sendSms.checked) {
        ret['_mobile'] = $('#sms_to').val();
    }
    var sendMail = $('#send-mail')[0];
    if (sendMail && sendMail.checked) {
        ret['_mail'] = $('#mail_to').val();
    }
    if (config.functions && config.functions.indexOf('mail_send_order_use_contact') > -1) {
        var contact = $('#contact').val();
        if (validateEmail(contact)) {
            ret['_mail'] = contact;
        }
    }
    return JSON.stringify(ret);
}

function order(type) {
    // assert(currentCategory !== null);
    // assert(currentProduct !== null);
    var contact = $('#contact').val();

    if (!calcTotalPrice()) {
        return;
    }

    var customer = getCookie('customer');
    if (!customer || customer.length !== 32) {
        customer = randomString(32);
        setCookie('customer', customer, 24 * 60 * 60 * 30 * 1000)
    }

    var orderUrl = window.config.url + '/api/shop/buy?category_id=' + currentCategory.id + '&product_id=' + currentProduct.id;
    if (currentCategory.password)
        orderUrl += '&category_password=' + encodeURIComponent(currentCategory.password);
    if (currentProduct.password)
        orderUrl += '&product_password=' + encodeURIComponent(currentProduct.password);
    orderUrl += '&count=' + $('#quantity').val() +
        '&coupon=' + encodeURIComponent($('#coupon').val()) +
        '&contact=' + encodeURIComponent(contact) +
        '&contact_ext=' + encodeURIComponent(_calcContactExt()) +
        '&pay_id=' + $('input[name=payway]:checked').val() +
        '&customer=' + customer;
    if (window.config.captcha.scene.shop.buy) {
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
        window.open('/s#/record?tab=cookie', '_blank')
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
        $('#products').focus();
        return false;
    }

    var contact = $('#contact').val();

    var showError = function (err, focus) {
        msg({
            type: 'error',
            content: err,
            then: function () {
                if (focus === undefined) focus = '#contact';
                setTimeout(function () {
                    $(focus).focus();
                }, 300);
            }
        });
        return false;
    };

    if (contactType === 'any') {
        if (!contact) return showError('请填写您的联系信息，如QQ、邮箱、手机号等等，用于查询订单');
        if (contact.length < 6) return showError('联系方式长度至少为6位');
    } else if (contactType === 'email') {
        if (!contact) return showError('请填写您的邮箱，用于查询订单');
        if (!validateEmail(contact)) return showError('输入的邮箱格式不正确');
    } else if (contactType === 'mobile') {
        if (!contact) return showError('请填写您的手机号码，用于查询订单');
        if (!validateMobile(contact)) return showError('输入的手机号格式不正确');
    } else if (contactType === 'qq') {
        if (!contact) return showError('请填写您的QQ号码，用于查询订单');
        if (contact.length < 5 || contact.length > 11 || !validatNumbers(this.contact))
            return showError('输入的QQ号码格式不正确');
    }

    if (contactExtValues.length) {
        for (var i = 0; i < contactExtValues.length; i++) {
            if (!contactExtValues[i]) {
                return showError('请填写 ' + contactExt[i])
            }
        }
    }

    var sendSms = $('#send-sms')[0];
    if (sendSms && sendSms.checked) {
        var smsTo = $('#sms_to').val();
        if (!smsTo) {
            return showError('请填写需要接受订单信息的手机号码', '#sms_to');
        }
        if (!validateMobile(smsTo)) {
            return showError('输入的手机号格式不正确', '#sms_to');
        }
    }

    var sendMail = $('#send-mail')[0];
    if (sendMail && sendMail.checked) {
        var mailTo = $('#mail_to').val();
        if (!mailTo) {
            return showError('请填写需要接受订单信息的邮箱', '#mail_to');
        }
        if (!validateEmail(mailTo)) {
            return showError('输入的邮箱格式不正确', '#mail_to');
        }
    }

    return calcTotalPrice();
}

$(function () {
    Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('mqqapi');

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

    var categoriesElm = $('#categories');
    var productsElm = $('#products');
    var categoryElms = [];
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
        categoryElms.push(tmp)
    });

    if (config.theme.list_type === 'button') {
        window.categoriesChange = function (id) {
            categoriesElm.children().removeClass('checked');
            categoriesElm.children('[data-id=' + id + ']').addClass('checked');
            getProducts(id);
        };
        window.productsChange = function (id) {
            productsElm.children().removeClass('checked');
            productsElm.children('[data-id=' + id + ']').addClass('checked');

            clearProductInfo();
            for (var i = 0; i < currentCategory.products.length; i++) {
                if (currentCategory.products[i].id === +id) {
                    showProductInfo(currentCategory.products[i]);
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

        categoriesElm.html(categoryElms).prop('disabled', true);
        currentCategory = config.categories[0];
        productsElm.html(tmp).prop('disabled', true);

        config.product.password = getParameterByName('p');
        showProductInfo(config.product);
    } else {
        categoriesElm.append(categoryElms);

        if (config.categories.length === 1) {
            categoriesElm.val(config.categories[0].id);
            getProducts(config.categories[0].id);  // 一个分类时, 商品预加载
            if (config.categories[0].password_open === false) {
                categoriesElm.prop('disabled', true);
            }
        }

        if (config.theme.list_type === 'button') {
        } else {
            categoriesElm.change(function () {
                currentCategory = null;
                clearProductInfo();
                getProducts($(this).val())
            });
            productsElm.change(function () {
                clearProductInfo();
                for (var i = 0; i < currentCategory.products.length; i++) {
                    if (currentCategory.products[i].id === +$(this).val()) {
                        showProductInfo(currentCategory.products[i]);
                        break;
                    }
                }
            })
        }
    }


    $('#quantity').change(calcTotalPrice);

    $('#coupon').change(getCouponInfo);

    if (window.config.captcha.scene.shop.buy) {
        if (window.config.captcha.driver === 'geetest') {
            var gt_config = window.config.captcha.config;
            var gtButton = document.createElement('button');
            gtButton.setAttribute('id', 'gt-btn-verify');
            gtButton.style.display = 'none';
            document.body.appendChild(gtButton);
            initGeetest({
                gt: gt_config.gt,
                challenge: gt_config.challenge,
                offline: !gt_config.success, // 表示用户后台检测极验服务器是否宕机
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
                        'captcha.a': gt_config.key,
                        'captcha.b': result.geetest_challenge,
                        'captcha.c': result.geetest_validate,
                        'captcha.d': result.geetest_seccode
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
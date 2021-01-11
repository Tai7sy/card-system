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
                message: e.responseText ? JSON.parse(e.responseText).message : '网络连接错误: ' + type,
                type: 'error'
            });
        }
    });

    var body = $('body');
    body.on('mousedown', function () {
        $(this).addClass("noOutline");
    });
    body.on('keydown', function (e) {
        if (e.key === "Tab") {
            $(this).removeClass("noOutline");
        }
    });
});

function msg(m, callback) {
    var id = 'msg_' + randomString(8);
    if (typeof m === 'object') {
        var obj = m;
        callback = obj.then;
        m = obj.message;
        if (obj.title)
            m = '<h3>' + obj.title + '</h3>' + m;

        if (obj.btn) {
            if (obj.btnfunc) {
                window['modal_btn_callback_' + id] = obj.btnfunc;
            } else if (obj.then) {
                window['modal_btn_callback_' + id] = function (id) {
                    obj.then(id, 'btn');
                }
            } else {
                window['modal_btn_callback_' + id] = closeModal;
            }
            m += '<br><div class="modal-btns"><a href="javascript:;" onclick="modal_btn_callback_' + id + '(\'' + id + '\')">' + obj.btn + '</a></div>'
        }
    }
    if (!window['_msg_index']) window['_msg_index'] = 1;
    var html = $('#message-template').html().replace(/{message}/, m).replace(/{id}/g, id);
    $(document.body).append(html);
    window['modal_close_callback_' + id] = callback;
}

function closeModal(id, self, event) {
    // console.log('closeModal.event', event);
    if (self && event && event.target !== self) {
        return;
    }
    if (typeof window['modal_close_callback_' + id] === 'function') {
        window['modal_close_callback_' + id](id, 'close');
        delete window['modal_close_callback_' + id];
    }
    $('#' + id).remove();
}

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

function toCurrency(a) {
    return '¥ ' + (a / 100).toFixed(2).replace(/\d(?=(?:\d{3})+\b)/g, '$&,')
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

function selectCategory(category) {
    currentCategory = category;

    // 左上角面包屑
    $('#shop-crumbs-content a:eq(1)').text(category ? category.name : '');

    var categoriesElm = $('#categories');
    var productsElm = $('#products');
    categoriesElm.children().removeClass('active');
    if (category) {
        categoriesElm.children('[data-id=' + category.id + ']').addClass('active');
        if (category.products) {
            renderProducts();
        } else {
            getProducts(category, function () {
                renderProducts();
            })
        }
    } else {
        productsElm.html('');
        selectProduct(null);
    }

    function renderProducts() {
        if (!category.products.length) {
            category.products = [{
                id: -1,
                name: '此分类下没有商品'
            }];
        }
        var productElms = [];
        category.products.forEach(function (product) {
            var item = document.createElement('div');
            item.setAttribute('class', 'option');

            var item_btn = document.createElement('a');

            var item_btn_texts = document.createElement('div');
            item_btn_texts.setAttribute('class', 'p-texts');

            var item_btn_name = document.createElement('span');
            item_btn_name.setAttribute('class', 'p-name');
            item_btn_name.innerText = product.name;

            var item_btn_price = document.createElement('span');
            item_btn_price.setAttribute('class', 'p-price');
            item_btn_price.innerText = '￥' + (product.price / 100);

            item_btn_texts.appendChild(item_btn_name);
            item_btn_texts.appendChild(item_btn_price);

            item_btn.appendChild(item_btn_texts);
            item_btn.addEventListener('click', function () {
                selectProduct(product);
            });
            item_btn.setAttribute('href', 'javascript:;');
            item_btn.setAttribute('tabindex', '0');
            item.appendChild(item_btn);
            item.setAttribute('data-id', product.id);
            productElms.push(item)
        });
        productsElm.html(productElms);
        if (!category.products[0].password_open || category.products[0].password) {
            selectProduct(category.products[0]);
        }
    }
}

function selectProduct(product) {
    currentProduct = product;

    var productsElm = $('#products');
    productsElm.children().removeClass('active');
    $('#price b').text('');
    $('.title-item h1').text('请选择商品');
    $('#shop-crumbs-content a:eq(2)').text('');
    $('#priceWholeBtn').hide();
    $('.buyBtn').text('立即购买').attr('disabled', true);
    $('#count').val('1');

    if (!product) {
        // 如果是空, 则取消选中, 并返回
        return;
    }
    if (product.id < 1) {
        // 如果是小于的, 说明是一个提示, 直接返回
        currentProduct = null;
        return;
    }

    function renderInfoToHtml() {
        productsElm.children('[data-id=' + product.id + ']').addClass('active');

        // 单价
        $('#price b').text('￥' + (product.price / 100));

        // 商品标题
        $('.title-item h1').text(product.name);

        // 左上角面包屑
        $('#shop-crumbs-content a:eq(2)').text(product.name);

        if (product.count < 1) {
            $('.buyBtn').text('售罄');
        } else {
            // 购买按钮
            $('.buyBtn').removeAttr('disabled');
        }


        if (product.price_whole && typeof product.price_whole === 'string') {
            product.price_whole = JSON.parse(product.price_whole);
        }

        if (product.price_whole && product.price_whole.length) {
            $('#priceWholeBtn').show()
        }

        // description-content
        $('.description-content').html(renderQuill(product.description));
    }


    if (product.password_open && !product.password) {
        msg({
            title: '请输入商品密码',
            message: '<input id="product-password-input" style="width: 90%;max-width: 320px;margin: 12px auto 0;display: block;">',
            btn: '确定',
            btnfunc: function (id) {
                var password = $('#product-password-input').val();
                if (!password || !password.length) {
                    closeModal(id);
                    return;
                }

                delete window['modal_close_callback_' + id];
                closeModal(id);

                $.post('/api/shop/product/password', {
                    product_id: product.id,
                    password: password
                }).success(function () {
                    if (currentProduct === product) {
                        product.password = password;
                        renderInfoToHtml();
                    }
                }).error(function () {
                    if (currentProduct === product) {
                        selectProduct(null);
                    }
                });
            },
            then: function () {
                selectProduct(null);
            }
        });
        setTimeout(function () {
            $('#product-password-input').focus();
        }, 100);
    } else {
        renderInfoToHtml();
    }
}

// category === currentProduct
function getProducts(category, callback) {
    if (category.products) return callback && callback(category.products);

    var queryData = {
        category_id: category.id
    };

    var next = function () {
        $.post('/api/shop/product', queryData).success(function (res) {
            if (currentCategory !== category) {
                // 如果在请求过程中已经选择了另一个分类, 则返回
                return;
            }
            if (!res.data.length) {
                res.data = [{
                    id: -2,
                    name: '此分类下没有商品'
                }]
            }
            category.password = queryData.password;
            category.products = res.data;
            callback(category.products);
        }).error(function () {
            selectCategory(null);
        });
    };

    if (category.password_open) {
        msg({
            title: '请输入分类密码',
            message: '<input id="category-password-input" style="width: 90%;max-width: 320px;margin: 12px auto 0;display: block;">',
            btn: '确定',
            btnfunc: function (id) {
                var password = $('#category-password-input').val();
                if (!password || !password.length) {
                    closeModal(id);
                    return;
                }

                delete window['modal_close_callback_' + id];
                closeModal(id);
                queryData.password = password;
                next();
            },
            then: function (id, action) {
                selectCategory(null);
            }
        });
        setTimeout(function () {
            $('#category-password-input').focus();
        }, 100);
    } else {
        next();
    }
}

function calcTotalPrice() {
    console.log('calcTotalPrice');
    var price_pay = 0;
    $('.shopList li:not(:first)').remove();
    if (currentProduct) {
        var buyCount = $('#count').val();
        $('.product-priceBox:first p:eq(1)').text('数量：' + buyCount);
        var price = currentProduct.price * buyCount;
        if (currentProduct.price_whole) {
            for (var i = currentProduct.price_whole.length - 1; i >= 0; i--) {
                var minCount = parseInt(currentProduct.price_whole[i][0]);
                if (buyCount >= minCount) {
                    console.log('批发价格生效', currentProduct.price_whole[i]);
                    $('.shopList ul').append('<li class="mainList"><div>' +
                        '<p>满' + minCount + '件, 单价' + toCurrency(parseInt(currentProduct.price_whole[i][1])) + '</p>' +
                        '</div><div class="product-priceBox"><p>- ' + toCurrency(currentProduct.price - currentProduct.price_whole[i][1]) + '</p></div></li>');
                    price = parseInt(currentProduct.price_whole[i][1]) * buyCount;
                    break;
                }
            }
        }

        price_pay = price;
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
            $('.shopList ul').append('<li class="mainList"><div><p>优惠券立减</p></div><div class="product-priceBox"><p>- ' + toCurrency(discount) + '</p></div></li>')

            $('#coupon-tip').text('立减' + off + ', 已优惠:' + (discount / 100).toFixed(2));
        }

        if (1 === +window.config.shop.fee_type) {
            var fee = 0;
            var payway = getPayway();
            if (payway) {
                // 四舍五入
                fee = Math.round(price_pay * payway.fee / (1 - payway.fee))
            }
            if (fee > 0) {
                price_pay += fee;
                $('.shopList ul').append('<li class="mainList"><div><p>手续费</p></div><div class="product-priceBox"><p>' + toCurrency(fee) + '</p></div></li>')
            }
        }

        var sendSms = $('#send-sms')[0];
        if (sendSms && sendSms.checked) {
            price_pay += config.sms_send_order.sms_price;

            if ($('#list-sms-fee')) {
                $('.shopList ul').append('<li class="mainList" id="list-sms-fee"><div><p>短信接收订单</p></div><div class="product-priceBox"><p>' + toCurrency(config.sms_send_order.sms_price) + '</p></div></li>')
            } else {
                $('#list-sms-fee .product-priceBox p').text(toCurrency(config.sms_send_order.sms_price))
            }
        }
    }

    $('.price-label').text(toCurrency(price_pay));
    return true;
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
        $('#coupon-tip').text('x 优惠券信息无效')
    });
}

function goCheckout() {
    $('#page-1').hide();
    $('#page-checkout').show();

    if (currentProduct.support_coupon) {
        $('#coupon-box').show();
        $('#coupon').val('')
    } else {
        $('#coupon-box').hide()
    }

    // 联系方式类型限制以及自定义字段
    if (currentProduct.fields) {
        try {
            if (typeof currentProduct.fields === 'string' && currentProduct.fields[0] === '{') {
                currentProduct.fields = JSON.parse(currentProduct.fields);
            }
            contactType = currentProduct.fields.type;
            if (currentProduct.fields.need_ext) {
                contactExt = currentProduct.fields.ext;
            }
        } catch (e) {
            contactType = 'any';
            contactExt = [];
        }
    } else {
        contactType = 'any';
        contactExt = [];
    }
    $('#contact-box em').text(contactTypeText[contactType].title);
    $('#contact-box input').attr('placeholder', contactTypeText[contactType].placeholder);


    // 是否勾选附加服务
    var sms_to = $('.extService .option-sms').hasClass('active');
    $('#send-sms').attr('checked', sms_to);
    $('#sms_to-box')[sms_to ? 'show' : 'hide']();

    var mail_to = $('.extService .option-mail').hasClass('active');
    $('#send-mail').attr('checked', mail_to);
    $('#mail_to-box')[mail_to ? 'show' : 'hide']();


    // 右侧名称与价格
    $('.product-name:first p').text(currentProduct.name);
    $('.product-priceBox:first p:eq(0)').text(toCurrency(currentProduct.price));
    calcTotalPrice();

    $('#invent').html('库存: ' + currentProduct.count2);

}

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

    calcTotalPrice();

    var contact = $('#contact').val();
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
    orderUrl += '&count=' + $('#count').val() +
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

    // 先弹出 后提示, 延时一会保证已经弹出
    setTimeout(function () {
        msg({
            message: '请在弹出的窗口完成付款<br>如果没有弹出窗口或付款失败，您也可以返回并重新提交订单',
            btn: '已付款，查询订单',
            btnfunc: function () {
                window.open('/s#/record?tab=cookie', '_blank')
            }
        });
    }, 200);
}

function checkOrder() {
    if (!currentCategory) {
        msg('请选择商品分类');
        $('#categories').focus();
        return false; // 阻止冒泡
    }

    if (!currentProduct) {
        msg('请选择商品');
        $('#products').focus();
        return false;
    }
    var count = +$('#count').val();
    var contact = $('#contact').val();
    var showError = function (err, focus) {
        msg(err, function () {
            if (focus === undefined) focus = '#contact';
            setTimeout(function () {
                $(focus).focus();
            }, 300);
        });
        return false;
    };

    if (count < currentProduct.buy_min || currentProduct.buy_max < count) {
        if (currentProduct.buy_min === currentProduct.buy_max) {
            var tip = '此商品只能购买&nbsp;' + currentProduct.buy_min + '</b>&nbsp;件'
        } else {
            tip = '最少购买&nbsp;<b>' + currentProduct.buy_min + '</b>&nbsp;件<br>最多购买&nbsp;<b>' + currentProduct.buy_max + '</b>&nbsp;件';
        }
        showError('商品购买数量出错，当前商品<br>' + tip, '#count');
        return false;
    }
    if (currentProduct.count === 0) {
        showError('当前商品库存不足', '#count');
        return false;
    }
    if (+currentProduct.count && +currentProduct.count < count) {
        showError('购买数量不能超出商品库存<br>当前商品库存&nbsp;<b>' + currentProduct.count + '</b>&nbsp;件', '#count');
        return false;
    }

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

    return true;
}

$(function () {
    Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('mqqapi');

    // 手机端菜单展示/隐藏
    $('.nav-mobile-menu-btn').click(function () {
        $(this).toggleClass('active');
        $('.nav-mobile-menu').toggle();
    });

    // 手机端菜单展示时, 点击任意按钮隐藏
    $('.nav-mobile-menu a').click(function () {
        $('.nav-mobile-menu-btn').toggleClass('active');
        $('.nav-mobile-menu').hide();
    });

    // 调整大小时, 隐藏菜单
    $(window).resize(function () {
        if (document.body.clientWidth > 750) {
            $('.nav-mobile-menu-btn').removeClass('active');
            $('.nav-mobile-menu').hide();
        }
    });


    // 公告
    $('#ann>.container').html(renderQuill(config.shop.ann));

    if (config.shop.ann_pop) {
        var ann_pop = renderQuill(config.shop.ann_pop, true);
        if (ann_pop) {
            msg({
                title: '店铺公告',
                message: '<div class="ql-editor quill-html">' + ann_pop + '</div>'
            });
        }
    }


    var categoriesElm = $('#categories');
    var productsElm = $('#products');
    var categoryElms = [];
    if (shopType === 'product') {
        config.product.password = getParameterByName('p');
        config.categories[0].products = [config.product];
    }
    config.categories.forEach(function (category) {
        var item = document.createElement('div');
        item.className = 'li';
        var item_btn = document.createElement('a');
        item_btn.innerText = category.name;
        item_btn.addEventListener('click', function () {
            selectCategory(category);
        });
        item_btn.setAttribute('href', 'javascript:;');
        item_btn.setAttribute('tabindex', '0');
        item.appendChild(item_btn);
        item.setAttribute('data-id', category.id);
        categoryElms.push(item)
    });

    categoriesElm.append(categoryElms);
    if (config.categories.length) {
        selectCategory(config.categories[0]); // 一个分类时, 选择分类, 然后加载商品
        if (config.categories[0].password_open === false) {
            categoriesElm.prop('disabled', true);
        }
    }

    // 价格显示批发价
    $('#priceWholeBtn').click(function () {
        if (!currentProduct || !currentProduct.price_whole) return;
        var txt = '';
        for (var i = 0; i < currentProduct.price_whole.length; i++) {
            txt += '<p tabindex="0">满' + currentProduct.price_whole[i][0] + '件，每件' + toCurrency(currentProduct.price_whole[i][1]) + '</p>'
        }
        msg('<h3>批发价格</h3>' + txt);
    });


    // 附加服务按钮
    $('.extService .option>a').click(function () {
        $(this).parent().toggleClass('active');
    });


    // 立即购买!
    $('.buyBtn').click(function () {
        if (!currentProduct) return;
        goCheckout();
        var state = {c_id: currentCategory.id, p_id: currentProduct.id};
        state.url = location.protocol + '//' + location.host + location.pathname + '?' + $.param(state);
        window.history.pushState(state, '', state.url);
    });


    // 结算页面
    $('#count').change(function () {
        $('#product-priceBox p:eq(1)').text('数量：' + $(this).val());
        calcTotalPrice();
    });

    $('#coupon').change(getCouponInfo);

    $('.paywayBtn').click(function () {
        $(this).children('input').attr('checked', true);
        $(this).siblings("label").children('input').attr('checked', false);
        $(this).siblings("label").removeClass("checked");
        $(this).addClass("checked");
    });

    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.c_id && e.state.p_id) {
            // forward, 结算页面
            goCheckout();
        } else {
            // backward, 返回首页
            $('#page-checkout').hide();
            $('#page-1').show();
        }
    });


    // 初始化, 清空地址栏信息
    history.replaceState(null, null, location.pathname);

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
                        message: '下单验证码加载失败, 请刷新重试',
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
                        message: '验证成功，请点击按钮跳转支付页面',
                        btn: '去支付',
                        btnfunc: function (id) {
                            closeModal(id);
                            order();
                        }
                    });
                });

                window.captchaObj = captchaObj;
                $('.checkoutBtn').click(function () {
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
        $('.checkoutBtn').click(function () {
            if (checkOrder()) {
                order();
            }
        });
    }
});
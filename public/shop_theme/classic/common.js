var currentCategory = null;
var currentProduct = null;
var currentCouponInfo = null;
var codeValidate = null;
var shopType = 'shop';
var contactType = 'any';
var contactTypeText = {
    email: {
        title: '聯系郵箱',
        placeholder: '請輸入郵箱，用于查詢訂單'
    },
    mobile: {
        title: '聯系手機號',
        placeholder: '請輸入手機號，用于查詢訂單'
    },
    qq: {
        title: '聯系QQ',
        placeholder: '請輸入QQ號碼，用于查詢訂單'
    },
    any: {
        title: '聯系方式',
        placeholder: '可以輸入QQ、郵箱、手機號等等，用于查詢訂單'
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
                title: '請求失敗',
                content: e.responseText ? JSON.parse(e.responseText).message : '網絡連接錯誤: ' + type,
                type: 'error'
            });
        }
    });
});

function randomString(len) {
    len = len || 16;
    var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
    /** **默認去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
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
    // 批發優惠
    $('#discount-btn').hide();
    $('#discount-tip').html('');
    // 庫存
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
        $('#products > option:first-child').text('加載中...');
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
            $('#products').html('<option value="-1">請選擇商品</option>').append(children);
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
                // 如果在請求過程中已經選擇了另一個分類, 則返回
                return;
            }
            if (!res.data.length) {
                res.data = [{
                    id: -2,
                    name: '此分類下沒有商品'
                }]
            }
            currentCategory.password = queryData.password;
            currentCategory.products = res.data;
            showProductsHtml(currentCategory.products);

        }).error(function () {
            if (config.theme.list_type === 'button') {
                $('#products').html('');
            } else {
                $('#products').html('<option value="-1">請選擇商品</option>');
            }
            selectCategory(-1);
        });
    };

    if (currentCategory.password_open) {
        passwordDialog('請輸入分類密碼', function (password) {
            if (!password || !password.length) {
                showToast('warn', '請輸入分類密碼');
                selectCategory(-1);
                if (config.theme.list_type === 'button') {
                    $('#products').html('');
                } else {
                    $('#products > option:first-child').text('請選擇商品');
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

// 選擇產品時, 顯示詳細信息
function showProductInfo(product) {
    clearProductInfo();

    if (product.id < 0) return;

    function renderInfoToHtml() {
        // 聯系方式類型限制以及自定義字段
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
        $('#invent').html('庫存: ' + product.count2);
        $('#description').html(renderQuill(product.description)).show();

        if (product.price_whole && typeof product.price_whole === 'string') {
            product.price_whole = JSON.parse(product.price_whole);
        }

        if (product.price_whole && product.price_whole.length) {
            var msg = '';
            product.price_whole.forEach(function (e) {
                msg += '滿' + e[0] + '件，單價<b>' + (e[1] / 100).toFixed(2) + '</b>元<br>';
            });
            $('#discount-btn').fadeIn();
            $('#discount-tip').html('<p>優惠<br>' + msg + '<p>')
        }

        if (product.support_coupon) {
            $('#coupon-box').fadeIn();
        }

        currentProduct = product;
        calcTotalPrice();
    }


    if (product.password_open && !product.password) {
        var currentChoose = product;
        passwordDialog('請輸入商品密碼', function (password) {
            if (!password || !password.length) {
                showToast('warn', '請輸入商品密碼');
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
        showToast('warn', '優惠券信息無效')
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
        $('#coupon-tip').text('立減' + off + ', 已優惠:' + (discount / 100).toFixed(2));
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
            fee_tip = ' <span style="font-size: 8px">(手續費' + (fee / 100).toFixed(2) + ') </span>';
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
            var tip = '此商品只能購買&nbsp;' + currentProduct.buy_min + '</b>&nbsp;件'
        } else {
            tip = '最少購買&nbsp;<b>' + currentProduct.buy_min + '</b>&nbsp;件<br>最多購買&nbsp;<b>' + currentProduct.buy_max + '</b>&nbsp;件';
        }
        msg({
            title: '提示',
            content: '購買數目限制<br>' + tip,
            btn: ['關閉'],
            then: function () {
                $('#quantity').val(currentProduct.buy_min).focus()
            }
        });
        return false;
    }
    if (currentProduct.count === 0) {
        msg({
            title: '提示',
            content: '當前商品庫存不足',
            btn: ['關閉']
        });
        return false;
    }
    if (+currentProduct.count && +currentProduct.count < count) {
        msg({
            title: '提示',
            content: '購買數目不能超出商品庫存<br>當前商品庫存&nbsp;<b>' + currentProduct.count + '</b>&nbsp;件',
            btn: ['關閉'],
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

    showOrderTip('請在彈出的窗口完成付款<br><span style="font-size:13px">如果沒有彈出窗口或付款失敗，您也可以返回重新發起付款</span>', function () {
        window.open('/s#/record?tab=cookie', '_blank')
    });
}

function checkOrder() {
    if (!currentCategory) {
        showToast('error', '請選擇商品分類');
        $('#categories').focus();
        return false; // 阻止冒泡
    }

    if (!currentProduct) {
        showToast('error', '請選擇商品');
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
        if (!contact) return showError('請填寫您的聯系信息，如QQ、郵箱、手機號等等，用于查詢訂單');
        if (contact.length < 6) return showError('聯系方式長度至少為6位');
    } else if (contactType === 'email') {
        if (!contact) return showError('請填寫您的郵箱，用于查詢訂單');
        if (!validateEmail(contact)) return showError('輸入的郵箱格式不正確');
    } else if (contactType === 'mobile') {
        if (!contact) return showError('請填寫您的手機號碼，用于查詢訂單');
        if (!validateMobile(contact)) return showError('輸入的手機號格式不正確');
    } else if (contactType === 'qq') {
        if (!contact) return showError('請填寫您的QQ號碼，用于查詢訂單');
        if (contact.length < 5 || contact.length > 11 || !validatNumbers(this.contact))
            return showError('輸入的QQ號碼格式不正確');
    }

    if (contactExtValues.length) {
        for (var i = 0; i < contactExtValues.length; i++) {
            if (!contactExtValues[i]) {
                return showError('請填寫 ' + contactExt[i])
            }
        }
    }

    var sendSms = $('#send-sms')[0];
    if (sendSms && sendSms.checked) {
        var smsTo = $('#sms_to').val();
        if (!smsTo) {
            return showError('請填寫需要接受訂單信息的手機號碼', '#sms_to');
        }
        if (!validateMobile(smsTo)) {
            return showError('輸入的手機號格式不正確', '#sms_to');
        }
    }

    var sendMail = $('#send-mail')[0];
    if (sendMail && sendMail.checked) {
        var mailTo = $('#mail_to').val();
        if (!mailTo) {
            return showError('請填寫需要接受訂單信息的郵箱', '#mail_to');
        }
        if (!validateEmail(mailTo)) {
            return showError('輸入的郵箱格式不正確', '#mail_to');
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
                title: '店鋪公告',
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
            getProducts(config.categories[0].id);  // 一個分類時, 商品預加載
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
                offline: !gt_config.success, // 表示用戶后臺檢測極驗服務器是否宕機
                product: 'bind', // 這里注意, 2.0請改成 popup
                width: '300px',
                https: true
                // 更多配置參數說明請參見：http://docs.geetest.com/install/client/web-front/
            }, function (captchaObj) {
                captchaObj.onReady(function () {
                    console.log('geetest: onReady')
                });
                captchaObj.onError(function (e) {
                    console.log('geetest: onError');
                    console.error(e);
                    msg({
                        title: '出錯了',
                        content: '下單驗證碼加載失敗, 請刷新重試',
                        type: 'error',
                        then: function () {
                            location.reload();
                        }
                    })
                });
                captchaObj.onSuccess(function () {
                    var result = captchaObj.getValidate();
                    if (!result) {
                        return alert('請完成驗證');
                    }
                    codeValidate = {
                        'captcha.a': gt_config.key,
                        'captcha.b': result.geetest_challenge,
                        'captcha.c': result.geetest_validate,
                        'captcha.d': result.geetest_seccode
                    };
                    msg({
                        title: '驗證完成',
                        content: '驗證成功，請點擊按鈕跳轉支付頁面',
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
                    // 3.0 沒有 bindOn 接口
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
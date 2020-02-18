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
                message: e.responseText ? JSON.parse(e.responseText).message : '網絡連接錯誤: ' + type,
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

function toCurrency(a) {
    return '$ ' + (a / 100).toFixed(2).replace(/\d(?=(?:\d{3})+\b)/g, '$&,')
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
                name: '此分類下沒有商品'
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
            item_btn_price.innerText = '$' + (product.price / 100);

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
    $('.title-item h1').text('請選擇商品');
    $('#shop-crumbs-content a:eq(2)').text('');
    $('#priceWholeBtn').hide();
    $('.buyBtn').text('立即購買').attr('disabled', true);
    $('#count').val('1');

    if (!product) {
        // 如果是空, 則取消選中, 并返回
        return;
    }
    if (product.id < 1) {
        // 如果是小于的, 說明是一個提示, 直接返回
        currentProduct = null;
        return;
    }

    function renderInfoToHtml() {
        productsElm.children('[data-id=' + product.id + ']').addClass('active');

        // 單價
        $('#price b').text('$' + (product.price / 100));

        // 商品標題
        $('.title-item h1').text(product.name);

        // 左上角面包屑
        $('#shop-crumbs-content a:eq(2)').text(product.name);

        if (product.count < 1) {
            $('.buyBtn').text('售罄');
        } else {
            // 購買按鈕
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
            title: '請輸入商品密碼',
            message: '<input id="product-password-input" style="width: 90%;max-width: 320px;margin: 12px auto 0;display: block;">',
            btn: '確定',
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
                // 如果在請求過程中已經選擇了另一個分類, 則返回
                return;
            }
            if (!res.data.length) {
                res.data = [{
                    id: -2,
                    name: '此分類下沒有商品'
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
            title: '請輸入分類密碼',
            message: '<input id="category-password-input" style="width: 90%;max-width: 320px;margin: 12px auto 0;display: block;">',
            btn: '確定',
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
        $('.product-priceBox:first p:eq(1)').text('數量：' + buyCount);
        var price = currentProduct.price * buyCount;
        if (currentProduct.price_whole) {
            for (var i = currentProduct.price_whole.length - 1; i >= 0; i--) {
                var minCount = parseInt(currentProduct.price_whole[i][0]);
                if (buyCount >= minCount) {
                    console.log('批發價格生效', currentProduct.price_whole[i]);
                    $('.shopList ul').append('<li class="mainList"><div>' +
                        '<p>滿' + minCount + '件, 單價' + toCurrency(parseInt(currentProduct.price_whole[i][1])) + '</p>' +
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
            $('.shopList ul').append('<li class="mainList"><div><p>優惠券立減</p></div><div class="product-priceBox"><p>- ' + toCurrency(discount) + '</p></div></li>')

            $('#coupon-tip').text('立減' + off + ', 已優惠:' + (discount / 100).toFixed(2));
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
                $('.shopList ul').append('<li class="mainList"><div><p>手續費</p></div><div class="product-priceBox"><p>' + toCurrency(fee) + '</p></div></li>')
            }
        }

        var sendSms = $('#send-sms')[0];
        if (sendSms && sendSms.checked) {
            price_pay += config.sms_send_order.sms_price;

            if ($('#list-sms-fee')) {
                $('.shopList ul').append('<li class="mainList" id="list-sms-fee"><div><p>短信接收訂單</p></div><div class="product-priceBox"><p>' + toCurrency(config.sms_send_order.sms_price) + '</p></div></li>')
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
        $('#coupon-tip').text('x 優惠券信息無效')
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

    // 聯系方式類型限制以及自定義字段
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


    // 是否勾選附加服務
    var sms_to = $('.extService .option-sms').hasClass('active');
    $('#send-sms').attr('checked', sms_to);
    $('#sms_to-box')[sms_to ? 'show' : 'hide']();

    var mail_to = $('.extService .option-mail').hasClass('active');
    $('#send-mail').attr('checked', mail_to);
    $('#mail_to-box')[mail_to ? 'show' : 'hide']();


    // 右側名稱與價格
    $('.product-name:first p').text(currentProduct.name);
    $('.product-priceBox:first p:eq(0)').text(toCurrency(currentProduct.price));
    calcTotalPrice();

    $('#invent').html('庫存: ' + currentProduct.count2);

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

    // 先彈出 后提示, 延時一會保證已經彈出
    setTimeout(function () {
        msg({
            message: '請在彈出的窗口完成付款<br>如果沒有彈出窗口或付款失敗，您也可以返回并重新提交訂單',
            btn: '已付款，查詢訂單',
            btnfunc: function () {
                window.open('/s#/record?tab=cookie', '_blank')
            }
        });
    }, 200);
}

function checkOrder() {
    if (!currentCategory) {
        msg('請選擇商品分類');
        $('#categories').focus();
        return false; // 阻止冒泡
    }

    if (!currentProduct) {
        msg('請選擇商品');
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
            var tip = '此商品只能購買&nbsp;' + currentProduct.buy_min + '</b>&nbsp;件'
        } else {
            tip = '最少購買&nbsp;<b>' + currentProduct.buy_min + '</b>&nbsp;件<br>最多購買&nbsp;<b>' + currentProduct.buy_max + '</b>&nbsp;件';
        }
        showError('商品購買數量出錯，當前商品<br>' + tip, '#count');
        return false;
    }
    if (currentProduct.count === 0) {
        showError('當前商品庫存不足', '#count');
        return false;
    }
    if (+currentProduct.count && +currentProduct.count < count) {
        showError('購買數量不能超出商品庫存<br>當前商品庫存&nbsp;<b>' + currentProduct.count + '</b>&nbsp;件', '#count');
        return false;
    }

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

    return true;
}

$(function () {
    Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('mqqapi');

    // 手機端菜單展示/隱藏
    $('.nav-mobile-menu-btn').click(function () {
        $(this).toggleClass('active');
        $('.nav-mobile-menu').toggle();
    });

    // 手機端菜單展示時, 點擊任意按鈕隱藏
    $('.nav-mobile-menu a').click(function () {
        $('.nav-mobile-menu-btn').toggleClass('active');
        $('.nav-mobile-menu').hide();
    });

    // 調整大小時, 隱藏菜單
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
                title: '店鋪公告',
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
        selectCategory(config.categories[0]); // 一個分類時, 選擇分類, 然后加載商品
        if (config.categories[0].password_open === false) {
            categoriesElm.prop('disabled', true);
        }
    }

    // 價格顯示批發價
    $('#priceWholeBtn').click(function () {
        if (!currentProduct || !currentProduct.price_whole) return;
        var txt = '';
        for (var i = 0; i < currentProduct.price_whole.length; i++) {
            txt += '<p tabindex="0">滿' + currentProduct.price_whole[i][0] + '件，每件' + toCurrency(currentProduct.price_whole[i][1]) + '</p>'
        }
        msg('<h3>批發價格</h3>' + txt);
    });


    // 附加服務按鈕
    $('.extService .option>a').click(function () {
        $(this).parent().toggleClass('active');
    });


    // 立即購買!
    $('.buyBtn').click(function () {
        if (!currentProduct) return;
        goCheckout();
        var state = {c_id: currentCategory.id, p_id: currentProduct.id};
        state.url = location.protocol + '//' + location.host + location.pathname + '?' + $.param(state);
        window.history.pushState(state, '', state.url);
    });


    // 結算頁面
    $('#count').change(function () {
        $('#product-priceBox p:eq(1)').text('數量：' + $(this).val());
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
            // forward, 結算頁面
            goCheckout();
        } else {
            // backward, 返回首頁
            $('#page-checkout').hide();
            $('#page-1').show();
        }
    });


    // 初始化, 清空地址欄信息
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
                        message: '下單驗證碼加載失敗, 請刷新重試',
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
                        message: '驗證成功，請點擊按鈕跳轉支付頁面',
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
                    // 3.0 沒有 bindOn 接口
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
var currentCategory = null;
var currentProduct = null;
var currentCouponInfo = null;
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

function msg(m) {
    return alert(m.message);
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

function selectCategory(category) {
    currentCategory = category;
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
            item.appendChild(item_btn);
            item.setAttribute('data-id', product.id);
            productElms.push(item)
        });
        productsElm.html(productElms);
        selectProduct(category.products[0]);
    }
}

function selectProduct(product) {
    currentProduct = product;
    var productsElm = $('#products');
    productsElm.children().removeClass('active');
    if(!product) return;

    productsElm.children('[data-id=' + product.id + ']').addClass('active');
    $('#price b').text('￥' + (product.price / 100));
    $('.title-item h1').text(product.name);



    // description-content
}

function getProducts(category, callback) {
    category.products = [];
    callback && callback();
}


$(function () {
    console.log('ready!');

    // 手机端菜单展示/隐藏
    $('.nav-mobile-menu-btn').click(function () {
        $(this).toggleClass('active');
        $('.nav-mobile-menu').toggle();
    });

    // 手机端菜单展示时, 点击任意按钮隐藏
    $('.nav-mobile-menu a').click(function () {
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
    config.categories.forEach(function (category) {
        var item = document.createElement('li');
        var item_btn = document.createElement('a');
        item_btn.innerText = category.name;
        item_btn.addEventListener('click', function () {
            selectCategory(category);
        });
        item.appendChild(item_btn);
        item.setAttribute('data-id', category.id);
        categoryElms.push(item)
    });


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
    };

    if (shopType === 'product') {
        var tmp = document.createElement('div');
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

        if (config.categories.length) {
            selectCategory(config.categories[0]);

            getProducts(config.categories[0].id);  // 一个分类时, 商品预加载
            if (config.categories[0].password_open === false) {
                categoriesElm.prop('disabled', true);
            }
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
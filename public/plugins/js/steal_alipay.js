function goPage(targetUrl, packageName, forceIntent) {
    "use strict";


    function compareVer(e, t) {
        for (var o = e.split("."), n = t.split("."), a = 0; a < o.length || a < n.length; a += 1) {
            var r = parseInt(o[a], 10) || 0
                , i = parseInt(n[a], 10) || 0;
            if (r < i)
                return -1;
            if (r > i)
                return 1
        }
        return 0
    }

    function openLinkByIframe(e) {
        console.log("in iframe func", e),
        S || (console.log("create iframe"),
            S = x.createElement("iframe"),
            S.id = "callapp_iframe_" + Date.now(),
            S.frameborder = "0",
            S.style.cssText = "display:none;",
            x.body.appendChild(S)),
            S.src = e
    }

    function openLinkByClickA(link) {
        var t = x.createElement("a");
        t.setAttribute("href", link),
            t.style.display = "none",
            x.body.appendChild(t);
        var o = x.createEvent("HTMLEvents");
        o.initEvent("click", !1, !1),
            t.dispatchEvent(o)
    }

    function isUrl(e) {
        return /^(http|https)\:\/\//.test(e)
    }

    var userAgent = "";
    userAgent = window.navigator.userAgent;
    var g = !1
        , m = !1
        , h = ""
        , b = userAgent.match(/Android[\s\/]([\d\.]+)/);
    b ? (g = !0,
        h = b[1]) : userAgent.match(/(iPhone|iPad|iPod)/) && (m = !0,
        b = userAgent.match(/OS ([\d_\.]+) like Mac OS X/),
    b && (h = b[1].split("_").join(".")));
    var v = !1
        , w = !1
        , y = !1;
    userAgent.match(/(?:Chrome|CriOS)\/([\d\.]+)/) ? (v = !0,
    userAgent.match(/Version\/[\d+\.]+\s*Chrome/) && (y = !0)) : userAgent.match(/iPhone|iPad|iPod/) && (userAgent.match(/Safari/) && userAgent.match(/Version\/([\d\.]+)/) ? w = !0 : userAgent.match(/OS ([\d_\.]+) like Mac OS X/) && (y = !0));
    var x = window.document
        , S = void 0;


    return (function goPage(targetUrl, packageName, forceIntent) {
        var finalUrl = targetUrl + '&_t=' + (new Date() - 0);
        console.log("targetUrl", targetUrl);
        var isOriginalChrome = g && v && !y
            , fixUgly = g && !!userAgent.match(/samsung/i) && compareVer(h, "4.3") >= 0 && compareVer(h, "4.5") < 0
            , ios9SafariFix = m && compareVer(h, "9.0") >= 0 && w;
        var inQQ = userAgent.toLowerCase().indexOf('qq/') > -1;
        console.log("isOriginalChrome", isOriginalChrome);
        console.log("fixUgly", fixUgly);
        console.log("ios9SafariFix", ios9SafariFix);
        console.log("inQQ", inQQ);
        console.log("forceIntent", forceIntent);
        if (isOriginalChrome || forceIntent) {
            var scheme = finalUrl.substring(0, finalUrl.indexOf("://"))
                , intentUrl = "#Intent;scheme=" + scheme + ";package=" + packageName + ";end";
            finalUrl = finalUrl.replace(/.*?\:\/\//, "intent://"),
                finalUrl += intentUrl,
                console.log("Intent", finalUrl)
        }
        if (ios9SafariFix || inQQ) {
            openLinkByClickA(finalUrl);
        } else if (0 === finalUrl.indexOf("intent:")) {
            console.log("jump intent");
            window.location.href = finalUrl
        } else {
            openLinkByIframe(finalUrl);
        }
    })(targetUrl, packageName, forceIntent);
}
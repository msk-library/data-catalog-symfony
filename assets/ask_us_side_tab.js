(function () {
    var options = {
        "id": 23227,
        "hash": "cf71745d2f1f46b229e56d412606c4e6",
        "siteUrl": "https:\/\/mskcc.libanswers.com",
        "iid": 2877,
        "onlinerules": [{
            "u": 0,
            "d": [7294],
            "c": ""
        }],
        "width": "400px",
        "height": "340px",
        "color_backg": "#F9F9F9",
        "autoload_time": 0,
        "slidebutton_url": "",
        "slidebutton_text": "Help",
        "slidebutton_height": "125px",
        "slidebutton_bcolor": "#0073e0",
        "slidebutton_color": "#FFFFFF",
        "slidebutton_position": "r",
        "botAvailability": -1,
        "botUseAutoload": false,
        "botCustomHours": {}
    };
    var cascadeServer =
        "https:\/\/chat-us.libanswers.com";
    var referer = "";
    var refererTitle = "";
    const removeCoverageFromRule =
        function (rule) {
            return {
                u: rule.u,
                d: rule.d,
                c: rule.c,
                fallbackSeconds: rule
                    .fallbackSeconds ||
                    0
            }
        },
        isCoverageInRange =
            function (hour, minute,
                ranges) {
                if (0 === ranges.length)
                    return !1;
                for (let i = 0; i <=
                    ranges.length; i++
                ) {
                    const range =
                        ranges[i];
                    if (!Array.isArray(
                        range) ||
                        2 !== range
                            .length)
                        continue;
                    const start = range[
                        0].split(
                            ":").map((
                                el =>
                                    parseInt(
                                        el,
                                        10)
                            )),
                        end = range[1]
                            .split(":").map(
                                (el =>
                                    parseInt(
                                        el,
                                        10))
                            );
                    if (!(start[0] >
                        hour || end[
                        0] <
                        hour)) {
                        if (start[0] <
                            hour && end[
                            0] >
                            hour)
                            return !0;
                        if (start[0] !==
                            hour) {
                            if (end[
                                0] !==
                                hour);
                            else if (
                                end[1] >
                                minute)
                                return !
                                    0
                        } else if (
                            start[1] <
                            minute)
                            return !0
                    }
                }
                return !1
            },
        adjustStatusRequestForCoverage =
            function (onlineRules) {
                const finalRules = [],
                    now = new Date,
                    day = now
                        .getUTCDay(),
                    hour = now
                        .getUTCHours(),
                    minute = now
                        .getUTCMinutes();
                return onlineRules
                    .forEach((rule => {
                        if (!
                            rule
                                .c ||
                            1 ===
                            rule
                                .coverageType
                        )
                            return void finalRules
                                .push(
                                    removeCoverageFromRule(
                                        rule
                                    )
                                );
                        if (0 ===
                            rule
                                .coverageType
                        )
                            return;
                        const
                            times =
                                rule
                                    .coverageHours;
                        if (!
                            times ||
                            !
                            times[
                            day
                            ] ||
                            0 ===
                            times[
                                day
                            ]
                                .length
                        )
                            return;
                        const
                            ranges =
                                times[
                                day
                                ];
                        isCoverageInRange
                            (hour,
                                minute,
                                ranges
                            ) &&
                            finalRules
                                .push(
                                    removeCoverageFromRule(
                                        rule
                                    )
                                )
                    })), finalRules
            },
        isItDuringBotCustomHours =
            function (customHours) {
                const now = new Date,
                    day = now
                        .getUTCDay(),
                    hour = now
                        .getUTCHours(),
                    minute = now
                        .getUTCMinutes(),
                    times = customHours;
                if (!times || !times[
                    day] || 0 ===
                    times[day].length)
                    return !1;
                const ranges = times[
                    day];
                return isCoverageInRange(
                    hour, minute,
                    ranges)
            },
        isBotActiveWithoutStatusCheck =
            function (botAvailability,
                customHours) {
                return 1 ===
                    botAvailability ||
                    4 ===
                    botAvailability &&
                    isItDuringBotCustomHours(
                        customHours)
            };;
    const tabWidget = {
        config: {},
        online: !1,
        loaded: !1,
        statusChecked: !1,
        autoload: !1,
        isCoopOnline: !1,
        chatContainer: null,
        chatButton: null,
        overlay: null,
        iframeContainer: null,
        iframe: null,
        chatTimer: null,
        referer: "",
        refererTitle: "",
        boundKeyboardHandler: null,
        boundOverlayHandler: null,
        narrowScreen: null,
        chatWindow: null,
        isNarrowScreen: function () {
            if (null !==
                this
                    .narrowScreen
            ) return this
                .narrowScreen;
            const
                mediaQuery =
                    window
                        .matchMedia(
                            "(max-width: 700px)"
                        );
            return this
                .narrowScreen =
                mediaQuery
                    .matches,
                this
                    .narrowScreen
        },
        getWidgetUrl: function () {
            "" === this
                .referer &&
                (this
                    .referer =
                    window
                        .location
                        .href),
                "" === this
                    .refererTitle &&
                window
                    .document
                    .title && (
                    this
                        .refererTitle =
                    window
                        .document
                        .title);
            let authId = 0;
            window
                .springSpace &&
                window
                    .springSpace
                    .la &&
                window
                    .springSpace
                    .la.Page &&
                window
                    .springSpace
                    .la.Page
                    .auth_id &&
                (authId =
                    window
                        .springSpace
                        .la.Page
                        .auth_id
                );
            let qs =
                `${this.config.siteUrl}/chat/widget/${this.config.hash}?referer=${encodeURIComponent(this.referer)}&referer_title=${encodeURIComponent(this.refererTitle)}&auth_id=${authId}`;
            return this
                .autoload &&
                (qs +=
                    "&auto=true",
                    this
                        .autoload = !
                    1), qs
        },
        deleteAutoPopDeny: function () {
            try {
                localStorage
                    .removeItem(
                        "libchat_auto"
                    )
            } catch (e) { }
        },
        autoPopDenied: function () {
            try {
                let obj =
                    localStorage
                        .getItem(
                            "libchat_auto"
                        );
                if ("" ===
                    obj)
                    return !
                        1;
                obj = JSON
                    .parse(
                        obj
                    );
                return !(
                    Math
                        .floor(
                            Date
                                .now() /
                            1e3
                        ) -
                    obj
                        .date >
                    3600
                ) ||
                    (this
                        .deleteAutoPopDeny(),
                        !1)
            } catch (e) {
                this.deleteAutoPopDeny()
            }
            return !1
        },
        autoLoadWidget: function () {
            this.autoPopDenied() ||
                (this
                    .autoload = !
                    0, this
                        .checkStatus()
                )
        },
        setTimer: function () {
            this.isNarrowScreen() ||
                !this.config
                    .autoload_time ||
                0 ===
                parseInt(
                    this
                        .config
                        .autoload_time,
                    10) ||
                this
                    .autoPopDenied() ||
                (this
                    .chatTimer =
                    window
                        .setTimeout(
                            this
                                .autoLoadWidget
                                .bind(
                                    this
                                ),
                            1e3 *
                            parseInt(
                                this
                                    .config
                                    .autoload_time,
                                10
                            )
                        ))
        },
        openWidget: function () {
            this.iframeContainer
                .style
                .display =
                "block",
                this
                    .chatContainer
                    .classList
                    .add(
                        "open"),
                this
                    .iframeContainer
                    .setAttribute(
                        "aria-hidden",
                        "false"
                    ), this
                        .chatButton
                        .setAttribute(
                            "aria-expanded",
                            !0),
                this
                    .boundKeyboardHandler =
                this
                    .keyboardCloseWidget
                    .bind(this),
                document
                    .body
                    .addEventListener(
                        "keydown",
                        this
                            .boundKeyboardHandler
                    )
        },
        closeWidget: function () {
            this.iframeContainer
                .style
                .display =
                "none", this
                    .chatContainer
                    .classList
                    .remove(
                        "open"),
                this
                    .iframeContainer
                    .setAttribute(
                        "aria-hidden",
                        "true"),
                this
                    .chatButton
                    .setAttribute(
                        "aria-expanded",
                        !1),
                document
                    .body
                    .removeEventListener(
                        "keydown",
                        this
                            .boundKeyboardHandler
                    ), this
                        .boundOverlayHandler &&
                this.overlay
                    .removeEventListener(
                        "click",
                        this
                            .boundOverlayHandler
                    ), this
                        .overlay
                        .style
                        .display =
                "none"
        },
        keyboardCloseWidget: function (
            ev) {
            "Escape" === ev
                .key && this
                    .closeWidget()
        },
        handleWidgetToggle: function (
            ev) {
            ev.preventDefault(),
                window
                    .clearTimeout(
                        this
                            .chatTimer
                    ), this
                        .chatContainer
                        .classList
                        .contains(
                            "open"
                        ) ? this
                            .closeWidget() :
                    (this
                        .statusChecked ||
                        this
                            .checkStatus(),
                        this
                            .openWidget()
                    )
        },
        openChat: function (ev) {
            if (ev
                .preventDefault(),
                this
                    .chatWindow &&
                !this
                    .chatWindow
                    .closed)
                this
                    .chatWindow
                    .focus();
            else {
                const
                    chatUrl =
                        this
                            .getWidgetUrl();
                this.chatWindow =
                    window
                        .open(
                            chatUrl,
                            "libchat",
                            `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=${this.config.width}, height=${this.config.height}`
                        )
            }
        },
        buildTab: function () {
            this.chatContainer =
                document
                    .createElement(
                        "div"),
                this
                    .chatContainer
                    .id =
                `lcs_slide_out-${this.config.id}`,
                this
                    .chatContainer
                    .setAttribute(
                        "role",
                        "region"
                    ), this
                        .chatContainer
                        .setAttribute(
                            "aria-label",
                            this
                                .config
                                .slidebutton_text
                        ), this
                            .chatContainer
                            .classList
                            .add(
                                "lcs_slide_out"
                            ), this
                                .chatContainer
                                .classList
                                .add(
                                    `lcs_slide_out-${this.config.slidebutton_position}`
                                );
            const openerId =
                `lcs_slide_out_button-${this.config.id}`;
            this.overlay =
                document
                    .createElement(
                        "div"),
                this.overlay
                    .classList
                    .add(
                        "tab_overlay"
                    ), this
                        .chatContainer
                        .appendChild(
                            this
                                .overlay
                        );
            const
                chat_header =
                    document
                        .createElement(
                            "div");
            if (chat_header
                .classList
                .add(
                    "lcs_header"
                ), this
                    .chatButton =
                document
                    .createElement(
                        "a"),
                this
                    .chatButton
                    .id =
                openerId,
                this
                    .chatButton
                    .setAttribute(
                        "role",
                        "button"
                    ), this
                        .chatButton
                        .setAttribute(
                            "aria-controls",
                            `lcs_slide_out-${this.config.id}`
                        ), this
                            .chatButton
                            .setAttribute(
                                "aria-expanded",
                                !1),
                this
                    .chatButton
                    .setAttribute(
                        "href",
                        "#"),
                chat_header
                    .appendChild(
                        this
                            .chatButton
                    ), this
                        .chatContainer
                        .appendChild(
                            chat_header
                        ), this
                            .iframeContainer =
                document
                    .createElement(
                        "div"),
                this
                    .iframeContainer
                    .classList
                    .add(
                        "lcs_load"
                    ), this
                        .iframeContainer
                        .setAttribute(
                            "aria-hidden",
                            "true"),
                this
                    .chatContainer
                    .appendChild(
                        this
                            .iframeContainer
                    ), this
                        .config
                        .slidebutton_url &&
                "" !== this
                    .config
                    .slidebutton_url
            ) {
                const img =
                    document
                        .createElement(
                            "img"
                        );
                img.setAttribute(
                    "src",
                    this
                        .config
                        .slidebutton_url
                ),
                    img
                        .setAttribute(
                            "alt",
                            this
                                .config
                                .slidebutton_text
                        ),
                    this
                        .chatButton
                        .style
                        .padding =
                    "0",
                    this
                        .chatButton
                        .appendChild(
                            img)
            } else this
                .config
                .slidebutton_color ||
                (this.config
                    .slidebutton_color =
                    "#ffffff"
                ), this
                    .chatButton
                    .innerHTML =
                this.config
                    .slidebutton_text,
                this
                    .chatButton
                    .style
                    .borderColor =
                this.config
                    .slidebutton_color,
                this
                    .chatButton
                    .style
                    .color =
                this.config
                    .slidebutton_color,
                this
                    .chatButton
                    .style
                    .backgroundColor =
                this.config
                    .slidebutton_bcolor,
                this
                    .chatButton
                    .style
                    .boxShadow =
                "rgb(204, 204, 204) 0px 0px 5px",
                "b" === this
                    .config
                    .slidebutton_position ?
                    this
                        .chatButton
                        .style
                        .borderWidth =
                    "4px 4px 0px 4px" :
                    this
                        .chatButton
                        .style
                        .borderWidth =
                    "0px 4px 4px 4px";
            document.body
                .appendChild(
                    this
                        .chatContainer
                ), this
                    .isNarrowScreen() ?
                    this
                        .chatButton
                        .addEventListener(
                            "click",
                            this
                                .openChat
                                .bind(
                                    this
                                )) :
                    this
                        .chatButton
                        .addEventListener(
                            "click",
                            this
                                .handleWidgetToggle
                                .bind(
                                    this
                                ))
        },
        handleMessage: function (
            ev) {
            const data = ev
                .data;
            if ("object" ==
                typeof data &&
                data.action)
                switch (data
                    .action
                ) {
                    case "peelout":
                    case "closeWidget":
                        this.chatButton
                            .focus(),
                            this
                                .closeWidget(),
                            this
                                .iframeContainer
                                .removeAttribute(
                                    "aria-live"
                                ),
                            this
                                .iframeContainer
                                .setAttribute(
                                    "aria-hidden",
                                    "true"
                                );
                        break;
                    case "chatStarted":
                        this.boundOverlayHandler &&
                            this
                                .overlay
                                .removeEventListener(
                                    "click",
                                    this
                                        .boundOverlayHandler
                                ),
                            this
                                .overlay
                                .style
                                .display =
                            "none";
                        break;
                    case "autopop":
                        this.openWidget(),
                            this
                                .boundOverlayHandler =
                            this
                                .closeWidget
                                .bind(
                                    this
                                ),
                            this
                                .overlay
                                .addEventListener(
                                    "click",
                                    this
                                        .boundOverlayHandler
                                ),
                            this
                                .overlay
                                .style
                                .display =
                            "block"
                }
        },
        loadIframe: function () {
            if (this.iframe)
                return;
            if ((!1 === this
                .online ||
                this
                    .isCoopOnline
            ) && !
                this.config
                    .botUseAutoload &&
                !0 === this
                    .autoload)
                return this
                    .autoload = !
                    1, void (
                        this
                            .statusChecked = !
                        1);
            const
                widgetUrl =
                    this
                        .getWidgetUrl();
            !0 === this
                .autoload &&
                (this
                    .autoload = !
                    1, this
                        .iframeContainer
                        .setAttribute(
                            "aria-live",
                            "polite"
                        ),
                    this
                        .iframeContainer
                        .setAttribute(
                            "aria-hidden",
                            "false"
                        )),
                window
                    .addEventListener(
                        "message",
                        this
                            .handleMessage
                            .bind(
                                this
                            )),
                this
                    .iframe =
                document
                    .createElement(
                        "iframe"
                    ), this
                        .iframe
                        .setAttribute(
                            "id",
                            `iframe_${this.config.hash}`
                        ), this
                            .iframe
                            .setAttribute(
                                "name",
                                `iframe_${this.config.hash}`
                            ), this
                                .iframe
                                .setAttribute(
                                    "src",
                                    widgetUrl
                                ), this
                                    .iframe
                                    .setAttribute(
                                        "title",
                                        "Chat Widget"
                                    ), this
                                        .iframe
                                        .setAttribute(
                                            "scrolling",
                                            "no"),
                this.iframe
                    .innerHTML =
                "Content is loading...",
                this
                    .iframeContainer
                    .appendChild(
                        this
                            .iframe)
        },
        statusError: function () {
            this.online = !
                1, this
                    .loadIframe()
        },
        statusSuccess: function (
            data) {
            this.online = !
                1, (data
                    .u ||
                    data
                        .d ||
                    data.c
                ) && (
                    this
                        .online = !
                    0), data
                        .c && data.c
                            .length >
                    0 && (this
                        .isCoopOnline = !
                        0), this
                            .loadIframe()
        },
        statusComplete: function (
            ev) {
            const xhr = ev
                .target,
                status = xhr
                    .status;
            if (status >=
                200 &&
                status < 300
            ) try {
                this.statusSuccess(
                    JSON
                        .parse(
                            xhr
                                .responseText
                        )
                )
            } catch (
                e) {
                    this.statusError()
                } else this
                    .statusError()
        },
        checkStatus: function () {
            if (isBotActiveWithoutStatusCheck(
                this
                    .config
                    .botAvailability,
                this
                    .config
                    .botCustomHours
            ))
                return void this
                    .loadIframe();
            const
                adjustedRules =
                    adjustStatusRequestForCoverage(
                        this
                            .config
                            .onlinerules
                    );
            this.statusChecked = !
                0;
            const xhr =
                new XMLHttpRequest;
            xhr.onload =
                this
                    .statusComplete
                    .bind(this),
                xhr
                    .onerror =
                this
                    .statusError
                    .bind(this),
                xhr.open(
                    "GET",
                    `${this.cascadeServer}/widget_status?iid=${this.config.iid}&rules=${encodeURIComponent(JSON.stringify(adjustedRules))}`
                ), xhr
                    .send()
        },
        insertWidgetCSS: function () {
            let b_btn_offset =
                "-42px";
            this.config
                .slidebutton_url &&
                "" !== this
                    .config
                    .slidebutton_url &&
                this.config
                    .slidebutton_height &&
                "" !== this
                    .config
                    .slidebutton_height &&
                (b_btn_offset =
                    `-${this.config.slidebutton_height}`
                );
            const
                iframeHeight =
                    parseInt(
                        this
                            .config
                            .height,
                        10) - 2,
                css =
                    `/* LibChat Widget CSS */\n        .lcs_slide_out { width: ${this.config.width}; height: ${this.config.height}; position: fixed; transition-duration: 0.6s; background-color: rgb(249, 249, 249); border: 1px solid #ccc; z-index: 1000; }\n        .lcs_slide_out div.lcs_header { display: block; z-index: 501; position: relative; }\n        .lcs_slide_out div.lcs_header a { text-align: center; display: block; position: absolute; border-width: 0; border-style: solid; background-color: ${this.config.slidebutton_bcolor}; }\n        .lcs_load { width: ${this.config.width}; height: 100%; padding: 0 2px 0 0; box-sizing: border-box; z-index: 501; position: relative; }\n        .lcs_slide_out-l { top: 100px; left: -${this.config.width}; }\n        .lcs_slide_out-l.open { left: 0px; }\n        .lcs_slide_out-l div.lcs_header { transform: rotate(-90deg); transform-origin: top right; }\n        .lcs_slide_out-l div.lcs_header a { right: 10px; }\n        .lcs_slide_out-r { top: 100px; right: -${this.config.width}; }\n        .lcs_slide_out-r.open { right: 0px; }\n        .lcs_slide_out-r div.lcs_header { transform: rotate(90deg); transform-origin: top left; }\n        .lcs_slide_out-r div.lcs_header a { left: 10px; }\n        .lcs_slide_out-b { right: 100px; bottom: -2px; height: 0; }\n        .lcs_slide_out-b.open { height: ${this.config.height}; }\n        .lcs_slide_out-b div.lcs_header a { left: 10px; bottom: 0px; border-width: 1px 1px 0px 1px; }\n        .lcs_slide_out-b.open div.lcs_header a { top: ${b_btn_offset}; bottom: auto; }\n        .lcs_slide_out iframe { width: 100%; height: ${iframeHeight}px; background-color: ${this.config.color_backg}; border: 0px; box-sizing:border-box; }\n        .lcs_slide_out div.tab_overlay { position: fixed; background-color: rgb(0, 0, 0); top: 0; left: 0; bottom: 0; right: 0; opacity: 0.0; z-index: 500; display: none; }\n        @media (max-width: 768px) {\n            .lcs_slide_out { width: 80%; }\n            .lcs_load { width: 100% }\n            .lcs_slide_out-l { left: -80%; }\n            .lcs_slide_out-r { right: -80%; }\n            .lcs_slide_out-b { left: 0; }\n        }`,
                head =
                    document
                        .head ||
                    document
                        .getElementsByTagName(
                            "head")[
                    0],
                style =
                    document
                        .createElement(
                            "style"
                        );
            style
                .styleSheet ?
                style
                    .styleSheet
                    .cssText =
                css : style
                    .appendChild(
                        document
                            .createTextNode(
                                css)
                    ), head
                        .appendChild(
                            style)
        },
        start: function () {
            !0 !== this
                .loaded && (
                    this
                        .loaded = !
                    0,
                    document
                        .querySelector(
                            `#lcs_slide_out-${this.config.id}`
                        ) ||
                    (this
                        .insertWidgetCSS(),
                        this
                            .buildTab(),
                        this
                            .setTimer(),
                        document
                            .addEventListener(
                                "visibilitychange",
                                (() => {
                                    "visible" !==
                                        document
                                            .visibilityState ||
                                        this
                                            .iframe ||
                                        (this
                                            .statusChecked = !
                                            1
                                        )
                                })
                            )
                    ))
        }
    };
    tabWidget.config = options,
        tabWidget.cascadeServer =
        cascadeServer, tabWidget
            .referer = referer,
        tabWidget.refererTitle =
        refererTitle, "complete" ===
            document.readyState ||
            "interactive" === document
                .readyState ? tabWidget
                    .start() : (document
                        .addEventListener(
                            "DOMContentLoaded",
                            tabWidget.start
                                .bind(tabWidget), !1
                        ), window
                            .addEventListener(
                                "load", tabWidget
                                    .start.bind(
                                        tabWidget), !1)
        );
})();
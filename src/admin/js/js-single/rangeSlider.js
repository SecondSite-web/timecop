!(function (t) {
    var e = {};
    function n(r) {
        if (e[r]) return e[r].exports;
        var i = (e[r] = { i: r, l: !1, exports: {} });
        return t[r].call(i.exports, i, i.exports, n), (i.l = !0), i.exports;
    }
    (n.m = t),
        (n.c = e),
        (n.d = function (t, e, r) {
            n.o(t, e) || Object.defineProperty(t, e, { enumerable: !0, get: r });
        }),
        (n.r = function (t) {
            "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(t, Symbol.toStringTag, { value: "Module" }), Object.defineProperty(t, "__esModule", { value: !0 });
        }),
        (n.t = function (t, e) {
            if ((1 & e && (t = n(t)), 8 & e)) return t;
            if (4 & e && "object" == typeof t && t && t.__esModule) return t;
            var r = Object.create(null);
            if ((n.r(r), Object.defineProperty(r, "default", { enumerable: !0, value: t }), 2 & e && "string" != typeof t))
                for (var i in t)
                    n.d(
                        r,
                        i,
                        function (e) {
                            return t[e];
                        }.bind(null, i)
                    );
            return r;
        }),
        (n.n = function (t) {
            var e =
                t && t.__esModule
                    ? function () {
                        return t.default;
                    }
                    : function () {
                        return t;
                    };
            return n.d(e, "a", e), e;
        }),
        (n.o = function (t, e) {
            return Object.prototype.hasOwnProperty.call(t, e);
        }),
        (n.p = ""),
        n((n.s = 5));
})([
    function (t, e, n) {
        "use strict";
        Object.defineProperty(e, "__esModule", { value: !0 });
        e.constants = { DIRECTION_VERTICAL: "vertical", DIRECTION_HORIZONTAL: "horizontal", TYPE_INTERVAL: "interval", TYPE_SINGLE: "single" };
    },
    function (t, e, n) {
        "use strict";
        Object.defineProperty(e, "__esModule", { value: !0 });
        var r = (function () {
            function t(t) {
                void 0 === t && (t = {}), (this.events = t);
            }
            return (
                (t.prototype.on = function (t, e) {
                    var n = this.events[t];
                    n ? n.push(e) : (this.events[t] = [e]);
                }),
                    (t.prototype.emit = function (t, e) {
                        var n = this.events[t];
                        n &&
                        n.forEach(function (t) {
                            return t(e);
                        });
                    }),
                    t
            );
        })();
        e.default = r;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i = function () {
                return void 0 === r && (r = Boolean(window && document && document.all && !window.atob)), r;
            },
            a = (function () {
                var t = {};
                return function (e) {
                    if (void 0 === t[e]) {
                        var n = document.querySelector(e);
                        if (window.HTMLIFrameElement && n instanceof window.HTMLIFrameElement)
                            try {
                                n = n.contentDocument.head;
                            } catch (t) {
                                n = null;
                            }
                        t[e] = n;
                    }
                    return t[e];
                };
            })(),
            s = [];
        function o(t) {
            for (var e = -1, n = 0; n < s.length; n++)
                if (s[n].identifier === t) {
                    e = n;
                    break;
                }
            return e;
        }
        function l(t, e) {
            for (var n = {}, r = [], i = 0; i < t.length; i++) {
                var a = t[i],
                    l = e.base ? a[0] + e.base : a[0],
                    u = n[l] || 0,
                    c = "".concat(l, " ").concat(u);
                n[l] = u + 1;
                var p = o(c),
                    d = { css: a[1], media: a[2], sourceMap: a[3] };
                -1 !== p ? (s[p].references++, s[p].updater(d)) : s.push({ identifier: c, updater: _(d, e), references: 1 }), r.push(c);
            }
            return r;
        }
        function u(t) {
            var e = document.createElement("style"),
                r = t.attributes || {};
            if (void 0 === r.nonce) {
                var i = n.nc;
                i && (r.nonce = i);
            }
            if (
                (Object.keys(r).forEach(function (t) {
                    e.setAttribute(t, r[t]);
                }),
                "function" == typeof t.insert)
            )
                t.insert(e);
            else {
                var s = a(t.insert || "head");
                if (!s) throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
                s.appendChild(e);
            }
            return e;
        }
        var c,
            p =
                ((c = []),
                    function (t, e) {
                        return (c[t] = e), c.filter(Boolean).join("\n");
                    });
        function d(t, e, n, r) {
            var i = n ? "" : r.media ? "@media ".concat(r.media, " {").concat(r.css, "}") : r.css;
            if (t.styleSheet) t.styleSheet.cssText = p(e, i);
            else {
                var a = document.createTextNode(i),
                    s = t.childNodes;
                s[e] && t.removeChild(s[e]), s.length ? t.insertBefore(a, s[e]) : t.appendChild(a);
            }
        }
        function f(t, e, n) {
            var r = n.css,
                i = n.media,
                a = n.sourceMap;
            if ((i ? t.setAttribute("media", i) : t.removeAttribute("media"), a && btoa && (r += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(a)))), " */")), t.styleSheet))
                t.styleSheet.cssText = r;
            else {
                for (; t.firstChild; ) t.removeChild(t.firstChild);
                t.appendChild(document.createTextNode(r));
            }
        }
        var h = null,
            v = 0;
        function _(t, e) {
            var n, r, i;
            if (e.singleton) {
                var a = v++;
                (n = h || (h = u(e))), (r = d.bind(null, n, a, !1)), (i = d.bind(null, n, a, !0));
            } else
                (n = u(e)),
                    (r = f.bind(null, n, e)),
                    (i = function () {
                        !(function (t) {
                            if (null === t.parentNode) return !1;
                            t.parentNode.removeChild(t);
                        })(n);
                    });
            return (
                r(t),
                    function (e) {
                        if (e) {
                            if (e.css === t.css && e.media === t.media && e.sourceMap === t.sourceMap) return;
                            r((t = e));
                        } else i();
                    }
            );
        }
        t.exports = function (t, e) {
            (e = e || {}).singleton || "boolean" == typeof e.singleton || (e.singleton = i());
            var n = l((t = t || []), e);
            return function (t) {
                if (((t = t || []), "[object Array]" === Object.prototype.toString.call(t))) {
                    for (var r = 0; r < n.length; r++) {
                        var i = o(n[r]);
                        s[i].references--;
                    }
                    for (var a = l(t, e), u = 0; u < n.length; u++) {
                        var c = o(n[u]);
                        0 === s[c].references && (s[c].updater(), s.splice(c, 1));
                    }
                    n = a;
                }
            };
        };
    },
    function (t, e, n) {
        "use strict";
        Object.defineProperty(e, "__esModule", { value: !0 }), (e.defaultModel = { min: 10, max: 50, step: 2, values: [20] });
    },
    function (t, e, n) {
        "use strict";
        Object.defineProperty(e, "__esModule", { value: !0 }), (e.defaultVisualModel = { direction: "horizontal", skin: "green", bar: !0, tip: !0, type: "single", scale: !1, settings: !1 });
    },
    function (t, e, n) {
        "use strict";
        Object.defineProperty(e, "__esModule", { value: !0 }), n(6);
    },
    function (t, e, n) {
        "use strict";
        (function (t) {
            var r =
                (this && this.__assign) ||
                function () {
                    return (r =
                        Object.assign ||
                        function (t) {
                            for (var e, n = 1, r = arguments.length; n < r; n++) for (var i in (e = arguments[n])) Object.prototype.hasOwnProperty.call(e, i) && (t[i] = e[i]);
                            return t;
                        }).apply(this, arguments);
                },
                i =
                    (this && this.__importDefault) ||
                    function (t) {
                        return t && t.__esModule ? t : { default: t };
                    };
            Object.defineProperty(e, "__esModule", { value: !0 });
            var a = i(n(8)),
                s = n(3),
                o = n(4),
                l = {
                    reset: function () {
                        var e = t(this).data("rangeSlider");
                        e.model.setState(t(this).data().startingModel), e.reCreateApplication(t(this).data().startingVisualModel);
                    },
                    destroy: function () {
                        t(this).data("rangeSlider").app.removeHTML(), t(this).off("onChange");
                    },
                    onChange: function (e) {
                        t(this).on("onChange", function (t) {
                            return e(t);
                        });
                    },
                };
            function u(e, n) {
                void 0 === e && (e = o.defaultVisualModel), void 0 === n && (n = s.defaultModel);
                var i = r(r({}, o.defaultVisualModel), e),
                    l = r(r({}, s.defaultModel), n);
                return this.each(function () {
                    (t(this).data().rangeSlider = new a.default(this, i, l)), (t(this).data().startingVisualModel = i), (t(this).data().startingModel = l);
                });
            }
            function c(e) {
                var n = e.VisualState,
                    r = e.ModelState,
                    i = t(this).data("rangeSlider");
                i.model.setState(r), i.reCreateApplication(Object.assign(i.visualModel.state, n));
            }
            t.fn.rangeSlider = function (e, n) {
                if ((t(this).data("rangeSlider") || u.call(this), "string" == typeof e)) {
                    if ("onChange" === e && "function" == typeof n) return l[e].call(this, n);
                    if (l[e] && "onChange" !== e) return l[e].call(this);
                }
                if ("object" == typeof e) return c.call(this, { VisualState: e, ModelState: n || {} });
            };
        }.call(this, n(7)));
    },
    function (t, e) {
        t.exports = $;
    },
    function (t, e, n) {
        "use strict";
        var r =
            (this && this.__assign) ||
            function () {
                return (r =
                    Object.assign ||
                    function (t) {
                        for (var e, n = 1, r = arguments.length; n < r; n++) for (var i in (e = arguments[n])) Object.prototype.hasOwnProperty.call(e, i) && (t[i] = e[i]);
                        return t;
                    }).apply(this, arguments);
            },
            i =
                (this && this.__importDefault) ||
                function (t) {
                    return t && t.__esModule ? t : { default: t };
                };
        Object.defineProperty(e, "__esModule", { value: !0 });
        var a = i(n(9)),
            s = i(n(10)),
            o = i(n(11)),
            l = (function () {
                function t(t, e, n) {
                    (this.anchor = t),
                        (this.settingsModel = n),
                        (this.model = new a.default(n)),
                        (this.visualModel = new s.default(e)),
                        (this.app = new o.default().main(this.visualModel.state, this.anchor)),
                        this.app.createUI(this.visualModel.state),
                        this.bindEvents(),
                        this.app.init(this.visualModel.state);
                }
                return (
                    (t.prototype.bindEvents = function () {
                        var t = this;
                        this.app.on("finishInit", function (e) {
                            return t.arrangeHandles(e);
                        }),
                            this.model.on("pxValueDone", function (e) {
                                return t.app.paint(e);
                            }),
                            this.app.on("onUserMove", function (e) {
                                return t.model.counting(e);
                            }),
                        this.app.UIs.settings &&
                        this.app.UIs.settings.on("newSettings", function (e) {
                            t.model.setState(e), t.arrangeHandles(e), e.step && t.reCreateApplication();
                        }),
                            this.model.on("pxValueDone", function () {
                                return t.app.UIs.settings && t.app.UIs.settings.setState(t.model.state, t.visualModel.state);
                            }),
                        this.app.UIs.settings &&
                        this.app.UIs.settings.on("reCreateApp", function (e) {
                            t.visualModel.setState(e), t.reCreateApplication();
                        }),
                            this.model.on("pxValueDone", function () {
                                return t.anchor.dispatchEvent(new CustomEvent("onChange", { detail: t.model.state }));
                            }),
                        this.app.UIs.scale &&
                        this.app.UIs.scale.on("newValueFromScale", function (e) {
                            t.model.setState(e), t.arrangeHandles(e);
                        });
                    }),
                        (t.prototype.arrangeHandles = function (t) {
                            var e = t.edge,
                                n = t.handles;
                            if (n) for (var r = 0; r < n.length; r += 1) this.model.counting({ edge: e, target: n[r], value: this.model.state.values[r] });
                        }),
                        (t.prototype.reCreateApplication = function (t) {
                            void 0 === t && (t = this.visualModel.state), this.app.removeHTML();
                            var e = r(r({}, this.settingsModel), this.model.state);
                            (this.model = new a.default(e)),
                                this.visualModel.setState(t),
                                (this.app = new o.default().main(this.visualModel.state, this.anchor)),
                                this.app.createUI(this.visualModel.state),
                                this.bindEvents(),
                                this.app.init(this.visualModel.state);
                        }),
                        t
                );
            })();
        e.default = l;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    }),
            a =
                (this && this.__assign) ||
                function () {
                    return (a =
                        Object.assign ||
                        function (t) {
                            for (var e, n = 1, r = arguments.length; n < r; n++) for (var i in (e = arguments[n])) Object.prototype.hasOwnProperty.call(e, i) && (t[i] = e[i]);
                            return t;
                        }).apply(this, arguments);
                },
            s =
                (this && this.__importDefault) ||
                function (t) {
                    return t && t.__esModule ? t : { default: t };
                };
        Object.defineProperty(e, "__esModule", { value: !0 });
        var o = s(n(1)),
            l = n(3),
            u = (function (t) {
                function e(e) {
                    var n = t.call(this) || this;
                    return (n.state = l.defaultModel), (n.mapOfHandles = new Map()), (n.edge = 0), n.setState(e), n;
                }
                return (
                    i(e, t),
                        (e.prototype.setState = function (t) {
                            var e = this.correctMinMax({ min: t.min, max: t.max }),
                                n = e.min,
                                r = e.max,
                                i = this.correctStep({ step: t.step, min: n, max: r }),
                                s = this.correctValues({ values: t.values, min: n, max: r, step: i });
                            this.state = a(a({}, this.state), { min: n, max: r, step: i, values: s });
                        }),
                        (e.prototype.counting = function (t) {
                            this.edge = t.edge || this.edge;
                            var e = this.findViewValue(t),
                                n = this.countPxValueFromValue(e),
                                r = t.target;
                            if (!r) throw new Error("Не был передан target!");
                            this.mapOfHandles.set(r, { value: e, pxValue: n }), void 0 === t.value && (this.state = a(a({}, this.state), this.updateArrayOfValues())), this.notifyAboutPxValueDone({ value: e, pxValue: n, target: r });
                        }),
                        (e.prototype.findViewValue = function (t) {
                            var e = 0;
                            return void 0 !== t.value ? (e = t.value) : void 0 !== t.left && (e = this.countValueFromLeft(t.left)), e;
                        }),
                        (e.prototype.updateArrayOfValues = function () {
                            for (var t = [], e = 0, n = Array.from(this.mapOfHandles.values()); e < n.length; e++) {
                                var r = n[e];
                                t.push(r.value);
                            }
                            return (
                                t.sort(function (t, e) {
                                    return t - e;
                                }),
                                1 === this.mapOfHandles.size && null != this.state.max && (t[1] = this.state.max),
                                    { values: t }
                            );
                        }),
                        (e.prototype.countPxValueFromValue = function (t) {
                            var e = this.state;
                            return (t - e.min) * (this.getRatio() / e.step);
                        }),
                        (e.prototype.getRatio = function () {
                            var t = this.state,
                                e = (this.edge / (t.max - t.min)) * t.step;
                            return isFinite(e) ? e : 0;
                        }),
                        (e.prototype.createArrayOfPxValues = function () {
                            var t = this;
                            return this.state.values
                                .map(function (e) {
                                    return t.countPxValueFromValue(e);
                                })
                                .sort(function (t, e) {
                                    return t - e;
                                });
                        }),
                        (e.prototype.countValueFromLeft = function (t) {
                            var e = this.state,
                                n = Math.round(t / this.getRatio()) * e.step + e.min;
                            return t >= this.edge ? this.state.max : this.correctValueInTheRange(n);
                        }),
                        (e.prototype.notifyAboutPxValueDone = function (t) {
                            this.emit("pxValueDone", { value: t.value, pxValue: t.pxValue, pxValues: this.createArrayOfPxValues(), steps: this.createSteps(), values: this.state.values, target: t.target, edge: this.edge });
                        }),
                        (e.prototype.correctMinMax = function (t) {
                            var e = void 0 === t.max ? this.state.max : t.max,
                                n = void 0 === t.min ? this.state.min : t.min;
                            return n >= e ? { min: e, max: n } : { min: n, max: e };
                        }),
                        (e.prototype.correctStep = function (t) {
                            var e = void 0 === t.step ? this.state.step : t.step,
                                n = t.min,
                                r = t.max,
                                i = Math.abs(r - n) || 1;
                            return e > i ? i : e < 1 ? 1 : e;
                        }),
                        (e.prototype.correctValues = function (t) {
                            var e = this,
                                n = (void 0 === t.values ? this.state.values : t.values)
                                    .map(function (n) {
                                        return e.correctValueInTheRange(n, t);
                                    })
                                    .sort(function (t, e) {
                                        return t - e;
                                    }),
                                r = t.max;
                            return 1 === n.length && n.push(r), n;
                        }),
                        (e.prototype.correctValueInTheRange = function (t, e) {
                            void 0 === e && (e = this.state);
                            var n = e.step,
                                r = e.min,
                                i = e.max,
                                a = r - Math.round(r / n) * n,
                                s = Math.round(t / n) * n + a;
                            return s < r ? r : s > i || t === i ? i : s;
                        }),
                        (e.prototype.createSteps = function () {
                            var t = this,
                                e = this.state,
                                n = e.min,
                                r = e.max,
                                i = e.step,
                                a = (r - n) / i,
                                s = new Set([n, r]);
                            if (a >= 15) for (var o = 0.2 * (r - n), l = n; l <= r; l += o) s.add(this.correctValueInTheRange(l));
                            else for (l = n + i; l <= r; l += i) s.add(l);
                            return Array.from(s)
                                .sort(function (t, e) {
                                    return t - e;
                                })
                                .map(function (e) {
                                    return { value: e, px: t.countPxValueFromValue(e) };
                                });
                        }),
                        e
                );
            })(o.default);
        e.default = u;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    }),
            a =
                (this && this.__assign) ||
                function () {
                    return (a =
                        Object.assign ||
                        function (t) {
                            for (var e, n = 1, r = arguments.length; n < r; n++) for (var i in (e = arguments[n])) Object.prototype.hasOwnProperty.call(e, i) && (t[i] = e[i]);
                            return t;
                        }).apply(this, arguments);
                },
            s =
                (this && this.__importDefault) ||
                function (t) {
                    return t && t.__esModule ? t : { default: t };
                };
        Object.defineProperty(e, "__esModule", { value: !0 });
        var o = s(n(1)),
            l = n(4),
            u = (function (t) {
                function e(e) {
                    var n = t.call(this) || this;
                    return (n.state = l.defaultVisualModel), n.setState(e), n;
                }
                return (
                    i(e, t),
                        (e.prototype.setState = function (t) {
                            (this.state = a(a({}, this.state), t)), this.emit("newVisualModel", this.state);
                        }),
                        e
                );
            })(o.default);
        e.default = u;
    },
    function (t, e, n) {
        "use strict";
        var r =
            (this && this.__importDefault) ||
            function (t) {
                return t && t.__esModule ? t : { default: t };
            };
        Object.defineProperty(e, "__esModule", { value: !0 });
        var i = r(n(12)),
            a = n(22),
            s = n(0),
            o = (function () {
                function t() {}
                return (
                    (t.prototype.main = function (t, e) {
                        var n,
                            r = t.type,
                            o = t.direction;
                        if (r === s.constants.TYPE_SINGLE) n = new a.SingleFactory(o);
                        else {
                            if (r !== s.constants.TYPE_INTERVAL) throw new Error("Error! Unknown " + r + " or " + o);
                            n = new a.IntervalFactory(o);
                        }
                        return new i.default(n, e);
                    }),
                        t
                );
            })();
        e.default = o;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    }),
            a =
                (this && this.__importDefault) ||
                function (t) {
                    return t && t.__esModule ? t : { default: t };
                };
        Object.defineProperty(e, "__esModule", { value: !0 });
        var s = a(n(13)),
            o = a(n(17)),
            l = a(n(1)),
            u = n(0),
            c = (function (t) {
                function e(e, n) {
                    var r = t.call(this) || this;
                    return (r.factory = e), (r.anchor = n), (r.UIs = {}), (r.template = new o.default()), (r.closestHandle = null), r;
                }
                return (
                    i(e, t),
                        (e.prototype.createUI = function (t) {
                            var e = t.bar,
                                n = t.scale,
                                r = t.settings;
                            this.template.init(t, this.anchor),
                                (this.UIs.handle = this.factory.createHandle(this.anchor)),
                            e && (this.UIs.bar = this.factory.createBar(this.anchor)),
                            n && (this.UIs.scale = this.factory.createScale(this.anchor)),
                            r && (this.UIs.settings = new s.default(this.anchor));
                        }),
                        (e.prototype.init = function (t) {
                            t.tip && this.UIs.handle && ((this.UIs.tip = this.factory.createTip()), this.UIs.handle.append(this.UIs.tip));
                            var e = this.getEdge(t),
                                n = this.anchor.querySelectorAll(".slider__handle"),
                                r = this.anchor.querySelector(".wrapper-slider");
                            if (!r) throw new Error(".wrapper-slider - не было найдено!");
                            this.bindEventListeners({ wrapper: r, state: t }), this.emit("finishInit", { handles: n, edge: e });
                        }),
                        (e.prototype.paint = function (t) {
                            for (var e = 0, n = Object.entries(this.UIs); e < n.length; e++) {
                                var r = n[e],
                                    i = r[0],
                                    a = r[1];
                                "settings" !== i && a.paint(t);
                            }
                        }),
                        (e.prototype.removeHTML = function () {
                            this.UIs.settings && this.anchor.removeChild(this.UIs.settings.wrapper), this.template.wrapper && this.anchor.removeChild(this.template.wrapper);
                        }),
                        (e.prototype.getEdge = function (t) {
                            var e = this.anchor.querySelector(".wrapper-slider");
                            if (!e) throw new Error(".wrapper-slider - не было найдено!");
                            if (!this.anchor.querySelectorAll(".slider__handle")) throw new Error(".slider__handle - не было найдено!");
                            return t.direction === u.constants.DIRECTION_VERTICAL ? e.clientHeight : e.offsetWidth;
                        }),
                        (e.prototype.bindEventListeners = function (t) {
                            t.wrapper.addEventListener("mousedown", this.handleStartMove.bind(this, t));
                        }),
                        (e.prototype.handleStartMove = function (t, e) {
                            var n = this;
                            e.preventDefault();
                            var r = e.target;
                            if (r.className.match(/(slider__handle|slider__tip|slider__bar)/g)) {
                                if ((r.className.includes("slider__tip") && (r = r.parentElement), !r)) throw new Error("event.target - не найден!");
                                var i = { shiftX: e.offsetX, shiftY: r.offsetHeight - e.offsetY, target: r, data: t },
                                    a = this.handleMouseMove.bind(this, i);
                                document.addEventListener("mousemove", a), document.addEventListener("mousedown", a);
                                var s = function () {
                                    (n.closestHandle = null), document.removeEventListener("mousemove", a), document.removeEventListener("mousedown", a), document.removeEventListener("mouseup", s);
                                };
                                document.addEventListener("mouseup", s);
                            }
                        }),
                        (e.prototype.handleMouseMove = function (t, e) {
                            var n,
                                r = t.target,
                                i = r.className.includes("slider__bar"),
                                a = i ? 0 : 0.5 * t.shiftY,
                                s = i ? 0 : 0.5 * t.shiftX,
                                o = t.data;
                            if (((n = o.state.direction === u.constants.DIRECTION_VERTICAL ? o.wrapper.offsetHeight - e.clientY - a + o.wrapper.getBoundingClientRect().top : e.clientX - s - o.wrapper.offsetLeft), i && !this.closestHandle)) {
                                var l = Array.from(o.wrapper.querySelectorAll(".slider__handle")).map(function (t) {
                                    return { value: o.state.direction === u.constants.DIRECTION_VERTICAL ? parseInt(t.style.bottom || "") : parseInt(t.style.left || ""), handle: t };
                                });
                                if (2 === l.length) {
                                    var c = [Math.abs(l[0].value - n), Math.abs(l[1].value - n)].map(function (t, e, n) {
                                        return t <= n[e + 1] ? e : e + 1;
                                    });
                                    this.closestHandle = l[c[0]].handle;
                                } else this.closestHandle = l[0].handle;
                            }
                            this.emit("onUserMove", { left: n, target: i ? this.closestHandle : r });
                        }),
                        e
                );
            })(l.default);
        e.default = c;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    }),
            a =
                (this && this.__assign) ||
                function () {
                    return (a =
                        Object.assign ||
                        function (t) {
                            for (var e, n = 1, r = arguments.length; n < r; n++) for (var i in (e = arguments[n])) Object.prototype.hasOwnProperty.call(e, i) && (t[i] = e[i]);
                            return t;
                        }).apply(this, arguments);
                },
            s =
                (this && this.__importDefault) ||
                function (t) {
                    return t && t.__esModule ? t : { default: t };
                };
        Object.defineProperty(e, "__esModule", { value: !0 }), n(14);
        var o = n(16),
            l = s(n(1)),
            u = n(0),
            c = n(3),
            p = n(4),
            d = (function (t) {
                function e(e) {
                    var n = t.call(this) || this;
                    return (
                        (n.anchor = e),
                            (n.state = {}),
                            (n.model = c.defaultModel),
                            (n.visualModel = p.defaultVisualModel),
                            (n.handleChangeSettings = n.handleChangeSettings.bind(n)),
                            e.insertAdjacentHTML("beforeend", o.settingsTemplate),
                            (n.wrapper = e.querySelector(".settings")),
                            n.wrapper.addEventListener("change", n.handleChangeSettings),
                            n
                    );
                }
                return (
                    i(e, t),
                        (e.prototype.setState = function (t, e) {
                            return (this.model = a(a({}, this.model), t)), (this.visualModel = a(a({}, this.visualModel), e)), this.paint(), this;
                        }),
                        (e.prototype.paint = function () {
                            var t = this.model,
                                e = this.visualModel,
                                n = this.wrapper.elements,
                                r = t.values,
                                i = r[0],
                                s = r[1];
                            t = a(a({}, t), { valueFrom: i, valueTo: s });
                            for (var o = 0, l = Array.from(n); o < l.length; o++) {
                                var c = l[o];
                                (c.value = t[c.name]), "valueFrom" === c.name && ((c.step = t.step), (c.min = t.min), (c.max = t.max)), "valueTo" === c.name && ((c.step = t.step), (c.min = t.min), (c.max = t.max));
                            }
                            for (var p = this.wrapper.querySelectorAll("select"), d = 0, f = Array.from(p); d < f.length; d++) {
                                var h = f[d];
                                h.value = e[h.name].toString();
                            }
                            if (e.type === u.constants.TYPE_SINGLE) {
                                var v = this.wrapper.valueTo;
                                if (v && "true" === v.getAttribute("disabled")) return;
                                v && v.setAttribute("disabled", "true");
                            }
                        }),
                        (e.prototype.handleChangeSettings = function (t) {
                            var e,
                                n,
                                r = t.target;
                            if ("INPUT" === r.tagName) {
                                var i = this.anchor.querySelectorAll(".slider__handle");
                                if ("valueFrom" === r.name || "valueTo" === r.name) {
                                    var a = Number(this.wrapper.valueFrom.value),
                                        s = Number(this.wrapper.valueTo.value);
                                    this.emit("newSettings", { handles: i, edge: this.state.edge, values: [a, s] });
                                } else this.emit("newSettings", (((e = { handles: i, edge: this.state.edge })[r.name] = Number(r.value)), e));
                            } else if ("SELECT" === r.tagName) {
                                var o = r,
                                    l = void 0;
                                (l = "true" === o.value || "false" === o.value ? JSON.parse(o.value) : o.value), this.emit("reCreateApp", (((n = {})[r.name] = l), n));
                            }
                        }),
                        e
                );
            })(l.default);
        e.default = d;
    },
    function (t, e, n) {
        var r = n(2),
            i = n(15);
        "string" == typeof (i = i.__esModule ? i.default : i) && (i = [[t.i, i, ""]]);
        var a = { insert: "head", singleton: !1 },
            s = (r(i, a), i.locals ? i.locals : {});
        t.exports = s;
    },
    function (t, e, n) {},
    function (t, e, n) {
        "use strict";
        Object.defineProperty(e, "__esModule", { value: !0 }),
            (e.settingsTemplate =
                '<form class="settings">\n                                <label class="settings__label"><input name="min" class="settings__input" type="number">\n                                  <b class="settings__option">min</b>\n                                </label>\n                                <label class="settings__label"><input name="max" class="settings__input" type="number">\n                                  <b class="settings__option">max</b>\n                                </label>\n                                <label class="settings__label"><input name="step" class="settings__input" type="number">\n                                  <b class="settings__option">step</b>\n                                </label>\n                                <label class="settings__label"><input name="valueFrom" class="settings__input" type="number">\n                                  <b class="settings__option">valueFrom</b>\n                                </label>\n                                <label class="settings__label"><input name="valueTo" class="settings__input" type="number">\n                                  <b class="settings__option">valueTo</b>\n                                </label>\n                                <div class="settings__separation"></div>\n                                <label class="settings__label">\n                                  <select name="skin">\n                                    <option>green</option>\n                                    <option>red</option>\n                                  </select>\n                                  <b class="settings__option">skin</b>\n                                </label>\n                                <label class="settings__label">\n                                  <select name="direction">\n                                    <option>horizontal</option>\n                                    <option>vertical</option>\n                                  </select>\n                                  <b class="settings__option">direction</b>\n                                </label>\n                                <label class="settings__label">\n                                  <select name="type">\n                                    <option>single</option>\n                                    <option>interval</option>\n                                  </select>\n                                  <b class="settings__option">type</b>\n                                </label>\n                                <label class="settings__label">\n                                  <select name="scale">\n                                    <option>true</option>\n                                    <option>false</option>\n                                  </select>\n                                  <b class="settings__option">scale</b>\n                                </label>\n                                <label class="settings__label">\n                                  <select name="bar">\n                                    <option>true</option>\n                                    <option>false</option>\n                                  </select>\n                                  <b class="settings__option">bar</b>\n                                </label>\n                                <label class="settings__label">\n                                  <select name="tip">\n                                    <option>true</option>\n                                    <option>false</option>\n                                  </select>\n                                  <b class="settings__option">tip</b>\n                                </label>\n                              </form>\n                            ');
    },
    function (t, e, n) {
        "use strict";
        Object.defineProperty(e, "__esModule", { value: !0 }), n(18), n(20);
        var r = (function () {
            function t() {
                this.wrapper = null;
            }
            return (
                (t.prototype.init = function (t, e) {
                    var n = t.skin,
                        r = t.direction,
                        i = '\n      <div class="wrapper-slider wrapper-slider--' + r + '">\n        <div class="slider slider--' + r + " slider--" + n + '">\n          <div class="slider__bar-empty">\n        </div>\n      </div>\n    ';
                    e.insertAdjacentHTML("afterbegin", i), (this.wrapper = e.querySelector(".wrapper-slider"));
                }),
                    t
            );
        })();
        e.default = r;
    },
    function (t, e, n) {
        var r = n(2),
            i = n(19);
        "string" == typeof (i = i.__esModule ? i.default : i) && (i = [[t.i, i, ""]]);
        var a = { insert: "head", singleton: !1 },
            s = (r(i, a), i.locals ? i.locals : {});
        t.exports = s;
    },
    function (t, e, n) {},
    function (t, e, n) {
        var r = n(2),
            i = n(21);
        "string" == typeof (i = i.__esModule ? i.default : i) && (i = [[t.i, i, ""]]);
        var a = { insert: "head", singleton: !1 },
            s = (r(i, a), i.locals ? i.locals : {});
        t.exports = s;
    },
    function (t, e, n) {},
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    });
        Object.defineProperty(e, "__esModule", { value: !0 });
        var a = n(23),
            s = n(24),
            o = n(25),
            l = n(26),
            u = function (t) {
                this.direction = t;
            },
            c = (function (t) {
                function e() {
                    return (null !== t && t.apply(this, arguments)) || this;
                }
                return (
                    i(e, t),
                        (e.prototype.createBar = function (t) {
                            return new s.SingleBar(this.direction, t);
                        }),
                        (e.prototype.createHandle = function (t) {
                            return new o.SingleHandle(this.direction, t);
                        }),
                        (e.prototype.createTip = function () {
                            return new a.SingleTip();
                        }),
                        (e.prototype.createScale = function (t) {
                            return new l.Scale(this.direction, t);
                        }),
                        e
                );
            })(u);
        e.SingleFactory = c;
        var p = (function (t) {
            function e() {
                return (null !== t && t.apply(this, arguments)) || this;
            }
            return (
                i(e, t),
                    (e.prototype.createBar = function (t) {
                        return new s.IntervalBar(this.direction, t);
                    }),
                    (e.prototype.createHandle = function (t) {
                        return new o.IntervalHandle(this.direction, t);
                    }),
                    (e.prototype.createTip = function () {
                        return new a.IntervalTip();
                    }),
                    (e.prototype.createScale = function (t) {
                        return new l.Scale(this.direction, t);
                    }),
                    e
            );
        })(u);
        e.IntervalFactory = p;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    });
        Object.defineProperty(e, "__esModule", { value: !0 });
        var a = (function () {
            function t() {}
            return (
                (t.prototype.init = function (t) {
                    t.insertAdjacentHTML("beforeend", '<div class="slider__tip"><div class="slider__tongue"></div></div>');
                }),
                    t
            );
        })();
        e.Tip = a;
        var s = (function (t) {
            function e() {
                return (null !== t && t.apply(this, arguments)) || this;
            }
            return (
                i(e, t),
                    (e.prototype.paint = function (t) {
                        var e = t.target,
                            n = t.value;
                        if (e) {
                            var r = e.querySelector(".slider__tip");
                            if (!r) throw new Error(".slider__tip - не был найден!");
                            r.setAttribute("data-value", "" + n);
                        }
                    }),
                    e
            );
        })(a);
        e.SingleTip = s;
        var o = (function (t) {
            function e() {
                return (null !== t && t.apply(this, arguments)) || this;
            }
            return (
                i(e, t),
                    (e.prototype.paint = function (t) {
                        var e = t.target,
                            n = t.value,
                            r = t.pxValues,
                            i = t.values;
                        if (e && r) {
                            var a = e.querySelector(".slider__tip");
                            if (!a) throw new Error(".slider__tip - не был найден!");
                            a.setAttribute("data-value", "" + n);
                            var s = e.parentElement && e.parentElement.querySelectorAll(".slider__tip"),
                                o =
                                    s &&
                                    Array.from(s).find(function (t) {
                                        return t !== a;
                                    });
                            if (o) {
                                var l = r[1] - r[0];
                                l <= o.offsetWidth && l <= a.offsetWidth
                                    ? (a.classList.contains("slider__tip--extended") && (a.classList.remove("slider__tip--extended"), o.classList.remove("slider__tip--extended"), (o.style.visibility = "visible")),
                                        (a.style.visibility = "hidden"),
                                        o.classList.add("slider__tip--extended"),
                                        o.setAttribute("data-extendedValue", "" + (i && i.join(" - "))))
                                    : ((a.style.visibility = "visible"), a.classList.remove("slider__tip--extended"), o.classList.remove("slider__tip--extended"), (o.style.visibility = "visible"));
                            }
                        }
                    }),
                    e
            );
        })(a);
        e.IntervalTip = o;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    });
        Object.defineProperty(e, "__esModule", { value: !0 });
        var a = n(0),
            s = function (t, e) {
                this.direction = t;
                var n = e.querySelector(".slider");
                if (!n) throw new Error(".slider - не было найдено!");
                n.insertAdjacentHTML("beforeend", '</div><div class="slider__bar"></div>');
            };
        e.Bar = s;
        var o = (function (t) {
            function e() {
                return (null !== t && t.apply(this, arguments)) || this;
            }
            return (
                i(e, t),
                    (e.prototype.paint = function (t) {
                        var e = t.pxValue,
                            n = t.target;
                        if (void 0 === e || void 0 === n) throw new Error("pxValue или target не был передан!");
                        var r = n.parentElement && n.parentElement.querySelector(".slider__bar");
                        if (!r) throw new Error(".slider__bar - не было найдено!");
                        this.direction === a.constants.DIRECTION_HORIZONTAL ? (r.style.width = e + 10 + "px") : this.direction === a.constants.DIRECTION_VERTICAL && (r.style.height = e + 10 + "px");
                    }),
                    e
            );
        })(s);
        e.SingleBar = o;
        var l = (function (t) {
            function e() {
                return (null !== t && t.apply(this, arguments)) || this;
            }
            return (
                i(e, t),
                    (e.prototype.paint = function (t) {
                        var e = t.pxValues,
                            n = t.target;
                        if (void 0 === e || void 0 === n) throw new Error("pxValues или target не был передан!");
                        var r = n.parentElement && n.parentElement.querySelector(".slider__bar");
                        if (!r) throw new Error(".slider__bar - не было найдено!");
                        this.direction === a.constants.DIRECTION_HORIZONTAL
                            ? ((r.style.left = e[0] + "px"), (r.style.width = e[1] - e[0] + 10 + "px"))
                            : this.direction === a.constants.DIRECTION_VERTICAL && ((r.style.bottom = e[0] + "px"), (r.style.height = e[1] - e[0] + 10 + "px"));
                    }),
                    e
            );
        })(s);
        e.IntervalBar = l;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    });
        Object.defineProperty(e, "__esModule", { value: !0 });
        var a = n(0),
            s = (function () {
                function t(t) {
                    this.anchor = t;
                }
                return (
                    (t.prototype.append = function (t) {
                        for (var e = this.anchor.querySelectorAll(".slider__handle"), n = 0, r = Array.from(e); n < r.length; n++) {
                            var i = r[n];
                            t.init(i);
                        }
                    }),
                        t
                );
            })();
        e.Handle = s;
        var o = (function (t) {
            function e(e, n) {
                var r = t.call(this, n) || this;
                (r.direction = e), (r.anchor = n);
                var i = n.querySelector(".slider");
                if (!i) throw new Error(".slider - не было найдено!");
                return i.insertAdjacentHTML("beforeend", '<div class="slider__handle"></div>'), r;
            }
            return (
                i(e, t),
                    (e.prototype.paint = function (t) {
                        var e = t.target,
                            n = t.pxValue;
                        if (!e) throw new Error("Не был передан target!");
                        this.direction === a.constants.DIRECTION_HORIZONTAL ? (e.style.left = n + "px") : this.direction === a.constants.DIRECTION_VERTICAL && (e.style.bottom = n + "px");
                    }),
                    e
            );
        })(s);
        e.SingleHandle = o;
        var l = (function (t) {
            function e(e, n) {
                var r = t.call(this, n) || this;
                (r.direction = e), (r.anchor = n);
                var i = n.querySelector(".slider");
                if (null === i) throw new Error(".slider - не было найдено!");
                return i.insertAdjacentHTML("beforeend", '<div class="slider__handle"></div>'), i.insertAdjacentHTML("beforeend", '<div class="slider__handle"></div>'), r;
            }
            return (
                i(e, t),
                    (e.prototype.paint = function (t) {
                        var e = t.target,
                            n = t.pxValue;
                        if (!e) throw new Error("Не был передан target!");
                        this.direction === a.constants.DIRECTION_HORIZONTAL ? (e.style.left = n + "px") : this.direction === a.constants.DIRECTION_VERTICAL && (e.style.bottom = n + "px");
                    }),
                    e
            );
        })(s);
        e.IntervalHandle = l;
    },
    function (t, e, n) {
        "use strict";
        var r,
            i =
                (this && this.__extends) ||
                ((r = function (t, e) {
                    return (r =
                        Object.setPrototypeOf ||
                        ({ __proto__: [] } instanceof Array &&
                            function (t, e) {
                                t.__proto__ = e;
                            }) ||
                        function (t, e) {
                            for (var n in e) e.hasOwnProperty(n) && (t[n] = e[n]);
                        })(t, e);
                }),
                    function (t, e) {
                        function n() {
                            this.constructor = t;
                        }
                        r(t, e), (t.prototype = null === e ? Object.create(e) : ((n.prototype = e.prototype), new n()));
                    }),
            a =
                (this && this.__importDefault) ||
                function (t) {
                    return t && t.__esModule ? t : { default: t };
                };
        Object.defineProperty(e, "__esModule", { value: !0 }), n(27);
        var s = a(n(1)),
            o = n(0),
            l = (function (t) {
                function e(e, n) {
                    var r = t.call(this) || this;
                    (r.direction = e), (r.anchor = n), (r.steps = [{ value: 0, px: 0 }]), (r.handleScaleValue = r.handleScaleValue.bind(r)), (r.slider = n.querySelector(".slider"));
                    return r.slider.insertAdjacentHTML("afterbegin", '<div class="slider__scale"><div class="scale"></div></div>'), (r.wrapper = r.anchor.querySelector(".scale")), r.wrapper.addEventListener("click", r.handleScaleValue), r;
                }
                return (
                    i(e, t),
                        (e.prototype.paint = function (t) {
                            var e = t.steps;
                            this.steps = e;
                            var n = e;
                            this.wrapper.innerHTML = "";
                            for (var r = 0; r < n.length; r += 1) {
                                var i = '<div class="scale__value">' + n[r].value + "</div>";
                                this.wrapper.insertAdjacentHTML("beforeend", i);
                                var a = this.wrapper.children[r];
                                switch (this.direction) {
                                    case o.constants.DIRECTION_HORIZONTAL:
                                        a.style.left = n[r].px + "px";
                                        break;
                                    case o.constants.DIRECTION_VERTICAL:
                                        a.style.bottom = n[r].px + "px";
                                }
                            }
                        }),
                        (e.prototype.findClosestHandle = function (t, e) {
                            var n = t.querySelectorAll(".slider__handle"),
                                r = t.querySelectorAll(".slider__tip"),
                                i = Array.from(r).map(function (t) {
                                    return Number(t.dataset.value);
                                });
                            2 === n.length
                                ? (i[
                                    [Math.abs(i[0] - e), Math.abs(i[1] - e)].map(function (t, e, n) {
                                        return t <= n[e + 1] ? e : e + 1;
                                    })[0]
                                    ] = e)
                                : ((i[0] = e), (i[1] = this.steps[this.steps.length - 1].value));
                            return { handles: n, values: i };
                        }),
                        (e.prototype.handleScaleValue = function (t) {
                            var e = t.target;
                            if ("scale__value" === e.className) {
                                var n = Number(e && e.textContent),
                                    r = this.findClosestHandle(this.anchor, n),
                                    i = r.handles,
                                    a = r.values;
                                this.emit("newValueFromScale", { handles: i, values: a });
                            }
                        }),
                        e
                );
            })(s.default);
        e.Scale = l;
    },
    function (t, e, n) {
        var r = n(2),
            i = n(28);
        "string" == typeof (i = i.__esModule ? i.default : i) && (i = [[t.i, i, ""]]);
        var a = { insert: "head", singleton: !1 },
            s = (r(i, a), i.locals ? i.locals : {});
        t.exports = s;
    },
    function (t, e, n) {},
]);

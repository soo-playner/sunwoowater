"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : new P(function (resolve) { resolve(result.value); }).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = y[op[0] & 2 ? "return" : op[0] ? "throw" : "next"]) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [0, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
exports.__esModule = true;
var express_1 = require("express");
var fs_1 = require("fs");
var ResponseHandler_1 = require("./handler/ResponseHandler");
var router = express_1.Router();
exports.router = router;
fs_1["default"].readdir(__dirname + '/route', function (err, filelist) {
    for (var _i = 0, filelist_1 = filelist; _i < filelist_1.length; _i++) {
        var filepath = filelist_1[_i];
        fs_1["default"].readFile(__dirname + '/route/' + filepath, 'utf8', function (err, file) {
            return __awaiter(this, void 0, void 0, function () {
                var _this = this;
                var domain, json, _loop_1, _i, _a, route;
                return __generator(this, function (_b) {
                    domain = '';
                    json = JSON.parse(file);
                    domain = json.domain;
                    _loop_1 = function (route) {
                        if (route.hasOwnProperty('controller') && route.hasOwnProperty('action')) {
                            router[route.method]('/' + domain + route.route, function (req, res, next) { return __awaiter(_this, void 0, void 0, function () {
                                var _this = this;
                                return __generator(this, function (_a) {
                                    (function () { return __awaiter(_this, void 0, void 0, function () {
                                        var _this = this;
                                        var MyClass, controller, result;
                                        return __generator(this, function (_a) {
                                            switch (_a.label) {
                                                case 0: return [4 /*yield*/, Promise.resolve().then(function () { return require('./controller/' + route.controller); })];
                                                case 1:
                                                    MyClass = (_a.sent())["default"];
                                                    controller = new MyClass();
                                                    result = controller[route.action](req, res, next);
                                                    if (!(result instanceof Promise)) return [3 /*break*/, 2];
                                                    result.then(function (data) { return __awaiter(_this, void 0, void 0, function () {
                                                        return __generator(this, function (_a) {
                                                            switch (_a.label) {
                                                                case 0:
                                                                    if (!(data !== null && data !== undefined)) return [3 /*break*/, 2];
                                                                    return [4 /*yield*/, ResponseHandler_1.responseHandler(req, res, data)];
                                                                case 1:
                                                                    _a.sent();
                                                                    _a.label = 2;
                                                                case 2: return [2 /*return*/];
                                                            }
                                                        });
                                                    }); });
                                                    return [3 /*break*/, 4];
                                                case 2:
                                                    if (!(result !== null && result !== undefined)) return [3 /*break*/, 4];
                                                    return [4 /*yield*/, ResponseHandler_1.responseHandler(req, res, result)];
                                                case 3:
                                                    _a.sent();
                                                    _a.label = 4;
                                                case 4: return [2 /*return*/];
                                            }
                                        });
                                    }); })();
                                    return [2 /*return*/];
                                });
                            }); });
                        }
                        else {
                            router[route.method]('/' + domain + route.route, function (req, res, next) { return __awaiter(_this, void 0, void 0, function () {
                                return __generator(this, function (_a) {
                                    switch (_a.label) {
                                        case 0:
                                            if (!route.hasOwnProperty('render')) return [3 /*break*/, 2];
                                            return [4 /*yield*/, ResponseHandler_1.responseHandler(req, res, { render: route.render })];
                                        case 1:
                                            _a.sent();
                                            return [3 /*break*/, 4];
                                        case 2:
                                            if (!route.hasOwnProperty('redirect')) return [3 /*break*/, 4];
                                            return [4 /*yield*/, ResponseHandler_1.responseHandler(req, res, { redirect: route.redirect })];
                                        case 3:
                                            _a.sent();
                                            _a.label = 4;
                                        case 4: return [2 /*return*/];
                                    }
                                });
                            }); });
                        }
                    };
                    for (_i = 0, _a = json.routes; _i < _a.length; _i++) {
                        route = _a[_i];
                        _loop_1(route);
                    }
                    return [2 /*return*/];
                });
            });
        });
    }
});

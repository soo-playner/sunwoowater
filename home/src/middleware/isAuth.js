"use strict";
exports.__esModule = true;
var isAuth = function (req, res, next) {
    console.log('isAuth');
    next();
};
exports["default"] = isAuth;

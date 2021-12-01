"use strict";
exports.__esModule = true;
var isAuth_1 = require("./middleware/isAuth");
exports.middlewares = [
    {
        method: "post",
        route: "/main",
        middleware: [
            isAuth_1["default"],
        ]
    }
];

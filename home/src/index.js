"use strict";
exports.__esModule = true;
var express_1 = require("express");
var morgan_1 = require("morgan");
var path = require("path");
var helmet_1 = require("helmet");
var dotenv_1 = require("dotenv");
var body_parser_1 = require("body-parser");
var express_session_1 = require("express-session");
var passport_1 = require("passport");
var route_1 = require("./route");
var middleware_1 = require("./middleware");
require("./@types/express-session");
var app = express_1["default"]();
dotenv_1["default"].config();
// sequelize.sync({ force: false })
//   .then(() => {
//     console.log('데이터베이스 연결 성공');
//   })
//   .catch((err) => {
//     console.error(err);
//   });
// view engine setup
app.set('views', path.join(__dirname, 'view'));
app.set('view engine', 'ejs');
app.use(express_1["default"].static(path.join(__dirname + '/../assets')));
app.use(body_parser_1["default"].json());
app.use(express_session_1["default"]({ secret: 'sessionKey', resave: true, saveUninitialized: false })); // 세션 활성화
app.use(passport_1["default"].initialize()); // passport 구동
app.use(passport_1["default"].session()); // 세션 연결
//morgan & helmet
if (process.env.NODE_ENV === 'production') {
    app.use(helmet_1["default"]({
        contentSecurityPolicy: false
    }));
    morgan_1["default"].token('xff', function (req, res) { return req.headers['forwarded']; });
    app.use(morgan_1["default"](':xff :remote-addr - :remote-user [:date[clf]] ":method :url HTTP/:http-version" :status :res[content-length] ":referrer" ":user-agent"'));
}
else {
    app.use(morgan_1["default"]('dev'));
}
app.use(express_1["default"].json());
app.use(express_1["default"].urlencoded({ extended: false }));
//middleware
for (var _i = 0, middlewares_1 = middleware_1.middlewares; _i < middlewares_1.length; _i++) {
    var info = middlewares_1[_i];
    for (var _a = 0, _b = info.middleware; _a < _b.length; _a++) {
        var mw = _b[_a];
        app.use(info.route, mw);
    }
}
//route
app.use(route_1.router);
// catch 404 and forward to error handler
app.use(function (req, res, next) {
    res.status(404).json('ERR_NOT_FOUND');
});
// error handler
app.use(function (err, req, res, next) {
    // set locals, only providing error in development
    res.locals.message = err.message;
    res.locals.error = req.app.get('env') === 'development' ? err : {};
    res.status(err.status || 500).json({
        message: err.message || 'ERR_UNKNOWN_ERROR'
    });
});
// server
var options = {
    port: process.env.NODE_PORT || 3000
};
app.listen(options, function () { return console.log("server on!!!" + options.port); });
exports["default"] = app;

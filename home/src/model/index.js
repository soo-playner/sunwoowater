"use strict";
exports.__esModule = true;
var sequelize_typescript_1 = require("sequelize-typescript");
var config_1 = require("../config/config");
exports.sequelize = new sequelize_typescript_1.Sequelize(config_1.dbConfig);
// sequelize.addModels([Board,BoardImage,Post,PostContent,PostLike,Reply,ReplyLike,Report])
// console.log(`[__dirname + '/data/*.ts']   :`, [__dirname + '/data/*.ts']);
exports.sequelize.addModels([__dirname + '/data/*.*']);

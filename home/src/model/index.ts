import { Sequelize } from 'sequelize-typescript';
import { dbConfig as config } from '../config/config';


export const sequelize = new Sequelize(config);
    // sequelize.addModels([Board,BoardImage,Post,PostContent,PostLike,Reply,ReplyLike,Report])
    
// console.log(`[__dirname + '/data/*.ts']   :`, [__dirname + '/data/*.ts']);
sequelize.addModels([__dirname + '/data/*.*']);
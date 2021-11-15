import express, { Request, Response, NextFunction  } from 'express';
import morgan from 'morgan';
import path = require('path');
import helmet from 'helmet';
import dotenv from 'dotenv';
import bodyParser from "body-parser";
import session from 'express-session';
import passport from 'passport';

import { sequelize } from './model/index';
import { router } from './route';
import { middlewares } from './middleware';

import './@types/express-session';


const app = express();

dotenv.config();

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
app.use(express.static(path.join(__dirname + '/../assets')));
app.use(bodyParser.json());
app.use(session({ secret: 'sessionKey', resave: true, saveUninitialized: false })); // 세션 활성화
app.use(passport.initialize()); // passport 구동
app.use(passport.session()); // 세션 연결

//morgan & helmet
if (process.env.NODE_ENV === 'production') {
  app.use(
    helmet({
      contentSecurityPolicy: false
    })
  );
  morgan.token('xff', (req: any, res: any) => req.headers['forwarded']);
  app.use(morgan(':xff :remote-addr - :remote-user [:date[clf]] ":method :url HTTP/:http-version" :status :res[content-length] ":referrer" ":user-agent"'));
} else {
  app.use(morgan('dev'));
}

app.use(express.json());
app.use(express.urlencoded({ extended: false }));

//middleware
for (const info of middlewares) {
  for (const mw of info.middleware) {
    app.use(info.route, mw);
  }
}

//route
app.use(router);

// catch 404 and forward to error handler
app.use((req: Request, res: Response, next: NextFunction) => {
  res.status(404).json('ERR_NOT_FOUND');
});

// error handler
app.use((err: any, req: Request, res: Response, next: NextFunction) => {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};
  res.status(err.status || 500).json({
    message: err.message || 'ERR_UNKNOWN_ERROR'
  });
});

// server
const options = {
  port: process.env.NODE_PORT || 3000
};

app.listen(options, () => console.log(`server on!!!${options.port}`));
export default app;
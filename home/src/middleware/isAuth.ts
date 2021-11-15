import { RequestHandler } from 'express';

const isAuth: RequestHandler = (req, res, next) => {
    console.log('isAuth');
    next();
}

export default isAuth;
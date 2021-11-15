import isAuth from './middleware/isAuth';

export const middlewares = [
    {
        method: "post",
        route: "/main",
        middleware: [
            isAuth,
        ]
    }
]
import { Request, Response } from "express";

export const responseHandler = function (req: Request, res: Response, opts: { render? : any, redirect? : any}) {
    
    //member 세션 주입
    if(req.hasOwnProperty('session') && req.session.hasOwnProperty('member')) {
        (opts as any).member = req.session.member;
    } else {
        (opts as any).member = {};
    }
    
    //post, put 요청 시, 입력된 필드값은 그대로 리턴
    if (['post', 'put'].indexOf(req.method.toLowerCase()) > -1) {
        (opts as any).field = req.body;
    } else {
        (opts as any).field = {};
    }

    //params 리턴
    if(req.params) {
        (opts as any).params = req.params;
    }

    //get query 리턴
    if(req.query) {
        (opts as any).query = req.query;
    }

    //요청 path 리턴
    if(req.path) {
        (opts as any).path = req.path;
    }

    if (req.xhr) {
        res.json(opts);
    } else if (opts.hasOwnProperty('redirect')) {
        if (req.hasOwnProperty('session')) {
            req.session.save(function (err: any) {
                if (err) return console.error(err);
                res.redirect(opts.redirect);
            });
        } else {
            res.redirect(opts.redirect);
        }
    } else if (opts.hasOwnProperty('render')) {
        res.render(opts.render, opts);
    } else {
        res.send(opts);
    }
}
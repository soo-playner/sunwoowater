import { Router, Request, Response, NextFunction } from 'express';
import fs from 'fs';
import { responseHandler } from './handler/ResponseHandler';

const router = Router();

fs.readdir(__dirname + '/route', function (err: any, filelist: string[]) {

    for (const filepath of filelist) {

        fs.readFile(__dirname + '/route/' + filepath, 'utf8', async function (err: any, file: string) {
            let domain = '';

            const json = JSON.parse(file);

            domain = json.domain;

            for (const route of json.routes) {

                if (route.hasOwnProperty('controller') && route.hasOwnProperty('action')) {
                    (router as any)[route.method]('/' + domain + route.route, async (req: Request, res: Response, next: NextFunction) => {
                        (async () => {
                            const { default: MyClass } = await import('./controller/' + route.controller);
                            const controller = new MyClass();
                            const result = controller[route.action](req, res, next);

                            if (result instanceof Promise) {
                                result.then(async data => {
                                    if (data !== null && data !== undefined) {
                                        await responseHandler(req, res, data);
                                    }
                                });
                            } else if (result !== null && result !== undefined) {
                                await responseHandler(req, res, result);
                            }
                        })()
                    });
                } else {
                    (router as any)[route.method]('/' + domain + route.route, async (req: Request, res: Response, next: NextFunction) => {
                        if (route.hasOwnProperty('render')) {
                            await responseHandler(req, res, { render: route.render });
                        } else if (route.hasOwnProperty('redirect')) {
                            await responseHandler(req, res, { redirect: route.redirect });
                        }
                    });
                }
            }
        });
    }
});

export { router }

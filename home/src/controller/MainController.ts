import { NextFunction, Request, Response } from "express";
import { MainService } from "../service/MainService";
import Joi from "joi";
import { sendMail } from "../helper/helper";
export default class MainController {
    private mainService = new MainService();
    
    async renderMain(req: Request, res: Response, next: NextFunction) {
        console.log('MainController')
        return { render: 'index' };
    }

    async createUser(req: Request, res: Response, next: NextFunction) {
        const schema = Joi.object().keys({
            email: Joi.string().required(),
            password: Joi.string().required(),
            name: Joi.string().required()
        })
            .unknown();
        try {
            await schema.validateAsync(req.body);
            await this.mainService.createUser(
                req.body.email,
                req.body.password,
                req.body.name
            )
            return { message: "등록이 완료되었습니다."};
        } catch (error) {
            console.error(error);
            next(error);
        }
    }

    async sendMail(req : Request, res: Response, next: NextFunction){
        try{
            const mailInfo = await sendMail(req.body);
            console.log("=============mailinfo================");
            console.log(mailInfo);
            return {msg : "sucess", render: "success_inquiry"};
        }catch(e){
            next(e);
        }
    }

}
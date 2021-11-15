import { sequelize as sequelizeModel } from "../model/index";
import sequelize from "sequelize";
import { User } from "../model/data/User";
const Op = sequelize.Op;

export class MainService {
    public async createUser(email: string, password: string, name: string): Promise<void> {
        await User.create({
            email : email,
            password : password,
            name : name
        })
    }
}
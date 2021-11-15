export async function sendMail(param : any){
    const nodemailer = require('nodemailer');
    const ejs = require('ejs');

    try{
console.log(__dirname);
        let emailTemplete2 = await ejs.renderFile(__dirname+'/../view/formmail.ejs', {data : param});

        let transporter = await nodemailer.createTransport({
            service : 'Naver',
            host : 'smtp.naver.com',
            port : 587,
            auth : {
                user : process.env.MAIL_EMAIL,
                pass : process.env.MAIL_PASSWORD
            }
        });
    
        let info = await transporter.sendMail({
            from : `"1TERAPOWER" <${process.env.MAIL_EMAIL}>`,
            to : 'lucy3897@naver.com',
            subject : '[1TERAPOWER] 문의메일',
            html : emailTemplete2
        });
        return info;
    } catch(e){
        console.log(e)
    }
}
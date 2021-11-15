import multer from 'multer';
import path = require('path');
import { RequestHandler } from 'express';

const upload =
multer({dest:__dirname+'/../assets/file/uploads/'}).any()
export default upload;





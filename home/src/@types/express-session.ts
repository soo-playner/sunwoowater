import 'express-session';

export {};

declare module 'express-session' {
    interface SessionData {
        member: any;
    }
}
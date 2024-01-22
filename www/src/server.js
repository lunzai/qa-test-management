import sirv from 'sirv';
import polka from 'polka';
import compression from 'compression';
import * as sapper from '@sapper/server';
import session from 'express-session';
import sessionFileStore from 'session-file-store'
import bodyParser from 'body-parser';
import { nanoid } from 'nanoid'
import {
    SESSION_SECRET, 
    COOKIE_MAX_AGE
} from '@app/configs';
const FileStore = sessionFileStore(session);

const { PORT, NODE_ENV } = process.env;
const dev = NODE_ENV === 'development';

polka() // You can also use Express
    .use(
		bodyParser.json(),
		session({
			secret: SESSION_SECRET,
			resave: true,
			saveUninitialized: true,
            rolling: true,            
            unset: 'destroy',
			cookie: {
                maxAge: parseInt(COOKIE_MAX_AGE, 10) * 1000,
                httpOnly: true,
			},
			store: new FileStore({
                path: '.sessions',
                retries: 5,
                // logFn: false,
                ttl: parseInt(COOKIE_MAX_AGE, 10)
            }),
            genid: () => 'qa-' + nanoid(8),
        }),
		compression({ threshold: 0 }),
        sirv('static', { dev }),
        (req, res, next) => {
            return sapper.middleware({
                session: () => {
                    return {
                        user: req.session.user || null,
                        token: req.session.token || null,
                        isGuest: !req.session.user || !req.session.token
                    }
                }
            })(req, res, next);
        },     
    )
	.listen(PORT, err => {
		if (err) console.error('error', err);
	});

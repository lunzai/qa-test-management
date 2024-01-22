import * as UserApi from '@app/api/user'

export async function get(req, res) {
    const { user, token } = req.session;
    if (user && token) {
        try {
            const response = await UserApi.logout(token);
            req.session = null;
        } catch (error) {
            res.end(JSON.stringify(error))
        }
    }
    res.end(JSON.stringify('OK'));
}

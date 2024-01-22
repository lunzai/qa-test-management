import * as UserApi from '@app/api/user'

export async function post(req, res) {
    const { id, currentPassword, newPassword } = req.body
    const { token } = req.session;
    try {
        const response = await UserApi.changePassword(id, currentPassword, newPassword, token);
        res.end(JSON.stringify(response));
    } catch (error) {
        res.statusCode = error.status;
        res.end(JSON.stringify(error));
    }
}

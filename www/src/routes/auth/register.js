import * as UserApi from '@app/api/user'

export async function post(req, res) {
    const { email, password, job_role, display_name } = req.body
    try {
        const response = await UserApi.register(email, password, job_role, display_name);
        if (response.success) {
            const { user, token } = response.data;
            req.session.user = user;
            req.session.token = token;
        }
        res.end(JSON.stringify(response));
    } catch (error) {
        res.statusCode = error.status || 500;
        res.end(JSON.stringify(error));
    }
}

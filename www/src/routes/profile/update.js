import * as UserApi from '@app/api/user'

export async function post(req, res) {
    const { id, display_name, job_role, email } = req.body
    const { token } = req.session;
    try {
        const response = await UserApi.update(id, { display_name, job_role, email }, token);
        if (response.success) {
            req.session.user = response.data;
        }
        res.end(JSON.stringify(response));
    } catch (error) {
        res.statusCode = error.status;
        res.end(JSON.stringify(error));
    }
}

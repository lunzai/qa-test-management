from locust import FastHttpUser, task, between
import time

# locust -H https://api.qa.lunzai.com -u 1 -r 1 

def login(self):
    loginData = {
        'email': 'hl@whitecoat.global',
        'password': 'heanluen'
    }
    with self.client.post("/user/auth", loginData, catch_response = True) as response:
        data = response.json()
        if data['success'] is not True:
            response.failure(data['status'] + ': ' + data['message'])
            return False
        return data['data']

class WebsiteUser(FastHttpUser):
    # task_set = UserBehavior
    wait_time = between(3, 10)
    token = False
    user = {}
    headers = {}

    @task(1)
    def profile(self):
        with self.client.get(f"/user/{self.user['id']}", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])

    @task(2)
    def test_group(self):
        with self.client.get("/group", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        # with self.client.get("/group?name=s", headers = self.headers, catch_response = True) as response:
        #     data = response.json()
        #     if data['success'] is not True:
        #         response.failure(data['message'])
        with self.client.get("/group?status=Active", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        with self.client.get("/group/1", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])

    @task(2)
    def test_issue(self):
        with self.client.get("/issue", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        with self.client.get("/issue?name=s", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        # with self.client.get("/issue?status=Active", headers = self.headers, catch_response = True) as response:
        #     data = response.json()
        #     if data['success'] is not True:
        #         response.failure(data['message'])
        with self.client.get("/issue/1", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])

    @task(2)
    def test_case(self):
        with self.client.get("/test-case", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        # with self.client.get("/test-case?name=s", headers = self.headers, catch_response = True) as response:
        #     data = response.json()
        #     if data['success'] is not True:
        #         response.failure(data['message'])
        with self.client.get("/test-case?status=Active", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        with self.client.get("/test-case/1", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])

    @task(2)
    def test_result(self):
        with self.client.get("/test-result", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        with self.client.get("/test-result?status=Active", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        with self.client.get("/test-result/1", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])

    @task(1)
    def test_result(self):
        with self.client.get("/user", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])
        with self.client.get("/user?status=Active", headers = self.headers, catch_response = True) as response:
            data = response.json()
            if data['success'] is not True:
                response.failure(data['message'])

    def on_start(self):
        if not self.token:
            response = login(self)
            self.token = response['token']
            self.user = response['user']
            self.headers = {
                'Authorization': f"Bearer {response['token']}"
            }
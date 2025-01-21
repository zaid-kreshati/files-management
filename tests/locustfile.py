import re
from locust import HttpUser, task, between


class LaravelTestUser(HttpUser):
    wait_time = between(5, 9)  # Simulate real user wait times between actions
    host = "http://localhost:8000"  # Replace with your app's URL

    def on_start(self):
        """
        Runs when a simulated user starts. Logs in the user.
        """
        self.token = None
        self.csrf_token = None
        self.headers = {}
        self.login()

    def login(self):
        """
        Simulate a user login and store session/cookies.
        """
        response = self.client.get("/")
        if response.status_code != 200:
            print("Failed to fetch homepage.")
            return

        # Extract CSRF token
        csrftoken_match = re.search(r'meta name="csrf-token" content="(.+?)"', response.text)
        if csrftoken_match:
            csrftoken = csrftoken_match.group(1)
            print("CSRF Token: ", csrftoken)
            self.csrf_token = csrftoken
        else:
            print("Failed to retrieve CSRF token.")
            return

        # Login data
        post_data = {
            'email': "user1@example.com",
            'password': 'password',
            '_token': self.csrf_token
        }

        # Login request
        login_response = self.client.post('/loginApi', data=post_data)
        if login_response.status_code == 200:
            print("Login successful.")
            # Extract token or session information
            self.token = login_response.json().get("token")
            print(self.token)
            self.headers = {
                "Authorization": f"Bearer {self.token}"
            }
        else:
            print(f"Login failed: {login_response.status_code}, Response: {login_response.text}")

    @task
    def fetch_files(self):
        """
        Simulates fetching files for a specific group.
        """
        if not self.token:
            print("User is not logged in. Skipping fetch_files.")
            return

        group_id = 1  # Replace with a valid group ID
        response = self.client.get(f"/files/{group_id}/all", headers=self.headers)
        if response.status_code == 200:
            print("Fetched files successfully.")
        else:
            print(f"Failed to fetch files: {response.status_code}, Response: {response.text}")

    @task
    def upload_file(self):
        """
        Simulates uploading a file to a specific group.
        """
        if not self.token:
            print("User is not logged in. Skipping upload_file.")
            return

        group_id = 1  # Replace with a valid group ID
        file_name = "1.txt"
        file_ids = [1]

        headers = {

            "Authorization": f"Bearer {self.token}",
            "_token": self.csrf_token,
        }

        file_data = {
            "files_names": file_name,
            "group_id": group_id,
            "_token": self.csrf_token,
        }
        response = self.client.post(f"/files/{group_id}/upload", data=file_data, headers=headers)

        check_in_data = {
            "file_ids": file_ids,  # Replace with valid file IDs
            "description": "Check-in test",
            "group_id": group_id,
            "_token": self.csrf_token,
        }
        response2 = self.client.post("/files/check-in", data=check_in_data, headers=headers)

        check_out_data = {
            "files_names": file_name,
            "group_id": group_id,
            "_token": self.csrf_token,
        }
        response3 = self.client.post(f"/files/check-out/{group_id}/file", data=check_out_data, headers=headers)


        if response.status_code == 200:
            print("File uploaded successfully:")
        else:
            print(f"Failed to upload file: {response.status_code}, Response: {response.text}")


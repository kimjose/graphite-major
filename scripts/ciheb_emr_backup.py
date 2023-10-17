import requests
import os
import json

# set the app credentials and SharePoint site URL
app_id = "391e55cd-5287-4e23-9c8d-4d6917944d12" #"your-app-id-here" client_id   
app_secret = "WwN8Q~4jiHGyXqfmTwYRWz2LE3JNixEQymFN_bpe" #"your-app-secret-here" secret valueID
site_url = "https://cihebkenyaorg.sharepoint.com/:f:/s/HealthInformatics-SYSTEMBACKUP/SYSTEM%20BACKUP/EVENT%20MANAGER"
#site_url = "https://your-tenant.sharepoint.com/sites/your-site"

# Microsoft Teams API resource URLs
#site_id = "CloudEMRImplementation-EMRBACKUP" #your-site-id"
team_id = "3a3c7c26-e481-4afd-a2d8-77cb56a7b46a" #"your-team-id" =="Group_Id" 
channel_id = "19%3a3F7l4EquT7HCAcLDqNcDoYzEecOpVfuraVuvk-wu0gs1%40thread.tacv2/" #"your-channel-id"
file_path = "C:\\API-EMRBACKUP\\ess_hts_import2.xlsx" # Replace with the path to your file on the Linux server
https://teams.microsoft.com/l/team/19%3a3F7l4EquT7HCAcLDqNcDoYzEecOpVfuraVuvk-wu0gs1%40thread.tacv2/conversations?groupId=3a3c7c26-e481-4afd-a2d8-77cb56a7b46a&tenantId=1c17770e-a269-4517-b296-c71e84196454
https://teams.microsoft.com/l/team/19%3a3F7l4EquT7HCAcLDqNcDoYzEecOpVfuraVuvk-wu0gs1%40thread.tacv2/conversations?groupId=3a3c7c26-e481-4afd-a2d8-77cb56a7b46a&tenantId=1c17770e-a269-4517-b296-c71e84196454
https://teams.microsoft.com/l/channel/19%3a3F7l4EquT7HCAcLDqNcDoYzEecOpVfuraVuvk-wu0gs1%40thread.tacv2/General?groupId=3a3c7c26-e481-4afd-a2d8-77cb56a7b46a&tenantId=1c17770e-a269-4517-b296-c71e84196454
# graph api access_token key
access_token = "eyJ0eXAiOiJKV1QiLCJub25jZSI6Ilo3YU1DaENFQ184c2Rfa01zUTRUdUJsQko1NnZNeFNQOXdoMVpxQkh0eVkiLCJhbGciOiJSUzI1NiIsIng1dCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyIsImtpZCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyJ9.eyJhdWQiOiJodHRwczovL2dyYXBoLm1pY3Jvc29mdC5jb20iLCJpc3MiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC8xYzE3NzcwZS1hMjY5LTQ1MTctYjI5Ni1jNzFlODQxOTY0NTQvIiwiaWF0IjoxNjgwOTQ3MDIwLCJuYmYiOjE2ODA5NDcwMjAsImV4cCI6MTY4MDk1MDkyMCwiYWlvIjoiRTJaZ1lEQTlJbnJqQnU4RUR3c1ZuLzhHNXJNTUFBPT0iLCJhcHBfZGlzcGxheW5hbWUiOiJTWVNURU0gQkFDS1VQIiwiYXBwaWQiOiIzOTFlNTVjZC01Mjg3LTRlMjMtOWM4ZC00ZDY5MTc5NDRkMTIiLCJhcHBpZGFjciI6IjEiLCJpZHAiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC8xYzE3NzcwZS1hMjY5LTQ1MTctYjI5Ni1jNzFlODQxOTY0NTQvIiwiaWR0eXAiOiJhcHAiLCJvaWQiOiI2ZDg4MDYxMy0yOGU2LTRjMGYtOGVhZS0wZTFhYTM2ZWQzNDAiLCJyaCI6IjAuQVZ3QURuY1hIR21pRjBXeWxzY2VoQmxrVkFNQUFBQUFBQUFBd0FBQUFBQUFBQUJjQUFBLiIsInJvbGVzIjpbIkFQSUNvbm5lY3RvcnMuUmVhZFdyaXRlLkFsbCIsIkFjY2Vzc1Jldmlldy5SZWFkV3JpdGUuTWVtYmVyc2hpcCIsIkFjY2Vzc1Jldmlldy5SZWFkV3JpdGUuQWxsIiwiU2l0ZXMuUmVhZC5BbGwiLCJTaXRlcy5SZWFkV3JpdGUuQWxsIiwiU2l0ZXMuTWFuYWdlLkFsbCIsIkZpbGVzLlJlYWRXcml0ZS5BbGwiLCJGaWxlcy5SZWFkLkFsbCIsIkFQSUNvbm5lY3RvcnMuUmVhZC5BbGwiLCJBY2Nlc3NSZXZpZXcuUmVhZC5BbGwiXSwic3ViIjoiNmQ4ODA2MTMtMjhlNi00YzBmLThlYWUtMGUxYWEzNmVkMzQwIiwidGVuYW50X3JlZ2lvbl9zY29wZSI6IkFGIiwidGlkIjoiMWMxNzc3MGUtYTI2OS00NTE3LWIyOTYtYzcxZTg0MTk2NDU0IiwidXRpIjoiVnVWdkNzVGNJRUd4dFdjWFVkMUNBQSIsInZlciI6IjEuMCIsIndpZHMiOlsiMDk5N2ExZDAtMGQxZC00YWNiLWI0MDgtZDVjYTczMTIxZTkwIl0sInhtc190Y2R0IjoxNTg4Njk1ODYwfQ.Woq1804mAAMQNmu6Yhq9PxcSUelxVoEPvCmjQTgza8TDLBFCmNTu-aNBlglQUNtyxwQDixv78moD_LHxCW5hdkyFvWFYeSvo0TXZYSQd2m9q3xBnggPZi1YnasI87v-PKV20jWOwDN8h7QIOepiCMoUVkugYEAjJldP3ejdJQ4McvMB4I6FbAZZqISaqxUOMX75pGfq5h1aXCMHnY1hvIQeqUCL013gsEDuXab-nUOHH1-A4Wi0PLdy-vATanKOBFPzYuY1QNUcwJwZtozPcCQFp8jnfP9qijHndLZy16BY-FEex0iH-lF9XHiro8AjjlFXRkCRDs4_ExOn4BCsKkQ"
tenant_id = "1c17770e-a269-4517-b296-c71e84196454" 

# authenticate with the app using the app ID and password
auth_url = "https://login.microsoftonline.com/{tenant_id}/oauth2/v2.0/token"
#auth_url = "https://login.microsoftonline.com/common/oauth2/v2.0/token"
auth_data = {
    "grant_type": "client_credentials",
    "client_id": app_id,
    "client_secret": app_secret,
    "scope": "https://graph.microsoft.com/.default"
}
auth_response = requests.post(auth_url, data=auth_data)
#auth_token = auth_response.json()["access_token"]

# upload the file to SharePoint using the Graph API
#upload_url = site_url + "/_api/v2.0/drives/{drive-id}/items/{parent-id}:/{file-name}:/content"
upload_url = site_url
upload_headers = {
    "Authorization": "Bearer " + access_token,
#   "Authorization": "Bearer " + auth_token,
    "Content-Type": "application/json"
}
upload_data = {
    "item": {
        "@microsoft.graph.conflictBehavior": "replace"
    }
}
with open(file_path, "rb") as f:
    file_content = f.read()
upload_response = requests.put(upload_url, headers=upload_headers, data=upload_data)

# send a message to the Teams channel using the Graph API
message_url = "https://graph.microsoft.com/v1.0/teams/{team-id}/channels/{channel-id}/messages"
message_headers = {
    "Authorization": "Bearer " + access_token,
    "Content-Type": "application/json"
}
message_data = {
    "body": {
        "content": "A new file has been uploaded: " + upload_response.json()["webUrl"]
    }
}
message_response = requests.post(message_url, headers=message_headers, data=json.dumps(message_data))

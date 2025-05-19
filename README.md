# SFTP PHP

## Getting Started
### set up the environment variables

- OPENSSH_PRIVATE_KEY=""
- SFTP_HOST=""
- SFTP_USER_NAME=""

### List the content of a remote directory
```bash 
docker compose exec sftp_php php /opt/sftp_php/sftp.php --show-dir data
```

### Copy the remote directory to the local-directory "local-dir"
```bash 
docker compose exec sftp_php php /opt/sftp_php/sftp.php --copy-dir data
```
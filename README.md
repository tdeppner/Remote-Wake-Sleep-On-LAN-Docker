# Remote-Wake-Sleep-On-LAN-Docker
A docker image of [sciguy14/Remote-Wake-Sleep-On-LAN-Server](https://github.com/sciguy14/Remote-Wake-Sleep-On-LAN-Server).

Dockerhub: https://hub.docker.com/r/tdeppner/remote-wake-sleep-on-lan-docker

This is a forked and updated spin of RWSOL, keeping pretty closely to ex0nuss' original work, see https://hub.docker.com/r/ex0nuss/remote-wake-sleep-on-lan-docker for the precursor. That earlier work is now older (2+ years) and is no longer maintained, thus this fork.

## What to expect from this fork?

### Goals
1) Have moderately current (3-6 months old at most) docker image.
- If a security issue is found, there'll likely be a quicker update, though I do not expect that to be a significant situation given the placement of and exposure of this type of service.

2) Standard docker image expectations:
- Image as small as reasonable, minimize bloat, etc.
- Rely on docker container idempotence (stopping and _even_ rm-ing a container should alway be safe.)
  - Use -v to mount directories or volumes as warranted.
  - Have backups. (Using mounted directories is often easier to backup than a docker volume.)
- Have an internal healthcheck, and where possible an externally available one.
- No SSL or certbot in the container.
  - Have a traefik container (or your reverse proxy of choice) running which can provide for SSL certs for any docker images that request it.

3) Patches, requests, etc, are welcomed.
- I do not expect (personally) to do many feature changes, this is a reasonably complete tool at this point.
- If there's an upstream change you really like and this image could benefit from a quicker rebuild, feel free to reach out.
- ex0nuss made a fine tool, it has a place in a homelab (or small production shop) with various hardware, it's a shame to see it languish, be subsumed by security concerns, or just be so terribly out of date you're not sure.

## Summary

> The Remote Wake/Sleep-on-LAN Server (RWSOLS) is a simple webapp that runs on Docker to remotely power up any computer via WOL. </br> This is necesarry, since WOL packages (Layer&nbsp;2) cannot be forwarded via a normal VPN (Layer&nbsp;3).

![preview img](https://raw.githubusercontent.com/tdeppner/Remote-Wake-Sleep-On-LAN-Docker/main/IMG_webinterface_preview.png)

**Information:**
- You don't need any additonal software to wake your client via WOL.
- Additional software is needed to sleep/shutdown your client via this webinterface.

## Usage
Here are some example snippets to help you get started creating a container.

### docker compose (recommended)
```YAML

services:
  frontend-rwsols:
    image: tdeppner/remote-wake-sleep-on-lan-docker
    container_name: frontend-rwsols
    restart: unless-stopped
    network_mode: host
    environment:
      - APACHE2_PORT=8080
      - PASSPHRASE=MyPassword
      - RWSOLS_COMPUTER_NAME="Pc1","Pc2"
      - RWSOLS_COMPUTER_MAC="XX:XX:XX:XX:XX:XX","XX:XX:XX:XX:XX:XX"
      - RWSOLS_COMPUTER_IP="192.168.1.45","192.168.1.50"
```

### docker CLI
```
docker run -d \
  --name=frontend-rwsols \
  --network="host" \
  -e APACHE2_PORT=8080 \
  -e PASSPHRASE=MyPassword \
  -e 'RWSOLS_COMPUTER_NAME="Pc1","Pc2"' \
  -e 'RWSOLS_COMPUTER_MAC="XX:XX:XX:XX:XX:XX","XX:XX:XX:XX:XX:XX"' \
  -e 'RWSOLS_COMPUTER_IP="192.168.1.45","192.168.1.50"' \
  --restart unless-stopped \
  tdeppner/remote-wake-sleep-on-lan-docker
```

## Parameters and environment variables
Container images are configured using parameters passed at runtime (such as those above). There is no config file needed.

Parameter / Env var | Optional | Default value | Description
------------ | :-------------: | ------------- | -------------
`network_mode: host` | no | / | The container’s network stack is not isolated from the Docker host. This is necessary to send WOL packages from a container. The port of the webserver is configured via `APACHE2_PORT`.
`APACHE2_PORT` | yes | 8080 | Port of the webinterface.
`PASSPHRASE` | yes | admin | Password of the webinterface. If no password is specified, you don't need a password to wake a PC.
`RWSOLS_COMPUTER_NAME` | no | / | Displaynames for the computers (**array**)<br>(**No spaces supported.** Please use hyphens or underscores)
`RWSOLS_COMPUTER_MAC` | no | / | MAC addresses for the computers (**array**)
`RWSOLS_COMPUTER_IP` | no | / | IP addresses for the computers (**array**)
`RWSOLS_SLEEP_PORT` | yes | 7760 | This is the Port being used by the Windows SleepOnLan Utility to initiate a Sleep State (**not necessary for WOL**)
`RWSOLS_SLEEP_CMD` | yes | suspend | Command to be issued by the windows sleeponlan utility (**not necessary for WOL**)

#### Explanation: Configuring the destination computer name, MAC and IP
To configure the computers, we will use these three environment variables:
- `RWSOLS_COMPUTER_NAME`
- `RWSOLS_COMPUTER_MAC`
- `RWSOLS_COMPUTER_IP`

<br/>

Let's say we want to wake 2 computers with the following configurations:
1. PC1
   - Displayname: PC-of-Mark
   - MAC address: 24:00:dd:5a:21:04
   - IP address: 192.168.1.146
2. PC2
   - Displayname: PC-of-John
   - MAC address: 59:3c:45:3c:30:f6
   - IP address: 192.168.1.177

<br/>

To configure the env vars it's easier to arrange them in a **vertical** table:
><table>
>  <tr>
>    <th><code>RWSOLS_COMPUTER_NAME</code></th>
>    <td>PC-of-Mark</td>
>    <td>PC-of-John</td>
>  </tr>
>  <tr>
>    <th><code>RWSOLS_COMPUTER_MAC</code></th>
>    <td>24:00:dd:5a:21:04</td>
>    <td>59:3c:45:3c:30:f6</td>
>  </tr>
>  <tr>
>    <th><code>RWSOLS_COMPUTER_IP</code></th>
>    <td>192.168.1.146</td>
>    <td>192.168.1.177</td>
>  </tr>
></table>

<br/>

Now you just format the table in an array:
>```
>      - RWSOLS_COMPUTER_NAME="PC-of-Mark","PC-of-John"
>      - RWSOLS_COMPUTER_MAC="24:00:dd:5a:21:04","59:3c:45:3c:30:f6"
>      - RWSOLS_COMPUTER_IP="192.168.1.146","192.168.1.177"
>```
>It's important to use the format as shown: `Env_var="XXX","XXX"`

## Health checking

### Web based (for example using curl or a monitoring tool like uptime-kuma)

The health endpoint is /health, and it should return only the word OK (and a courtesy newline).

**curl \<url:port\>/health**

For example:
```
$ curl localhost:9898/health
OK
```

#### [Uptime-kuma example](https://github.com/louislam/uptime-kuma)
Add New Monitor
Field | Value
----- | -----
Monitor Type | HTTP(s) - Keyword
Friendly Name | RWSOL
URL | https://rwsol.mydomain.example.com/health
Keyword | OK


### From the local shell

```
# docker ps
```

**Healthy example**
```
$ docker ps
CONTAINER ID   IMAGE                                     COMMAND                  CREATED          STATUS                    PORTS                                       NAMES
e6d1bc47fab2   tdeppner/remote-wake-sleep-on-lan-docker   "/entrypoint.sh"         15 minutes ago   Up 15 minutes (healthy)                                               development-rwsols
```

**Unhealthy example**
```
CONTAINER ID   IMAGE                                     COMMAND                  CREATED         STATUS                     PORTS                                       NAMES
9b7639dd3d39   tdeppner/remote-wake-sleep-on-lan-docker   "/entrypoint.sh"         3 minutes ago   Up 3 minutes (unhealthy)                                               development-rwsols
```

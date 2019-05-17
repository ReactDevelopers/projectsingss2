
#Beam App

Steps to setup:
* Install ***node v8.11.3***.
* Install ***mysql v5.7***.
* cd /path/to/the/application.
* Set/change values to the key below in .env.beam according to the configurations

```
NODE_URL=http://localhost:8485/
NODE_DEBUG=true
NODE_PORT=8485
NODE_IP=http://localhost

MAP_KEY=AIzaSyAyOimGOliLEKgVj34amJWL3K5Kyhe1Ec4

DATABASE_NAME=dbname
DATABASE_HOST=localhost
DATABASE_USER=foo
DATABASE_PASS=bar
DATABASE_PORT=3306

SERVER_TIMEZONE=UTC

ADMIN_NAME=beam
ADMIN_PASS=beam
```

* Run below commands sequentially. (Though node_modules already present but is any issues can run "npm install" first)
```
NODE_ENV=beam npm run seed
NODE_ENV=beam DEBUG=beam-app:* npm start
```
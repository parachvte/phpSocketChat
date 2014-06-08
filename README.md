# phpSocketChat


* Author: Ryannnnnnn
* Email: ryannx6@gmail.com)

### 0. Dependance

* Apache
* PHP (compiled with `--enable-sockets` option)
* jQuery and jQuery Cookie plugin
* Pure

Versions I used: 

```
[
    'Apache' => ['2.2.26', '2.4.9'],
    'PHP' => ['5.5.9', '5.6.0beta3', '5.6.0beta4'],
    'jQuery' => '2.2.1',
    'jQuery Cookie Plugin' => '1.4.1',
    'Pure' => '0.5.0'
]
```

### 1. Structures

Directory `lib/` contains some useful common class.

* To implement the socket server, we have classes `SocketServer`, `SocketClient`, `SocketPacket`.

* Upper layer classes `ChatPacket`, `ChatRequest`, `ChatResponse` to define the request and response variables used to communicate between the app and controllers.

* Later, Some instant storage classes `MessagePool`, `MessageCache`, `UserPool` and `UserCache` to temporarily save the message, channel and user information. They can be rewrite to fit the needs when a MySQL or Redis database is required.

Directory `server/` contains controllers and `app.php`, we route a `ChatRequest` to its controllers and ended returning a `ChatResponse` and send back to the client through socket server.

Directory `client/` contains the HTML, CSS, JS, images resources that can be reached by user. A script `client.php` put there to allow user to send `SocketResquest` to the socket server.


### 2. Get Started

##### 2.1. (Optional) Configure a virtual host for this demo.

Uncomment the line `Include conf/extra/httpd-vhosts.conf` in `path/to/apache/conf/`.

And make a virtual host in `conf/extra/httpd-vhosts.conf` like this:

```
<VirtualHost hostname:80>
    ServerAdmin admin@xxxmail.com
    DocumentRoot "/path/to/phpSocketChat/client"
    ServerName hostname.com
    ServerAlias www.hostname.com
    ErrorLog "logs/hostname-error_log"
    CustomLog "logs/hostname-access_log" common
</VirtualHost>
```

Don't forget to give the `DocumentRoot` proper permissions.

##### 2.2. (Optional) Modify `chat.js`

Modify the line `var host = 'http://127.0.0.1';` to `var host = 'yourhostname';`。

##### 2.3. To run the socket server.

Go to the `server/` directory and run `php app.php`. These will start a UDP socket server on port 2333. The port is internal that called by `client.php` only.

### 3. Some trivial details

##### 3.1. Recombination

When implementing the `SocketPacket.php`, considered that the size of the text may be very large, I tried to divide the text to several parts and add an IP-header like header to each part and send them separately. After socket server receive all parts, we can sort and recombine them and build a `ChatPacket` object.

##### 3.2. Features supported

* List all existing channels
* Create a new channel
* Delete a existing channel
* Switch between channels
* List users in current channel
* Send a broadcast message in a channel
* Polling messages in a channel

### 4. License (GNU AGPLv3)

Copyright (C) 2014 Ryannnnnnn

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.







  





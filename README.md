# Play with React PHP

Run server to the socket localhost:8080:

```zsh
php run.php
```

Now the server listens the previous socket so we can start new connections:

* First connection:
```zsh
telnet localhost:8080
```

* Second connection
```zsh
telnet localhost:8080
```

Then, you'll be able to send messages from one connection to the other.
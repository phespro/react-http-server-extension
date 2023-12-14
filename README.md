# react-http-server-extension
HTTP-Server implementation for Phespro built on top of ReactHTTP Server

## Dependencies

- ext-pcntl
- Tested on Linux only. Other OS may work though.

## Warning

ReactPHP is based on the reactor pattern. This means, that you need to write your application in a different style. You
can use any library which uses reactor pattern inside of this server, as long as it's compatible with revolt event loop
(https://revolt.run/).

You need to make sure, that your application does not do blocking IO. Instead use libraries to do non-blocking IO.

## Setup

Load extension:

`phespro/react-http-server-extension`

Start Server:

```
php artisan react:start-server --host=http://0.0.0.0 --workerAmount=5
```

You can also decorate the service `\Phespro\ReactHttpServerExtension\Config` to add more configuration.

## Further reading and optimization

See official documentation: https://reactphp.org/http/#httpserver
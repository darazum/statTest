<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\SwooleRequestBridge;

$app = require __DIR__ . '/bootstrap.php';

$server = new Server("0.0.0.0", 9501);

$server->set([
    'worker_num' => 16,
    'max_conn' => 5000,
    'max_request' => 30000,
    'log_file' => '/var/log/swoole.log',
    'log_level' => SWOOLE_LOG_DEBUG
]);

register_shutdown_function(function () {
    error_log("[SHUTDOWN] PHP shutdown triggered (fatal error or SIGTERM)");
});

$server->on("start", function () {
    echo "Swoole HTTP server started at http://0.0.0.0:9501, build time: " .
        (file_exists('/build_time.txt') ? file_get_contents('/build_time.txt') : 'unknown') . "\n";
});

$server->on("WorkerError", function ($serv, $workerId, $workerPid, $exitCode, $signal) {
    error_log("Worker $workerId ($workerPid) exited with code $exitCode, signal $signal");
});

$server->on("request", function (Request $req, Response $res) use ($app, $server) {
    try {
        $t = microtime(true);
        $psrRequest = SwooleRequestBridge::fromSwoole($req);
        $psrResponse = $app->handle($psrRequest);

        $res->status($psrResponse->getStatusCode());

        foreach ($psrResponse->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $res->header($name, $value);
            }
        }
        $responseStr = (string)$psrResponse->getBody();


        $swooleTime = microtime(true) - $t;
        if (mt_rand(1, 1000) == 1) {
            $stat = $server->stats();
            $statStr = ' conn:' . $stat['connection_num'];
            $statStr .= ', worker:' . $stat['worker_num'];
            $statStr .= ', request_count:' . $stat['worker_request_count'];
            $statStr .= ', accept:' . $stat['accept_count'];
            $statStr .= ', close:' . $stat['close_count'];
            $statStr .= ', abort:' . $stat['abort_count'];
            $statStr .= ', abort:' . $stat['abort_count'];
            error_log(date('Y-m-d H:i:s') . 'SWOOLE request time: ' . round(microtime(true) - $t, 4) . $statStr);
        }

        if ($swooleTime > 0.02) {
            error_log('SWOOLE SLOW response: ' . $swooleTime);
        }

        $res->end($responseStr);
    } catch (\Throwable $e) {
        error_log('[ERROR] ' . $e->getMessage());
        $res->status(500);
        $res->end("Internal Server Error");
    }
});

$server->start();
<?php
namespace Core;

class ErrorHandler {
    public static function handleException($exception) {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Application Error</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background: #f8d7da; color: #721c24; padding: 50px; }
                .error-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="error-container">
                    <h1 class="display-4 text-danger">Oops! Something went wrong.</h1>
                    <p class="lead">An unexpected error occurred in the application.</p>
                    <hr>
                    <h4>Error Details:</h4>
                    <p><strong>Message:</strong> <?php echo $exception->getMessage(); ?></p>
                    <p><strong>File:</strong> <?php echo $exception->getFile(); ?> (Line <?php echo $exception->getLine(); ?>)</p>
                    <h5>Stack Trace:</h5>
                    <pre><?php echo $exception->getTraceAsString(); ?></pre>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) return;
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}

<?php

declare(strict_types=1);

namespace Canvas\Exceptions;

use Phalcon\DiInterface;

class Handler implements ExceptionHandlerInterface
{
    /**
     * The container implementation.
     *
     * @var \Phalcon\DiInterface
     */
    protected $container;

    /**
     * A list of the exception types that shouldn't be reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the internal exception types that shouldn't be reported.
     *
     * @var array
     */
    protected $internalDontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
    ];

    /**
     * Create a new exception handler instance.
     *
     * @param \Phalcon\DiInterface  $container
     * @return void
     */
    public function __construct(DiInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return mixed
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }

        if (is_callable($reportCallable = [$exception, 'report'])) {
            return $this->container->call($reportCallable);
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $exception;
        }

        $logger->error(
            $exception->getMessage(),
            array_merge($this->context(), ['exception' => $exception]
        ));
    }


    /**
     * Determine if the exception should be reported.
     *
     * @param  \Exception  $e
     * @return bool
     */
    public function shouldReport(Exception $exception): bool
    {
        return ! $this->shouldntReport($exception);
    }


    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function shouldntReport(Exception $exception)
    {
        $dontReport = array_merge($this->dontReport, $this->internalDontReport);
        return ! is_null(Arr::first($dontReport, function ($type) use ($exception) {
            return $exception instanceof $type;
        }));
    }




}

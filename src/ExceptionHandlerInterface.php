<?php

declare(strict_types=1);

namespace Canvas\Exceptions;

use Exception;

interface ExceptionHandlerInterface
{

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e);

    /**
     * Determine if the exception should be reported.
     *
     * @param  \Exception  $e
     * @return bool
     */
    public function shouldReport(Exception $e);

}

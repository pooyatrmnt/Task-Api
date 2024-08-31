<?php

set_exception_handler('\Ninja\ApiErrorHandler::handleException');
set_error_handler('\Ninja\ApiErrorHandler::handleError');
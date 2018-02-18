<?php

namespace App\Component\User\Event;

class UserEvents
{
    const RESETTING_REQUEST_SUCCESS = 'user.resetting.request.success';
    const RESETTING_RESET_INITIALIZE = 'user.resetting.reset.initialize';
    const RESETTING_RESET_SUCCESS = 'user.resetting.reset.success';
    const RESETTING_RESET_COMPLETED = 'user.resetting.reset.completed';

    const REGISTRATION_SUCCESS = 'user.register.success';
    const REGISTRATION_CONFIRMED = 'user.registration.confirmed';
    
    const CREATE_SUCCESS = 'user.create.success';

    const SECURITY_IMPLICIT_LOGIN = 'user.security.implicit_login';
}
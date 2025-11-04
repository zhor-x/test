<?php

namespace App\Enums;

enum UserRole: string
{

    case STUDENT = 'student';
    case INSTRUCTOR = 'instructor';
    case ADMIN = 'admin';

}

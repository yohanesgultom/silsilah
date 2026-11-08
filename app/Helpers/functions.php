<?php

use App\User;

/**
 * Convert file size to have unit string.
 *
 * @param  int  $bytes
 * @return string
 */
function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2).' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2).' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2).' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes.' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes.' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

/**
 * Get user photo image tag.
 *
 * @param  \App\User  $user
 * @param  array  $attributes
 * @return \Illuminate\Support\HtmlString
 */
function userPhoto(User $user, $attributes = [])
{
    $photoPath = userPhotoPath($user->photo_path, $user->gender_id);
    $img = '<img src="'.$photoPath.'"';
    foreach ($attributes as $key => $value) {
        $img .= ' '.$key.'="'.$value.'"';
    }
    $img .= '>';
    return new \Illuminate\Support\HtmlString($img);
}

/**
 * Get user photo by path. Return default gender icon by default.
 *
 * @param  string  $photoPath
 * @param  int  $genderId
 * @return string
 */
function userPhotoPath($photoPath, $genderId)
{
    if (is_file(public_path('storage/'.$photoPath))) {
        return asset('storage/'.$photoPath);
    }

    return asset('images/icon_user_'.$genderId.'.png');
}

function is_system_admin(User $user)
{
    if (method_exists($user, 'is_system_admin')) {
        return $user->is_system_admin();
    }
    // Fallback for non-App\User instances
    if ($user->email) {
        if (config('app.system_admin_emails')) {
            $adminEmails = explode(';', config('app.system_admin_emails'));
            return in_array($user->email, $adminEmails);
        }
    }

    return false;
}

/**
 * Generate an HTML link to a named route.
 *
 * @param  string  $route
 * @param  string  $text
 * @param  array  $parameters
 * @param  array  $attributes
 * @return \Illuminate\Support\HtmlString
 */
function link_to_route($route, $text, $parameters = [], $attributes = [])
{
    $url = route($route, $parameters);
    $attributesStr = '';
    foreach ($attributes as $key => $value) {
        $attributesStr .= ' '.$key.'="'.e($value).'"';
    }
    return new \Illuminate\Support\HtmlString('<a href="'.$url.'"'.$attributesStr.'>'.e($text).'</a>');
}

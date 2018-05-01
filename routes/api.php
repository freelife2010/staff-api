<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['prefix' => 'auth'], function(Router $api) {
        $api->post('signup', 'App\\Api\\V1\\Controllers\\Authentication\\SignUpController@signUp');
        $api->post('login', 'App\\Api\\V1\\Controllers\\Authentication\\LoginController@login');

        $api->post('recovery', 'App\\Api\\V1\\Controllers\\Authentication\\ForgotPasswordController@sendResetEmail');
        $api->post('reset', 'App\\Api\\V1\\Controllers\\Authentication\\ResetPasswordController@resetPassword');
    });
    $api->post('get_user', 'App\\Api\\V1\\Controllers\\UserController@getUserData');
    $api->post('update_level', 'App\\Api\\V1\\Controllers\\UserController@updateLevel');
    $api->post('get_groups', 'App\\Api\\V1\\Controllers\\ChatController@getGroups');
    $api->post('upload_image', 'App\\Api\\V1\\Controllers\\S3ImageController@uploadImage');

    //for employee
    $api->post('update_profile', 'App\\Api\\V1\\Controllers\\UserController@updateProfile');
    $api->post('get_jobs', 'App\\Api\\V1\\Controllers\\JobController@getJobs');
    $api->post('apply_job', 'App\\Api\\V1\\Controllers\\JobController@applyJob');
    $api->post('get_applied_jobs', 'App\\Api\\V1\\Controllers\\JobController@getAppliedJobs');
    $api->post('is_applied', 'App\\Api\\V1\\Controllers\\JobController@isApplied');

    //for employer
    $api->post('add_address', 'App\\Api\\V1\\Controllers\\AddressController@addAddress');
    $api->post('remove_address', 'App\\Api\\V1\\Controllers\\AddressController@removeAddress');
    $api->post('get_employees', 'App\\Api\\V1\\Controllers\\UserController@getEmployees');
    $api->post('post_job', 'App\\Api\\V1\\Controllers\\JobController@postJob');
    $api->post('get_my_jobs', 'App\\Api\\V1\\Controllers\\JobController@getMyJobs');
    $api->post('get_applicants', 'App\\Api\\V1\\Controllers\\JobController@getApplicants');
    $api->post('update_favorite', 'App\\Api\\V1\\Controllers\\JobController@updateFavorite');
    $api->post('reactivate_job', 'App\\Api\\V1\\Controllers\\JobController@reactivateJob');
    $api->post('delete_job', 'App\\Api\\V1\\Controllers\\JobController@deleteJob');
    $api->post('get_group_by_user', 'App\\Api\\V1\\Controllers\\ChatController@getGroupByUser');

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
            ]);
        });

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);
    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});

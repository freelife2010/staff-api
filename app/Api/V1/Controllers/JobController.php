<?php

namespace App\Api\V1\Controllers;

use App\Application;
use App\Job;
use App\JobSubcategory;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class JobController extends Controller {

    public function getUser($credentials, JWTAuth $JWTAuth) {
        $validator = Validator::make($credentials, [
            'token' => 'required',
        ]);
        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        return $JWTAuth->toUser($credentials['token']);
    }

    //for employer
    public function postJob(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $job = new Job($request->job);
            $job['status'] = 0;//enabled
            $job->save();

            $subCategories = [];
            foreach ($request->subCategories as $item) {
                $subcategory = new JobSubcategory($item);
                $subcategory['job_id'] = $job['id'];
                $subcategory->save();
                array_push($subCategories, $subcategory);
            }
            $job['subcategories'] = $subCategories;

            return response()->json([
                'job' => $job,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function getMyJobs(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $jobs = Job::where('user_id', '=', $user['id'])
                ->where('status', '<', 2)
                ->orderByDesc('created_at')
                ->get();
            $myJobs = [];
            foreach ($jobs as $job) {
                $job['subcategories'] = JobSubcategory::where('job_id', '=', $job['id'])->get();
                $job['applicants'] = Application::where('job_id', '=', $job['id'])
                    ->where('status', '=', 1)->count();
                array_push($myJobs, $job);
            }

            return response()->json([
                'message' => $myJobs,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function getApplicants(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $applicants = Application::join('users','applications.user_id', '=', 'users.id')
                ->join('employees','applications.user_id', '=', 'employees.user_id')
                ->where('applications.job_id', '=', $request->job_id)
                ->where('applications.status', '=', 1)
                ->orderByDesc('applications.applied_at')
                ->get(['users.id as user_id', 'users.avatar_url', 'users.phone_no', 'employees.*', 'applications.*']);

            return response()->json([
                'message' => $applicants,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function updateFavorite(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            Application::where('id', '=', $request->application_id)
                ->update(['like' => $request->like]);
            return response()->json([
                'message' => $request->like,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function reactivateJob(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            Job::where('id', '=', $request->job['id'])
                ->update(array_merge($request->job, ['status' => $request->status]));

            $subCategories = [];
            foreach ($request->subCategories as $item) {
                if (isset($item['id'])) {
                    JobSubcategory::where('id', '=', $item['id'])
                        ->update($item);
                    array_push($subCategories, $item);
                } else {
                    $subcategory = new JobSubcategory($item);
                    $subcategory['job_id'] = $request->job['id'];
                    $subcategory->save();
                    array_push($subCategories, $subcategory);
                }
            }

            return response()->json([
                'message' => $request->status,
                'subcategories' => $subCategories,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function deleteJob(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            Job::where('id', '=', $request->job_id)
                ->update(['status' => 2]);

            return response()->json([
                'message' => "ok",
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    //for employee
    public function getJobs(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $jobs = Job::join('users','jobs.user_id', '=', 'users.id')
                ->join('employers','jobs.user_id', '=', 'employers.user_id')
                ->where('jobs.status', '=', 0)
                ->whereRaw($request->category_id == 0 ? 'true' : 'pow(2, 9 - jobs.category_id) & ' . $request->category_id . ' > 0')
                ->orderByDesc('jobs.created_at')
                ->get(['jobs.*', 'users.avatar_url', 'users.phone_no', 'employers.company_name', 'employers.phone_visible', 'employers.website_url']);
            $myJobs = [];
            foreach ($jobs as $job) {
                $job['job_id'] = $job['id'];
                $job['subcategories'] = JobSubcategory::where('job_id', '=', $job['id'])->get();
                $application = Application::where('job_id', '=', $job['id'])
                    ->where('user_id', '=', $user['id'])
                    ->get()->first();
                if ($application) $job = array_merge($job->toArray(), $application->toArray());
                array_push($myJobs, $job);
            }

            return response()->json([
                'message' => $myJobs,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function applyJob(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            if ($request->id) {
                Application::where('id', '=', $request->id)
                    ->update(['status' => $request->status, 'applied_at' => date('Y-m-d H:i:s')]);
            } else {
                $application = new Application();
                $application['job_id'] = $request->job_id;
                $application['user_id'] = $user['id'];
                $application['applied_at'] = date('Y-m-d H:i:s');
                $application->save();
            }
            $application = Application::where('id', '=', $request->id ? $request->id : $application['id'])->get()->first();
            return response()->json([
                'message' => $application,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function isApplied(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $application = Application::where('job_id', '=', $request->job_id)
                ->where('user_id', '=', $user['id'])
                ->get()->first();
            return response()->json([
                'message' => $application,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }

    public function getAppliedJobs(Request $request, JWTAuth $JWTAuth) {
        $user = $this->getUser($request->only(['token']), $JWTAuth);
        if ($user) {
            $jobs = Application::join('jobs', 'applications.job_id', '=', 'jobs.id')
                ->join('users','jobs.user_id', '=', 'users.id')
                ->join('employers','jobs.user_id', '=', 'employers.user_id')
                ->where('applications.user_id', '=', $user['id'])
                ->where('applications.status', '=', 1)
                ->where('jobs.category_id', $request->category_id > 0 ? '=' : '<>', $request->category_id)
                ->orderByDesc('applications.applied_at')
                ->get(['jobs.*', 'jobs.id as job_id', 'users.avatar_url', 'users.phone_no', 'employers.company_name', 'employers.phone_visible', 'employers.website_url', 'applications.*']);
            $myJobs = [];
            foreach ($jobs as $job) {
                $job['subcategories'] = JobSubcategory::where('job_id', '=', $job['id'])->get();
                array_push($myJobs, $job);
            }

            return response()->json([
                'message' => $myJobs,
            ]);
        } else {
            return response()->json([
                'message' => "ALERT_NO_USER"
            ]);
        }
    }
}

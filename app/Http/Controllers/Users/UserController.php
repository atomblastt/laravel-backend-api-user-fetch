<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Services\Users\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Services\Users\UserDailyRecordService;

class UserController extends Controller
{
    /**
     * Controller Constructor
     */
    public function __construct(
        protected UserService $service = new UserService(),
        protected UserDailyRecordService $serviceDailyRecord = new UserDailyRecordService(),
    ) {
    }

    # index home page
    public function index()
    {
        $users = $this->service->getAll();
        $dailyRecord = $this->serviceDailyRecord->getAll();
        $viewParam = [
            'users' => [
                'title' => 'User List',
                'data' => $users,
            ],
            'daily_records' => [
                'title' => 'Daily User Record List',
                'data' => $dailyRecord,
            ]
        ];
        return view('users.userList', $viewParam);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $id = decrypt($id);
        $response = $this->service->softDeleteById($id);
        if ($response) {
            Session::flash('status', 'success');
            Session::flash('message', 'User data deleted successfully.');
        } else {
            Session::flash('status', 'error');
            Session::flash('message', 'Failed to delete user data.');
        }        
        return true;
    }
}
